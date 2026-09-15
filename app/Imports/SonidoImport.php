<?php

namespace App\Imports;

use App\Models\EquipoSonido;
use App\Models\CategoriaSonido;
use App\Models\Sede;
use App\Models\Estado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Auth;

class SonidoImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsEmptyRows
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
        if (empty($row['codigo_inventario']) && empty($row['serial'])) {
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

            if (empty($row['serial'])) {
                throw new \Exception('El campo "serial" es obligatorio');
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

            $existeCodigo = EquipoSonido::where(
                'codigo_inventario',
                trim($row['codigo_inventario'])
            )->exists();

            if ($existeCodigo) {
                throw new \Exception(
                    'El código "' . $row['codigo_inventario'] . '" ya existe en el sistema'
                );
            }

            $existeSerial = EquipoSonido::where(
                'serial',
                trim($row['serial'])
            )->exists();

            if ($existeSerial) {
                throw new \Exception(
                    'El serial "' . $row['serial'] . '" ya está registrado'
                );
            }

            // =====================================================
            // OBTENER O CREAR CATEGORÍA
            // =====================================================

            $categoria = CategoriaSonido::firstOrCreate(
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
            // CREAR EQUIPO DE SONIDO
            // =====================================================

            $equipo = new EquipoSonido([
                'codigo_inventario' => trim($row['codigo_inventario']),
                'categoria_sonido_id' => $categoria->id,
                'sede_id' => $sede ? $sede->id : ($esAdmin ? null : $user->sede_id),
                'marca' => trim($row['marca']),
                'modelo' => trim($row['modelo']),
                'serial' => trim($row['serial']),
                'potencia' => isset($row['potencia']) ? trim($row['potencia']) : null,
                'estatus' => $this->normalizarEstatus($row['estatus'] ?? null),
                'observaciones' => isset($row['observaciones']) ? trim($row['observaciones']) : null,
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
                        'serial' => $row['serial'] ?? 'N/A',
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