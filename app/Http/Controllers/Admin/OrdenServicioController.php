<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrdenServicio;
use App\Models\Equipo;
use App\Models\User;
use App\Models\BitacoraEquipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FiltroSedeTrait;
use App\Services\BitacoraService;

class OrdenServicioController extends Controller
{
    use FiltroSedeTrait;

    public function index(Request $request)
    {
        $query = OrdenServicio::with(['equipo', 'tecnico']);
        $query = $this->filtrarOrdenesPorSede($query);

        if ($request->estatus) {
            if ($request->estatus === 'abiertas') {
                $query->whereNull('estatus_final');
            } else {
                $query->where('estatus_final', $request->estatus);
            }
        }

        $ordenes = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.ordenes.index', compact('ordenes'));
    }

    public function show($id)
    {
        $ordene = OrdenServicio::with(['equipo.componentes', 'tecnico'])->findOrFail($id);
        return view('admin.ordenes.show', compact('ordene'));
    }

    public function create()
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $equiposQuery = Equipo::with('sede')
            ->whereDoesntHave('ordenesServicio', function ($q) {
                $q->whereNull('estatus_final');
            })
            ->orderBy('codigo_inventario_institucional');

        if (!$esAdmin) {
            $equiposQuery->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $equiposQuery->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $equiposQuery->whereHas('sede', function ($q) use ($estadoId) {
                $q->where('estado_id', $estadoId);
            });
        }

        $equipos = $equiposQuery->get();

        $tecnicosQuery = User::orderBy('name');

        if (!$esAdmin) {
            $tecnicosQuery->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $tecnicosQuery->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $tecnicosQuery->where('estado_id', $estadoId);
        }

        $tecnicos = $tecnicosQuery->get();

        return view('admin.ordenes.create', compact('equipos', 'tecnicos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipo_id' => 'required|exists:equipos,id',
            'tecnico_id' => 'required|exists:users,id',
            'problema_reportado_usuario' => 'required|string',
        ]);

        $tieneOrdenAbierta = OrdenServicio::where('equipo_id', $validated['equipo_id'])
            ->whereNull('estatus_final')
            ->exists();

        if ($tieneOrdenAbierta) {
            return back()->with('error', 'Este equipo ya tiene una orden de servicio abierta.')->withInput();
        }

        $anio = date('Y');
        $ultimoTicket = OrdenServicio::whereYear('created_at', $anio)->count();
        $codigoTicket = 'CONAPDIS-' . $anio . '-' . str_pad($ultimoTicket + 1, 4, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $orden = OrdenServicio::create([
                'codigo_ticket' => $codigoTicket,
                'equipo_id' => $validated['equipo_id'],
                'tecnico_id' => $validated['tecnico_id'],
                'problema_reportado_usuario' => $validated['problema_reportado_usuario'],
            ]);

            $equipo = Equipo::findOrFail($validated['equipo_id']);
            $estatusAnterior = $equipo->estatus_general;
            $equipo->update(['estatus_general' => 'En Mantenimiento']);

            BitacoraEquipo::create([
                'equipo_id' => $equipo->id,
                'usuario_id' => auth()->id(),
                'accion' => 'Creación de Orden (' . $codigoTicket . ')',
                'descripcion_detallada' => 'Se abrió la orden de servicio. Problema reportado: ' . $validated['problema_reportado_usuario'],
                'datos_anteriores' => ['estatus_general' => $estatusAnterior],
                'datos_nuevos' => ['estatus_general' => 'En Mantenimiento'],
                'fecha_registro' => now(),
            ]);

            BitacoraService::accion(
                'ordenes',
                'crear',
                "Orden de servicio creada: {$codigoTicket} para equipo {$equipo->codigo_inventario_institucional}",
                $orden,
                [
                    'codigo_ticket' => $codigoTicket,
                    'equipo' => $equipo->codigo_inventario_institucional,
                    'tecnico_id' => $validated['tecnico_id'],
                ],
                $codigoTicket
            );

            DB::commit();
            return redirect()->route('admin.ordenes.index')
                ->with('success', 'Orden creada. Ticket: ' . $codigoTicket);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $ordene = OrdenServicio::findOrFail($id);
        return view('admin.ordenes.edit', compact('ordene'));
    }

    public function update(Request $request, $id)
    {
        $ordene = OrdenServicio::findOrFail($id);

        $validated = $request->validate([
            'diagnostico_tecnico' => 'nullable|string',
            'acciones_realizadas' => 'nullable|string',
            'estatus_final' => 'nullable|in:Reparado,En Espera de Repuesto,Remitido a Sede Central,Irrecuperable',
        ]);

        $data = $validated;

        $equipo = Equipo::findOrFail($ordene->equipo_id);
        $estatusAnterior = $equipo->estatus_general;
        $nuevoEstatusEquipo = $estatusAnterior;

        if (!empty($validated['estatus_final'])) {
            $data['fecha_cierre'] = now();

            switch ($validated['estatus_final']) {
                case 'Reparado':
                    $nuevoEstatusEquipo = 'Operativo';
                    break;
                case 'En Espera de Repuesto':
                case 'Remitido a Sede Central':
                    $nuevoEstatusEquipo = 'En Mantenimiento';
                    break;
                case 'Irrecuperable':
                    $nuevoEstatusEquipo = 'Inoperativo';
                    break;
                default:
                    $nuevoEstatusEquipo = 'En Mantenimiento';
                    break;
            }

            $equipo->update(['estatus_general' => $nuevoEstatusEquipo]);
        }

        $ordene->update($data);

        BitacoraEquipo::create([
            'equipo_id' => $ordene->equipo_id,
            'usuario_id' => auth()->id(),
            'accion' => 'Act. Orden (' . $ordene->codigo_ticket . ')',
            'descripcion_detallada' => 'Actualización de orden de servicio. ' .
                (!empty($validated['estatus_final']) ? 'Estatus final: ' . $validated['estatus_final'] . '. ' : '') .
                'Diagnóstico: ' . ($validated['diagnostico_tecnico'] ?? 'N/A'),
            'datos_anteriores' => ['estatus_general' => $estatusAnterior],
            'datos_nuevos' => ['estatus_general' => $nuevoEstatusEquipo],
            'fecha_registro' => now(),
        ]);

        BitacoraService::accion(
            'ordenes',
            'editar',
            "Orden de servicio actualizada: {$ordene->codigo_ticket}" .
                (!empty($validated['estatus_final']) ? " (estatus final: {$validated['estatus_final']})" : ''),
            $ordene,
            [
                'codigo_ticket' => $ordene->codigo_ticket,
                'estatus_final' => $validated['estatus_final'] ?? null,
            ],
            $ordene->codigo_ticket
        );

        return redirect()->route('admin.ordenes.index')->with('success', 'Orden actualizada y registrada en la bitácora.');
    }
}