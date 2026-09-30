<?php

namespace App\Imports;

use App\Models\Componente;
use App\Models\CategoriaComponente;
use App\Models\Sede;
use App\Models\Estado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Auth;

class ComponentesImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows
{
    protected $service;
    protected $filaActual = 1;

    protected array $serialesEnArchivo = [];

    public function __construct($service = null)
    {
        $this->service = $service;
    }

    public function model(array $row)
    {
        $this->filaActual++;

        if (empty($row['serial_unico']) && empty($row['marca'])) {
            return null;
        }

        try {
            $user = Auth::user();
            $esAdmin = $user->hasRole('Administrador');

            if (empty($row['serial_unico'])) {
                throw new \Exception('El campo "serial_unico" es obligatorio');
            }

            if (empty($row['marca'])) {
                throw new \Exception('El campo "marca" es obligatorio');
            }

            if (empty($row['modelo'])) {
                throw new \Exception('El campo "modelo" es obligatorio');
            }

            if (empty($row['categoria'])) {
                throw new \Exception('El campo "categoria" es obligatorio');
            }

            if (empty($row['sede'])) {
                throw new \Exception('El campo "sede" es obligatorio');
            }

            $serialTrim = strtoupper(trim($row['serial_unico']));

            if (isset($this->serialesEnArchivo[$serialTrim])) {
                throw new \Exception(
                    'El serial "' . $serialTrim . '" está DUPLICADO dentro del mismo archivo (ya apareció en la fila ' .
                    $this->serialesEnArchivo[$serialTrim] . ')'
                );
            }

            if (Componente::where('serial_unico', $serialTrim)->exists()) {
                throw new \Exception('El serial "' . $serialTrim . '" ya existe en la base de datos');
            }

            $categoria = CategoriaComponente::firstOrCreate(
                ['nombre' => trim($row['categoria'])],
                ['descripcion' => 'Creada automáticamente al importar']
            );

            $sede = Sede::where('nombre_sede', trim($row['sede']))->first();

            if (!$sede) {
                $dc = Estado::where('nombre', 'Distrito Capital')->first();
                if ($dc) {
                    $sede = Sede::create([
                        'estado_id' => $dc->id,
                        'nombre_sede' => trim($row['sede']),
                        'direccion' => 'Dirección pendiente',
                        'codigo_postal' => null,
                    ]);
                }
            }

            if (!$esAdmin && $sede && $sede->id !== $user->sede_id) {
                throw new \Exception('No tiene permiso para importar en la sede: ' . $row['sede']);
            }

            $valorPrudencial = $this->parsearValor($row['valor_prudencial'] ?? null);
            $valorAdquisicion = $this->parsearValor($row['valor_adquisicion'] ?? null);

            $componente = new Componente([
                'categoria_componente_id' => $categoria->id,
                'marca' => trim($row['marca']),
                'modelo' => trim($row['modelo']),
                'serial_unico' => $serialTrim,
                'sede_id' => $sede ? $sede->id : ($esAdmin ? null : $user->sede_id),
                'estatus' => $this->normalizarEstatus($row['estatus'] ?? null),
                'valor_prudencial' => $valorPrudencial,
                'valor_adquisicion' => $valorAdquisicion,
                'observaciones' => isset($row['observaciones']) ? trim($row['observaciones']) : null,
            ]);

            $this->serialesEnArchivo[$serialTrim] = $this->filaActual;

            if ($this->service) {
                $this->service->registrarExito();
            }

            return $componente;

        } catch (\Exception $e) {
            if ($this->service) {
                $this->service->registrarError(
                    $this->filaActual,
                    $e->getMessage(),
                    [
                        'serial' => $row['serial_unico'] ?? 'N/A',
                        'marca' => $row['marca'] ?? 'N/A',
                        'modelo' => $row['modelo'] ?? 'N/A',
                    ]
                );
            }

            return null;
        }
    }

    private function parsearValor($valor)
    {
        if ($valor === null || $valor === '') return null;
        if (is_numeric($valor)) return $valor;

        $limpio = str_replace('.', '', (string) $valor);
        $limpio = str_replace(',', '.', $limpio);

        return is_numeric($limpio) ? $limpio : null;
    }

    private function normalizarEstatus($estatus)
    {
        if (empty($estatus)) return 'Disponible';

        $estatus = mb_strtolower(trim($estatus));

        $mapa = [
            'disponible' => 'Disponible',
            'instalado' => 'Instalado',
            'en revision' => 'En Revisión',
            'en revisión' => 'En Revisión',
            'revision' => 'En Revisión',
            'desincorporado' => 'Desincorporado',
        ];

        return $mapa[$estatus] ?? 'Disponible';
    }

    public function chunkSize(): int
    {
        return 500;
    }
}