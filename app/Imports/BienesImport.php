<?php

namespace App\Imports;

use App\Models\BienNacional;
use App\Models\CategoriaBien;
use App\Models\Sede;
use App\Models\Estado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Auth;

class BienesImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsEmptyRows
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

        // Saltar filas totalmente vacías
        if (empty($row['codigo_inventario']) && empty($row['descripcion'])) {
            return null;
        }

        try {
            $user = Auth::user();
            $esAdmin = $user->hasRole('Administrador');

            // =====================================================
            // VALIDACIONES MANUALES (para capturar errores sin detener)
            // =====================================================

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

            // =====================================================
            // VERIFICAR DUPLICADOS
            // =====================================================

            $existe = BienNacional::where(
                'codigo_inventario',
                trim($row['codigo_inventario'])
            )->exists();

            if ($existe) {
                throw new \Exception(
                    'El código "' . $row['codigo_inventario'] . '" ya existe en el sistema'
                );
            }

            // =====================================================
            // OBTENER O CREAR CATEGORÍA
            // =====================================================

            $categoria = CategoriaBien::firstOrCreate(
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
            // CREAR BIEN
            // =====================================================

            $bien = new BienNacional([
                'codigo_inventario' => trim($row['codigo_inventario']),
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
                'valor_adquisicion' => $row['valor_adquisicion'] ?? null,
                'fecha_adquisicion' => $row['fecha_adquisicion'] ?? null,
                'observaciones' => isset($row['observaciones']) ? trim($row['observaciones']) : null,
            ]);

            // Registrar éxito
            if ($this->service) {
                $this->service->registrarExito();
            }

            return $bien;

        } catch (\Exception $e) {
            // Registrar error sin detener el proceso
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