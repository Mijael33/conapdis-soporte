<?php

namespace App\Imports;

use App\Models\Vehiculo;
use App\Models\CategoriaVehiculo;
use App\Models\Sede;
use App\Models\Estado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Auth;

class VehiculosImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsEmptyRows
{
    protected $service;
    protected $filaActual = 1;

    public function __construct($service = null)
    {
        $this->service = $service;
    }

    public function model(array $row)
    {
        $this->filaActual++;

        // Saltar filas vacías
        if (empty($row['codigo_inventario']) && empty($row['placa'])) {
            return null;
        }

        try {
            $user = Auth::user();
            $esAdmin = $user->hasRole('Administrador');

            // =====================================================
            // VALIDACIONES MANUALES
            // =====================================================

            if (empty($row['codigo_inventario'])) {
                throw new \Exception('El campo "codigo_inventario" es obligatorio');
            }

            if (empty($row['placa'])) {
                throw new \Exception('El campo "placa" es obligatorio');
            }

            if (empty($row['categoria'])) {
                throw new \Exception('El campo "categoria" es obligatorio');
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

            // =====================================================
            // VERIFICAR DUPLICADOS
            // =====================================================

            $existeCodigo = Vehiculo::where(
                'codigo_inventario',
                trim($row['codigo_inventario'])
            )->exists();

            if ($existeCodigo) {
                throw new \Exception(
                    'El código "' . $row['codigo_inventario'] . '" ya existe en el sistema'
                );
            }

            $existePlaca = Vehiculo::where(
                'placa',
                trim($row['placa'])
            )->exists();

            if ($existePlaca) {
                throw new \Exception(
                    'La placa "' . $row['placa'] . '" ya está registrada'
                );
            }

            // =====================================================
            // OBTENER O CREAR CATEGORÍA
            // =====================================================

            $categoria = CategoriaVehiculo::firstOrCreate(
                ['nombre' => trim($row['categoria'])],
                ['descripcion' => 'Creada automáticamente al importar']
            );

            // =====================================================
            // OBTENER O CREAR SEDE
            // =====================================================

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

            // =====================================================
            // VALIDAR PERMISO POR SEDE
            // =====================================================

            if (!$esAdmin && $sede && $sede->id !== $user->sede_id) {
                throw new \Exception(
                    'No tiene permiso para importar en la sede: ' . $row['sede']
                );
            }

            // =====================================================
            // CREAR VEHÍCULO
            // =====================================================

            $vehiculo = new Vehiculo([
                'codigo_inventario' => trim($row['codigo_inventario']),
                'categoria_vehiculo_id' => $categoria->id,
                'sede_id' => $sede ? $sede->id : ($esAdmin ? null : $user->sede_id),
                'placa' => trim($row['placa']),
                'marca' => trim($row['marca']),
                'modelo' => trim($row['modelo']),
                'anio' => $row['anio'] ?? null,
                'color' => isset($row['color']) ? trim($row['color']) : null,
                'serial_motor' => isset($row['serial_motor']) ? trim($row['serial_motor']) : null,
                'serial_chasis' => isset($row['serial_chasis']) ? trim($row['serial_chasis']) : null,
                'kilometraje' => $row['kilometraje'] ?? 0,
                'estatus' => $this->normalizarEstatus($row['estatus'] ?? null),
                'observaciones' => isset($row['observaciones']) ? trim($row['observaciones']) : null,
            ]);

            if ($this->service) {
                $this->service->registrarExito();
            }

            return $vehiculo;

        } catch (\Exception $e) {
            if ($this->service) {
                $this->service->registrarError(
                    $this->filaActual,
                    $e->getMessage(),
                    [
                        'codigo' => $row['codigo_inventario'] ?? 'N/A',
                        'placa' => $row['placa'] ?? 'N/A',
                    ]
                );
            }

            return null;
        }
    }

    /**
     * Normaliza el estatus a un valor válido.
     */
    private function normalizarEstatus($estatus)
    {
        if (empty($estatus)) {
            return 'Disponible';
        }

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

    public function batchSize(): int
    {
        return 500;
    }

    public function chunkSize(): int
    {
        return 500;
    }
}