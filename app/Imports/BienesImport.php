<?php

namespace App\Imports;

use App\Models\BienNacional;
use App\Models\CategoriaBien;
use App\Models\Sede;
use App\Models\Estado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Auth;

class BienesImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows
{
    protected $service;
    protected $filaActual = 1;

    protected array $codigosEnArchivo = [];

    public function __construct($service = null)
    {
        $this->service = $service;
    }

    public function model(array $row)
    {
        $this->filaActual++;

        if (empty($row['codigo_inventario']) && empty($row['descripcion'])) {
            return null;
        }

        try {
            $user = Auth::user();
            $esAdmin = $user->hasRole('Administrador');

            if (empty($row['codigo_inventario'])) {
                throw new \Exception('El campo "codigo_inventario" es obligatorio');
            }

            if (empty($row['descripcion'])) {
                throw new \Exception('El campo "descripcion" es obligatorio');
            }

            if (empty($row['categoria'])) {
                throw new \Exception('El campo "categoria" es obligatorio');
            }

            if (empty($row['sede'])) {
                throw new \Exception('El campo "sede" es obligatorio');
            }

            $codigoTrim = trim($row['codigo_inventario']);

            if (isset($this->codigosEnArchivo[$codigoTrim])) {
                throw new \Exception(
                    'El código "' . $codigoTrim . '" está DUPLICADO dentro del mismo archivo (ya apareció en la fila ' .
                    $this->codigosEnArchivo[$codigoTrim] . ')'
                );
            }

            if (BienNacional::where('codigo_inventario', $codigoTrim)->exists()) {
                throw new \Exception('El código "' . $codigoTrim . '" ya existe en la base de datos');
            }

            $categoria = CategoriaBien::firstOrCreate(
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

            $bien = new BienNacional([
                'codigo_inventario' => $codigoTrim,
                'categoria_bien_id' => $categoria ? $categoria->id : null,
                'sede_id' => $sede ? $sede->id : ($esAdmin ? null : $user->sede_id),
                'descripcion' => trim($row['descripcion']),
                'marca' => isset($row['marca']) ? trim($row['marca']) : null,
                'modelo' => isset($row['modelo']) ? trim($row['modelo']) : null,
                'serial' => isset($row['serial']) ? trim($row['serial']) : null,
                'color' => isset($row['color']) ? trim($row['color']) : null,
                'material' => isset($row['material']) ? trim($row['material']) : null,
                'estatus' => $this->normalizarEstatus($row['estatus'] ?? null),
                'usuario_asignado_nombre' => isset($row['usuario_asignado']) ? trim($row['usuario_asignado']) : null,
                'usuario_asignado_cedula' => isset($row['cedula_asignado']) ? trim($row['cedula_asignado']) : null,
                'usuario_asignado_cargo' => isset($row['cargo_asignado']) ? trim($row['cargo_asignado']) : null,
                'valor_prudencial' => $valorPrudencial,
                'valor_adquisicion' => $valorAdquisicion,
                'fecha_adquisicion' => $row['fecha_adquisicion'] ?? null,
                'observaciones' => isset($row['observaciones']) ? trim($row['observaciones']) : null,
            ]);

            $this->codigosEnArchivo[$codigoTrim] = $this->filaActual;

            if ($this->service) {
                $this->service->registrarExito();
            }

            return $bien;

        } catch (\Exception $e) {
            if ($this->service) {
                $this->service->registrarError(
                    $this->filaActual,
                    $e->getMessage(),
                    [
                        'codigo' => $row['codigo_inventario'] ?? 'N/A',
                        'descripcion' => $row['descripcion'] ?? 'N/A',
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
            'asignado' => 'Asignado',
            'en mantenimiento' => 'En Mantenimiento',
            'mantenimiento' => 'En Mantenimiento',
            'en revision' => 'En Mantenimiento',
            'en revisión' => 'En Mantenimiento',
            'revision' => 'En Mantenimiento',
            'desincorporado' => 'Desincorporado',
        ];

        return $mapa[$estatus] ?? 'Disponible';
    }

    public function chunkSize(): int
    {
        return 500;
    }
}