<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RegistroEntradaSalida;
use App\Models\Equipo;
use App\Models\BienNacional;
use App\Models\Vehiculo;
use App\Models\EquipoSonido;
use App\Models\Sede;
use Illuminate\Http\Request;
use App\Traits\FiltroSedeTrait;
use App\Exports\EntradaSalidaExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class EntradaSalidaController extends Controller
{
    use FiltroSedeTrait;

    public function index(Request $request)
    {
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = RegistroEntradaSalida::with(['sede.estado', 'usuario']);

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        if ($request->tipo) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->bien_tipo) {
            $query->where('bien_tipo', $request->bien_tipo);
        }

        $registros = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.entrada_salida.index', compact('registros'));
    }

    public function show($id)
    {
        $registro = RegistroEntradaSalida::with(['sede.estado', 'usuario'])->findOrFail($id);
        return view('admin.entrada_salida.show', compact('registro'));
    }

    public function create()
    {
        $equipos = Equipo::orderBy('codigo_inventario_institucional')->get();
        $bienes = BienNacional::orderBy('codigo_inventario')->get();
        $vehiculos = Vehiculo::orderBy('placa')->get();
        $sonido = EquipoSonido::orderBy('serial')->get();
        $sedes = Sede::with('estado')->orderBy('nombre_sede')->get();

        return view('admin.entrada_salida.create', compact('equipos', 'bienes', 'vehiculos', 'sonido', 'sedes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo' => 'required|in:Salida,Entrada',
            'bien_tipo' => 'required|in:Tecnologia,BienNacional,Vehiculo,Sonido',
            'bien_id' => 'required|integer',
            'sede_id' => 'required|exists:sedes,id',
            'fecha_hora_salida' => 'nullable|date',
            'fecha_hora_entrada' => 'nullable|date',
            'autorizado_por_nombre' => 'nullable|string|max:150',
            'autorizado_por_cedula' => 'nullable|string|max:20',
            'autorizado_por_cargo' => 'nullable|string|max:150',
            'persona_retira_nombre' => 'nullable|string|max:150',
            'persona_retira_cedula' => 'nullable|string|max:20',
            'persona_retira_cargo' => 'nullable|string|max:150',
            'motivo' => 'nullable|string',
            'destino' => 'nullable|string',
            'seguridad_salida_nombre' => 'nullable|string|max:150',
            'seguridad_salida_cedula' => 'nullable|string|max:20',
            'seguridad_entrada_nombre' => 'nullable|string|max:150',
            'seguridad_entrada_cedula' => 'nullable|string|max:20',
            'estado_salida' => 'nullable|in:Operativo,Con Daños,Incompleto',
            'estado_entrada' => 'nullable|in:Operativo,Con Daños,Incompleto,No Retornó',
            'observaciones_salida' => 'nullable|string',
            'observaciones_entrada' => 'nullable|string',
        ]);

        $validated['usuario_id'] = auth()->id();

        $registro = RegistroEntradaSalida::create($validated);
        return redirect()->route('admin.entrada-salida.index')->with('success', 'Registro creado.');
    }

    public function edit($id)
    {
        $registro = RegistroEntradaSalida::findOrFail($id);
        $equipos = Equipo::orderBy('codigo_inventario_institucional')->get();
        $bienes = BienNacional::orderBy('codigo_inventario')->get();
        $vehiculos = Vehiculo::orderBy('placa')->get();
        $sonido = EquipoSonido::orderBy('serial')->get();
        $sedes = Sede::with('estado')->orderBy('nombre_sede')->get();

        return view('admin.entrada_salida.edit', compact('registro', 'equipos', 'bienes', 'vehiculos', 'sonido', 'sedes'));
    }

    public function update(Request $request, $id)
    {
        $registro = RegistroEntradaSalida::findOrFail($id);

        $validated = $request->validate([
            'tipo' => 'required|in:Salida,Entrada',
            'bien_tipo' => 'required|in:Tecnologia,BienNacional,Vehiculo,Sonido',
            'bien_id' => 'required|integer',
            'sede_id' => 'required|exists:sedes,id',
            'fecha_hora_salida' => 'nullable|date',
            'fecha_hora_entrada' => 'nullable|date',
            'autorizado_por_nombre' => 'nullable|string|max:150',
            'autorizado_por_cedula' => 'nullable|string|max:20',
            'autorizado_por_cargo' => 'nullable|string|max:150',
            'persona_retira_nombre' => 'nullable|string|max:150',
            'persona_retira_cedula' => 'nullable|string|max:20',
            'persona_retira_cargo' => 'nullable|string|max:150',
            'motivo' => 'nullable|string',
            'destino' => 'nullable|string',
            'seguridad_salida_nombre' => 'nullable|string|max:150',
            'seguridad_salida_cedula' => 'nullable|string|max:20',
            'seguridad_entrada_nombre' => 'nullable|string|max:150',
            'seguridad_entrada_cedula' => 'nullable|string|max:20',
            'estado_salida' => 'nullable|in:Operativo,Con Daños,Incompleto',
            'estado_entrada' => 'nullable|in:Operativo,Con Daños,Incompleto,No Retornó',
            'observaciones_salida' => 'nullable|string',
            'observaciones_entrada' => 'nullable|string',
        ]);

        $registro->update($validated);
        return redirect()->route('admin.entrada-salida.index')->with('success', 'Registro actualizado.');
    }

    public function destroy($id)
    {
        $registro = RegistroEntradaSalida::findOrFail($id);
        $registro->delete();
        return redirect()->route('admin.entrada-salida.index')->with('success', 'Registro eliminado.');
    }

    /*
    |--------------------------------------------------------------------------
    | REPORTES PDF
    |--------------------------------------------------------------------------
    */

    /**
     * Generar PDF de un registro específico.
     */
    public function pdf($id)
    {
        $registro = RegistroEntradaSalida::with(['sede.estado', 'usuario'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.entrada_salida.pdf', compact('registro'));
        $pdf->setPaper('letter', 'portrait');

        $nombreArchivo = 'Movimiento-' . $registro->tipo . '-' . $registro->id . '-' . date('Y-m-d') . '.pdf';
        return $pdf->download($nombreArchivo);
    }

    /**
     * Generar PDF de todos los registros (listado).
     */
    public function pdfListado(Request $request)
    {
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');

        $query = RegistroEntradaSalida::with(['sede.estado', 'usuario']);

        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        if ($request->tipo) {
            $query->where('tipo', $request->tipo);
        }
        if ($request->bien_tipo) {
            $query->where('bien_tipo', $request->bien_tipo);
        }

        $registros = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('admin.entrada_salida.pdf_listado', compact('registros'));
        $pdf->setPaper('letter', 'landscape');

        return $pdf->download('movimientos-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Exportar a Excel.
     */
    public function exportarExcel()
    {
        return Excel::download(new EntradaSalidaExport, 'movimientos-' . date('Y-m-d') . '.xlsx');
    }
}