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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\FiltroSedeTrait;
use App\Services\BitacoraService;
use App\Exports\EntradaSalidaExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class EntradaSalidaController extends Controller
{
    use FiltroSedeTrait;

    public function index(Request $request)
    {
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = RegistroEntradaSalida::with(['sede.estado', 'salidaUsuario', 'entradaUsuario']);

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        if ($request->estatus) {
            $query->where('estatus', $request->estatus);
        }
        if ($request->bien_tipo) {
            $query->where('bien_tipo', $request->bien_tipo);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('bien_codigo', 'ILIKE', "%{$search}%")
                  ->orWhere('bien_descripcion', 'ILIKE', "%{$search}%")
                  ->orWhere('salida_retira_nombre', 'ILIKE', "%{$search}%");
            });
        }

        $registros = $query->orderBy('created_at', 'desc')->paginate(20);

        $pendientes = RegistroEntradaSalida::pendientes()->count();
        $completados = RegistroEntradaSalida::completados()->count();

        return view('admin.entrada_salida.index', compact('registros', 'pendientes', 'completados'));
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
        // FORZAR SEDE SI NO ES ADMIN/AUDITOR
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        
        if (!$esAdmin && !$esAuditor) {
            $request->merge(['sede_id' => $user->sede_id]);
        }

        $validated = $request->validate([
            'bien_tipo' => 'required|in:Tecnologia,BienNacional,Vehiculo,Sonido',
            'bien_id' => 'required|integer',
            'sede_id' => 'required|exists:sedes,id',
            'salida_autoriza_nombre' => 'required|string|max:150',
            'salida_autoriza_cedula' => 'required|string|max:20',
            'salida_autoriza_cargo' => 'required|string|max:150',
            'salida_retira_nombre' => 'required|string|max:150',
            'salida_retira_cedula' => 'required|string|max:20',
            'salida_retira_cargo' => 'required|string|max:150',
            'salida_motivo' => 'required|string',
            'salida_destino' => 'required|string',
            'salida_seguridad_nombre' => 'required|string|max:150',
            'salida_seguridad_cedula' => 'required|string|max:20',
            'salida_estado_bien' => 'required|in:Operativo,Con Daños,Incompleto',
            'salida_observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $infoBien = $this->obtenerInfoBien($request->bien_tipo, $request->bien_id);

            $registro = RegistroEntradaSalida::create([
                'bien_tipo' => $request->bien_tipo,
                'bien_id' => $request->bien_id,
                'bien_codigo' => $infoBien['codigo'],
                'bien_descripcion' => $infoBien['descripcion'],
                'sede_id' => $request->sede_id,
                'fecha_hora_salida' => now(),
                'zona_horaria' => config('app.timezone', 'America/Caracas'),
                'salida_autoriza_nombre' => $request->salida_autoriza_nombre,
                'salida_autoriza_cedula' => $request->salida_autoriza_cedula,
                'salida_autoriza_cargo' => $request->salida_autoriza_cargo,
                'salida_retira_nombre' => $request->salida_retira_nombre,
                'salida_retira_cedula' => $request->salida_retira_cedula,
                'salida_retira_cargo' => $request->salida_retira_cargo,
                'salida_motivo' => $request->salida_motivo,
                'salida_destino' => $request->salida_destino,
                'salida_seguridad_nombre' => $request->salida_seguridad_nombre,
                'salida_seguridad_cedula' => $request->salida_seguridad_cedula,
                'salida_estado_bien' => $request->salida_estado_bien,
                'salida_observaciones' => $request->salida_observaciones,
                'salida_usuario_id' => Auth::id(),
                'salida_ip' => $request->ip(),
                'estatus' => 'Pendiente',
            ]);

            BitacoraService::accion(
                'entrada-salida',
                'registrar_salida',
                "Salida registrada: {$registro->numero_comprobante} - {$registro->bien_codigo} ({$registro->bien_descripcion})",
                $registro,
                ['bien_codigo' => $registro->bien_codigo, 'retira' => $registro->salida_retira_nombre],
                $registro->numero_comprobante
            );

            DB::commit();
            return redirect()->route('admin.entrada-salida.show', $registro)
                ->with('success', 'Salida registrada exitosamente. Comprobante: ' . $registro->numero_comprobante);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la salida: ' . $e->getMessage())->withInput();
        }
    }

    public function registrarEntrada($id)
    {
        $registro = RegistroEntradaSalida::with(['sede.estado'])->findOrFail($id);

        if ($registro->estatus !== 'Pendiente') {
            return redirect()->route('admin.entrada-salida.show', $registro)
                ->with('error', 'Este movimiento ya tiene una entrada registrada.');
        }

        return view('admin.entrada_salida.registrar-entrada', compact('registro'));
    }

    public function storeEntrada(Request $request, $id)
    {
        $registro = RegistroEntradaSalida::findOrFail($id);

        if ($registro->estatus !== 'Pendiente') {
            return redirect()->route('admin.entrada-salida.show', $registro)
                ->with('error', 'Este movimiento ya tiene una entrada registrada.');
        }

        $validated = $request->validate([
            'entrada_recibe_nombre' => 'required|string|max:150',
            'entrada_recibe_cedula' => 'required|string|max:20',
            'entrada_seguridad_nombre' => 'required|string|max:150',
            'entrada_seguridad_cedula' => 'required|string|max:20',
            'entrada_estado_bien' => 'required|in:Operativo,Con Daños,Incompleto,No Retornó',
            'entrada_observaciones' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $datosAnteriores = $registro->toArray();

            $registro->update([
                'fecha_hora_entrada' => now(),
                'entrada_recibe_nombre' => $request->entrada_recibe_nombre,
                'entrada_recibe_cedula' => $request->entrada_recibe_cedula,
                'entrada_seguridad_nombre' => $request->entrada_seguridad_nombre,
                'entrada_seguridad_cedula' => $request->entrada_seguridad_cedula,
                'entrada_estado_bien' => $request->entrada_estado_bien,
                'entrada_observaciones' => $request->entrada_observaciones,
                'entrada_usuario_id' => Auth::id(),
                'entrada_ip' => $request->ip(),
                'estatus' => 'Completado',
            ]);

            BitacoraService::accion(
                'entrada-salida',
                'registrar_entrada',
                "Entrada registrada: {$registro->numero_comprobante} - {$registro->bien_codigo}. Estado al retornar: {$registro->entrada_estado_bien}",
                $registro,
                ['entrega' => $registro->entrada_recibe_nombre, 'estado_entrada' => $registro->entrada_estado_bien],
                $registro->numero_comprobante
            );

            DB::commit();
            return redirect()->route('admin.entrada-salida.show', $registro)
                ->with('success', 'Entrada registrada exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar la entrada: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $registro = RegistroEntradaSalida::with(['sede.estado', 'salidaUsuario', 'entradaUsuario'])->findOrFail($id);
        return view('admin.entrada_salida.show', compact('registro'));
    }

    public function edit($id)
    {
        $registro = RegistroEntradaSalida::findOrFail($id);

        if ($registro->estatus !== 'Pendiente') {
            return redirect()->route('admin.entrada-salida.show', $registro)
                ->with('error', 'No se puede editar un movimiento ya completado.');
        }

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

        if ($registro->estatus !== 'Pendiente') {
            return redirect()->route('admin.entrada-salida.show', $registro)
                ->with('error', 'No se puede editar un movimiento ya completado.');
        }

        // FORZAR SEDE SI NO ES ADMIN/AUDITOR
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');

        if (!$esAdmin && !$esAuditor) {
            $request->merge(['sede_id' => $user->sede_id]);
        }

        $validated = $request->validate([
            'sede_id' => 'required|exists:sedes,id',
            'salida_autoriza_nombre' => 'required|string|max:150',
            'salida_autoriza_cedula' => 'required|string|max:20',
            'salida_autoriza_cargo' => 'required|string|max:150',
            'salida_retira_nombre' => 'required|string|max:150',
            'salida_retira_cedula' => 'required|string|max:20',
            'salida_retira_cargo' => 'required|string|max:150',
            'salida_motivo' => 'required|string',
            'salida_destino' => 'required|string',
            'salida_seguridad_nombre' => 'required|string|max:150',
            'salida_seguridad_cedula' => 'required|string|max:20',
            'salida_estado_bien' => 'required|in:Operativo,Con Daños,Incompleto',
            'salida_observaciones' => 'nullable|string',
        ]);

        $datosAnteriores = $registro->toArray();
        $registro->update($validated);

        BitacoraService::editar(
            'entrada-salida',
            $registro,
            $datosAnteriores,
            "Salida editada: {$registro->numero_comprobante} - {$registro->bien_codigo}",
            $registro->numero_comprobante
        );

        return redirect()->route('admin.entrada-salida.show', $registro)
            ->with('success', 'Salida actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $registro = RegistroEntradaSalida::findOrFail($id);

        DB::beginTransaction();
        try {
            BitacoraService::eliminar(
                'entrada-salida',
                $registro,
                "Movimiento eliminado: {$registro->numero_comprobante} - {$registro->bien_codigo} ({$registro->bien_descripcion})",
                $registro->numero_comprobante
            );

            $registro->delete();

            DB::commit();
            return redirect()->route('admin.entrada-salida.index')
                ->with('success', 'Registro eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function pdfSalida($id)
    {
        $registro = RegistroEntradaSalida::with(['sede.estado', 'salidaUsuario'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.entrada_salida.pdf_salida', compact('registro'));
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);

        return $pdf->download('Salida-' . $registro->numero_comprobante . '.pdf');
    }

    public function pdfEntrada($id)
    {
        $registro = RegistroEntradaSalida::with(['sede.estado', 'salidaUsuario', 'entradaUsuario'])->findOrFail($id);

        if ($registro->estatus !== 'Completado') {
            return redirect()->route('admin.entrada-salida.show', $registro)
                ->with('error', 'Aún no se ha registrado la entrada.');
        }

        $pdf = Pdf::loadView('admin.entrada_salida.pdf_entrada', compact('registro'));
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);

        return $pdf->download('Entrada-' . $registro->numero_comprobante . '.pdf');
    }

    public function pdfListado(Request $request)
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = RegistroEntradaSalida::with(['sede.estado', 'salidaUsuario', 'entradaUsuario']);

        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        if ($request->estatus) $query->where('estatus', $request->estatus);
        if ($request->bien_tipo) $query->where('bien_tipo', $request->bien_tipo);

        $registros = $query->orderBy('created_at', 'desc')->get();

        $pdf = Pdf::loadView('admin.entrada_salida.pdf_listado', compact('registros'));
        $pdf->setPaper('letter', 'landscape');
        $pdf->setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);

        return $pdf->download('movimientos-' . date('Y-m-d') . '.pdf');
    }

    public function exportarExcel()
    {
        return Excel::download(new EntradaSalidaExport, 'movimientos-' . date('Y-m-d') . '.xlsx');
    }

    private function obtenerInfoBien($tipo, $id): array
    {
        return match ($tipo) {
            'Tecnologia' => (function () use ($id) {
                $bien = Equipo::find($id);
                return [
                    'codigo' => $bien->codigo_inventario_institucional ?? 'N/A',
                    'descripcion' => $bien ? ($bien->marca . ' ' . $bien->modelo) : 'N/A',
                ];
            })(),
            'BienNacional' => (function () use ($id) {
                $bien = BienNacional::find($id);
                return [
                    'codigo' => $bien->codigo_inventario ?? 'N/A',
                    'descripcion' => $bien->descripcion ?? 'N/A',
                ];
            })(),
            'Vehiculo' => (function () use ($id) {
                $bien = Vehiculo::find($id);
                return [
                    'codigo' => $bien->placa ?? 'N/A',
                    'descripcion' => $bien ? ($bien->marca . ' ' . $bien->modelo) : 'N/A',
                ];
            })(),
            'Sonido' => (function () use ($id) {
                $bien = EquipoSonido::find($id);
                return [
                    'codigo' => $bien->serial ?? 'N/A',
                    'descripcion' => $bien ? ($bien->marca . ' ' . $bien->modelo) : 'N/A',
                ];
            })(),
            default => ['codigo' => 'N/A', 'descripcion' => 'N/A'],
        };
    }
}