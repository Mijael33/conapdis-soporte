<?php

namespace App\Imports;

use App\Models\Componente;
use App\Models\CategoriaComponente;
use App\Models\Sede;
use App\Models\Estado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Facades\Auth;

class ComponentesImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading, SkipsEmptyRows
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
        if (empty($row['serial_unico']) && empty($row['marca'])) {
            return null;
        }

        try {
            $user = Auth::user();
            $esAdmin = $user->hasRole('Administrador');

            // =====================================================
            // VALIDACIONES MANUALES
            // =====================================================

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

            // =====================================================
            // VERIFICAR DUPLICADOS
            // =====================================================

            $existe = Componente::where(
                'serial_unico',
                trim($row['serial_unico'])
            )->exists();

            if ($existe) {
                throw new \Exception(
                    'El serial "' . $row['serial_unico'] . '" ya existe en el sistema'
                );
            }

            // =====================================================
            // OBTENER O CREAR CATEGORÍA
            // =====================================================

            $categoria = CategoriaComponente::firstOrCreate(
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
            // CREAR COMPONENTE
            // =====================================================

            $componente = new Componente([
                'categoria_componente_id' => $categoria->id,
                'marca' => trim($row['marca']),
                'modelo' => trim($row['modelo']),
                'serial_unico' => trim($row['serial_unico']),
                'sede_id' => $sede ? $sede->id : ($esAdmin ? null : $user->sede_id),
                'estatus' => $this->normalizarEstatus($row['estatus'] ?? null),
                'observaciones' => isset($row['observaciones']) ? trim($row['observaciones']) : null,
            ]);

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
            'instalado' => 'Instalado',
            'en revision' => 'En Revisión',
            'en revisión' => 'En Revisión',
            'revision' => 'En Revisión',
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