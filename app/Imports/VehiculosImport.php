<?php

namespace App\Imports;

use App\Models\Vehiculo;
use App\Models\CategoriaVehiculo;
use App\Models\Sede;
use App\Models\Estado;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithLimit;
use Illuminate\Support\Facades\Auth;

class VehiculosImport implements ToModel, WithHeadingRow, WithLimit
{
    protected $service;
    protected $filaActual = 1;

    protected array $codigosEnArchivo = [];
    protected array $placasEnArchivo = [];
    protected array $categoriasCache = [];
    protected array $sedesCache = [];

    protected $userId = null;
    protected $userSedeId = null;
    protected $puedeImportarCualquierSede = false;

    public function __construct($service = null)
    {
        $this->service = $service;

        $user = Auth::user();
        if ($user) {
            $this->userId = $user->id;
            $this->userSedeId = $user->sede_id;
            $this->puedeImportarCualquierSede = $user->puedeVerNacional();
        }
    }

    public function model(array $row)
    {
        $this->filaActual++;

        if (empty($row['codigo_inventario']) && empty($row['placa'])) {
            return null;
        }

        try {
            $puedeImportarCualquierSede = $this->puedeImportarCualquierSede;
            $userSedeId = $this->userSedeId;

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

            $codigoTrim = trim($row['codigo_inventario']);
            $placaTrim = strtoupper(trim($row['placa']));

            if (isset($this->codigosEnArchivo[$codigoTrim])) {
                throw new \Exception(
                    'El código "' . $codigoTrim . '" está DUPLICADO dentro del mismo archivo (ya apareció en la fila ' .
                    $this->codigosEnArchivo[$codigoTrim] . ')'
                );
            }

            if (isset($this->placasEnArchivo[$placaTrim])) {
                throw new \Exception(
                    'La placa "' . $placaTrim . '" está DUPLICADA dentro del mismo archivo (ya apareció en la fila ' .
                    $this->placasEnArchivo[$placaTrim] . ')'
                );
            }

            if (Vehiculo::where('codigo_inventario', $codigoTrim)->exists()) {
                throw new \Exception('El código "' . $codigoTrim . '" ya existe en la base de datos');
            }

            if (Vehiculo::where('placa', $placaTrim)->exists()) {
                throw new \Exception('La placa "' . $placaTrim . '" ya existe en la base de datos');
            }

            $nombreCategoria = trim($row['categoria']);
            if (!isset($this->categoriasCache[$nombreCategoria])) {
                $this->categoriasCache[$nombreCategoria] = CategoriaVehiculo::firstOrCreate(
                    ['nombre' => $nombreCategoria],
                    ['descripcion' => 'Creada automáticamente al importar']
                );
            }
            $categoria = $this->categoriasCache[$nombreCategoria];

            $nombreSede = trim($row['sede']);
            if (!isset($this->sedesCache[$nombreSede])) {
                $sede = Sede::where('nombre_sede', $nombreSede)->first();
                if (!$sede) {
                    $dc = Estado::where('nombre', 'Distrito Capital')->first();
                    if ($dc) {
                        $sede = Sede::create([
                            'estado_id' => $dc->id,
                            'nombre_sede' => $nombreSede,
                            'direccion' => 'Dirección pendiente',
                            'codigo_postal' => null,
                        ]);
                    }
                }
                $this->sedesCache[$nombreSede] = $sede;
            }
            $sede = $this->sedesCache[$nombreSede];

            if (!$puedeImportarCualquierSede && $sede && $sede->id !== $userSedeId) {
                throw new \Exception('No tiene permiso para importar en la sede: ' . $row['sede']);
            }

            $valorPrudencial = $this->parsearValor($row['valor_prudencial'] ?? null);
            $valorAdquisicion = $this->parsearValor($row['valor_adquisicion'] ?? null);

            $usuarioNombre = $row['usuario_asignado_nombre'] ?? $row['usuario_nombre'] ?? null;
            $usuarioCedula = $row['usuario_asignado_cedula'] ?? $row['usuario_cedula'] ?? null;
            $usuarioCargo = $row['usuario_asignado_cargo'] ?? $row['usuario_cargo'] ?? null;

            $vehiculo = new Vehiculo([
                'codigo_inventario' => $codigoTrim,
                'categoria_vehiculo_id' => $categoria->id,
                'sede_id' => $sede ? $sede->id : ($puedeImportarCualquierSede ? null : $userSedeId),
                'placa' => $placaTrim,
                'marca' => trim($row['marca']),
                'modelo' => trim($row['modelo']),
                'anio' => $row['anio'] ?? null,
                'color' => isset($row['color']) ? trim($row['color']) : null,
                'serial_motor' => isset($row['serial_motor']) ? trim($row['serial_motor']) : null,
                'serial_carroceria' => isset($row['serial_carroceria'])
                    ? trim($row['serial_carroceria'])
                    : (isset($row['serial_chasis']) ? trim($row['serial_chasis']) : null),
                'kilometraje' => $row['kilometraje'] ?? 0,
                'estatus' => $this->normalizarEstatus($row['estatus'] ?? null),
                'usuario_asignado_nombre' => $usuarioNombre ? trim($usuarioNombre) : null,
                'usuario_asignado_cedula' => $usuarioCedula ? trim($usuarioCedula) : null,
                'usuario_asignado_cargo' => $usuarioCargo ? trim($usuarioCargo) : null,
                'valor_prudencial' => $valorPrudencial,
                'valor_adquisicion' => $valorAdquisicion,
                'observaciones' => isset($row['observaciones']) ? trim($row['observaciones']) : null,
            ]);

            $this->codigosEnArchivo[$codigoTrim] = $this->filaActual;
            $this->placasEnArchivo[$placaTrim] = $this->filaActual;

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

    public function limit(): int
    {
        return 10000;
    }
}