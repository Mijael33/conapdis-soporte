<?php

namespace App\Imports;

use App\Models\Equipo;
use App\Models\TipoEquipo;
use App\Models\Departamento;
use App\Models\Sede;
use App\Models\Estado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Auth;

class EquiposImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsEmptyRows
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
        if (empty($row['codigo_inventario']) && empty($row['tipo_equipo'])) {
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

            if (empty($row['tipo_equipo'])) {
                throw new \Exception('El campo "tipo_equipo" es obligatorio');
            }

            if (empty($row['departamento'])) {
                throw new \Exception('El campo "departamento" es obligatorio');
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

            $existe = Equipo::where(
                'codigo_inventario_institucional',
                trim($row['codigo_inventario'])
            )->exists();

            if ($existe) {
                throw new \Exception(
                    'El código "' . $row['codigo_inventario'] . '" ya existe en el sistema'
                );
            }

            // =====================================================
            // OBTENER O CREAR TIPO DE EQUIPO
            // =====================================================

            $tipoEquipo = TipoEquipo::firstOrCreate(
                ['nombre' => trim($row['tipo_equipo'])],
                ['descripcion' => 'Creado automáticamente al importar']
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
            // OBTENER O CREAR DEPARTAMENTO
            // =====================================================

            $departamento = null;
            if ($sede && !empty($row['departamento'])) {
                $departamento = Departamento::firstOrCreate(
                    [
                        'sede_id' => $sede->id,
                        'nombre_departamento' => trim($row['departamento'])
                    ],
                    [
                        'piso' => null,
                        'extension_telefonica' => null
                    ]
                );
            }

            // =====================================================
            // CREAR EQUIPO
            // =====================================================

            $equipo = new Equipo([
                'codigo_inventario_institucional' => trim($row['codigo_inventario']),
                'serial_chasis' => isset($row['serial_chasis']) ? trim($row['serial_chasis']) : null,
                'tipo_equipo_id' => $tipoEquipo->id,
                'departamento_id' => $departamento ? $departamento->id : null,
                'marca' => trim($row['marca']),
                'modelo' => trim($row['modelo']),
                'usuario_asignado_nombre' => isset($row['usuario_nombre']) ? trim($row['usuario_nombre']) : null,
                'usuario_asignado_cedula' => isset($row['usuario_cedula']) ? trim($row['usuario_cedula']) : null,
                'usuario_asignado_cargo' => isset($row['usuario_cargo']) ? trim($row['usuario_cargo']) : null,
            ]);

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
                    ]
                );
            }

            return null;
        }
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