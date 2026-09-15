<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrdenServicio;
use App\Models\Equipo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Traits\FiltroSedeTrait;

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

        /*
        |--------------------------------------------------------------------------
        | EQUIPOS: Solo de la sede filtrada y SIN orden abierta
        |--------------------------------------------------------------------------
        */
        $equiposQuery = Equipo::with('departamento.sede.estado')
            ->whereDoesntHave('ordenesServicio', function ($q) {
                $q->whereNull('estatus_final');
            })
            ->orderBy('codigo_inventario_institucional');

        if (!$esAdmin) {
            $equiposQuery->whereHas('departamento.sede', function ($q) use ($user) {
                $q->where('id', $user->sede_id);
            });
        } elseif ($sedeId) {
            $equiposQuery->whereHas('departamento.sede', function ($q) use ($sedeId) {
                $q->where('id', $sedeId);
            });
        } elseif ($estadoId) {
            $equiposQuery->whereHas('departamento.sede', function ($q) use ($estadoId) {
                $q->where('estado_id', $estadoId);
            });
        }

        $equipos = $equiposQuery->get();

        /*
        |--------------------------------------------------------------------------
        | TÉCNICOS: Todos los usuarios (ya que los roles se crean dinámicamente)
        |--------------------------------------------------------------------------
        | Se listan todos los usuarios activos de la sede. El admin decidirá
        | quién es técnico al momento de asignar.
        */
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
            OrdenServicio::create([
                'codigo_ticket' => $codigoTicket,
                'equipo_id' => $validated['equipo_id'],
                'tecnico_id' => $validated['tecnico_id'],
                'problema_reportado_usuario' => $validated['problema_reportado_usuario'],
            ]);

            Equipo::where('id', $validated['equipo_id'])->update(['estatus_general' => 'En Mantenimiento']);

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

        if ($validated['estatus_final']) {
            $data['fecha_cierre'] = now();

            if ($validated['estatus_final'] === 'Reparado') {
                Equipo::where('id', $ordene->equipo_id)->update(['estatus_general' => 'Operativo']);
            }
            if ($validated['estatus_final'] === 'Irrecuperable') {
                Equipo::where('id', $ordene->equipo_id)->update(['estatus_general' => 'Inoperativo']);
            }
        }

        $ordene->update($data);

        return redirect()->route('admin.ordenes.index')->with('success', 'Orden actualizada.');
    }
}