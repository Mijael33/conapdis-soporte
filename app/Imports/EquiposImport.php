<?php

namespace App\Imports;

use App\Models\Equipo;
use App\Models\TipoEquipo;
use App\Models\Sede;
use App\Models\Estado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Auth;

class EquiposImport implements ToModel, WithHeadingRow, WithChunkReading, SkipsEmptyRows
{
    protected $service;
    protected $filaActual = 1;

    /**
     * Cache temporal para detectar duplicados dentro del mismo Excel.
     */
    protected array $codigosEnArchivo = [];

    public function __construct($service = null)
    {
        $this->service = $service;
    }

    public function model(array $row)
    {
        $this->filaActual++;

        // Saltar filas vacías
        if (empty($row['codigo_inventario']) && empty($row['tipo_equipo'])) {
            return null;
        }

        try {
            $user = Auth::user();
            $esAdmin = $user->hasRole('Administrador');

            /*
            |--------------------------------------------------------------------------
            | VALIDACIONES MANUALES
            |--------------------------------------------------------------------------
            */

            if (empty($row['codigo_inventario'])) {
                throw new \Exception('El campo "codigo_inventario" es obligatorio');
            }

            if (empty($row['tipo_equipo'])) {
                throw new \Exception('El campo "tipo_equipo" es obligatorio');
            }

            if (empty($row['sede'])) {
                throw new \Exception('El campo "sede" es obligatorio');
            }

            if (empty($row['marca'])) {
                throw new \Exception('El campo "marca" es obligatorio');
            }

            if (empty($row['modelo'])) {
                throw new \Exception('El campo "modelo" es obligatorio');
            }

            /*
            |--------------------------------------------------------------------------
            | VERIFICAR DUPLICADOS DENTRO DEL MISMO ARCHIVO
            |--------------------------------------------------------------------------
            */

            $codigoTrim = trim($row['codigo_inventario']);

            if (isset($this->codigosEnArchivo[$codigoTrim])) {
                throw new \Exception(
                    'El código "' . $codigoTrim . '" está DUPLICADO dentro del mismo archivo (ya apareció en la fila ' .
                    $this->codigosEnArchivo[$codigoTrim] . ')'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | VERIFICAR DUPLICADOS CONTRA LA BASE DE DATOS
            |--------------------------------------------------------------------------
            */

            if (Equipo::where('codigo_inventario_institucional', $codigoTrim)->exists()) {
                throw new \Exception('El código "' . $codigoTrim . '" ya existe en la base de datos');
            }

            /*
            |--------------------------------------------------------------------------
            | OBTENER O CREAR TIPO DE EQUIPO
            |--------------------------------------------------------------------------
            */

            $tipoEquipo = TipoEquipo::firstOrCreate(
                ['nombre' => trim($row['tipo_equipo'])],
                ['descripcion' => 'Creado automáticamente al importar']
            );

            /*
            |--------------------------------------------------------------------------
            | OBTENER O CREAR SEDE
            |--------------------------------------------------------------------------
            */

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

            /*
            |--------------------------------------------------------------------------
            | VALIDAR PERMISO POR SEDE
            |--------------------------------------------------------------------------
            */

            if (!$esAdmin && $sede && $sede->id !== $user->sede_id) {
                throw new \Exception('No tiene permiso para importar en la sede: ' . $row['sede']);
            }

            /*
            |--------------------------------------------------------------------------
            | VALIDAR Y NORMALIZAR VALORES
            |--------------------------------------------------------------------------
            */

            $valorPrudencial = $this->parsearValor($row['valor_prudencial'] ?? null);
            $valorAdquisicion = $this->parsearValor($row['valor_adquisicion'] ?? null);

            /*
            |--------------------------------------------------------------------------
            | CREAR EQUIPO
            |--------------------------------------------------------------------------
            */

            $equipo = new Equipo([
                'codigo_inventario_institucional' => $codigoTrim,
                'serial_chasis' => isset($row['serial_chasis']) ? trim($row['serial_chasis']) : null,
                'tipo_equipo_id' => $tipoEquipo->id,
                'sede_id' => $sede ? $sede->id : null,
                'marca' => trim($row['marca']),
                'modelo' => trim($row['modelo']),
                'estatus_general' => $this->normalizarEstatusGeneral($row['estatus_general'] ?? null),
                'usuario_asignado_nombre' => isset($row['usuario_nombre']) ? trim($row['usuario_nombre']) : null,
                'usuario_asignado_cedula' => isset($row['usuario_cedula']) ? trim($row['usuario_cedula']) : null,
                'usuario_asignado_cargo' => isset($row['usuario_cargo']) ? trim($row['usuario_cargo']) : null,
                'valor_prudencial' => $valorPrudencial,
                'valor_adquisicion' => $valorAdquisicion,
            ]);

            // Guardar en cache temporal
            $this->codigosEnArchivo[$codigoTrim] = $this->filaActual;

            if ($this->service) {
                $this->service->registrarExito();
            }

            return $equipo;

        } catch (\Exception $e) {
            if ($this->service) {
                $this->service->registrarError(
                    $this->filaActual,
                    $e->getMessage(),
                    [
                        'codigo' => $row['codigo_inventario'] ?? 'N/A',
                        'tipo' => $row['tipo_equipo'] ?? 'N/A',
                        'marca' => $row['marca'] ?? 'N/A',
                        'modelo' => $row['modelo'] ?? 'N/A',
                    ]
                );
            }

            return null;
        }
    }

    /**
     * Convierte un valor de Excel a número decimal limpio.
     */
    private function parsearValor($valor)
    {
        if ($valor === null || $valor === '') {
            return null;
        }

        if (is_numeric($valor)) {
            return $valor;
        }

        $limpio = str_replace('.', '', (string) $valor);
        $limpio = str_replace(',', '.', $limpio);

        return is_numeric($limpio) ? $limpio : null;
    }

    /**
     * Normaliza el estatus general del equipo.
     */
    private function normalizarEstatusGeneral($estatus)
    {
        if (empty($estatus)) {
            return 'Operativo';
        }

        $estatus = mb_strtolower(trim($estatus));

        $mapa = [
            'operativo' => 'Operativo',
            'en mantenimiento' => 'En Mantenimiento',
            'mantenimiento' => 'En Mantenimiento',
            'inoperativo' => 'Inoperativo',
            'donado' => 'Donado/Desincorporado',
            'donado/desincorporado' => 'Donado/Desincorporado',
            'desincorporado' => 'Donado/Desincorporado',
        ];

        return $mapa[$estatus] ?? 'Operativo';
    }

    public function chunkSize(): int
    {
        return 500;
    }
}