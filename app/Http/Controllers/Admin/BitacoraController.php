<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BitacoraGlobal;
use App\Models\User;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * Listado de bitácora con filtros.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');

        $query = BitacoraGlobal::with('usuario')->orderBy('fecha_registro', 'desc');

        // Filtros
        if ($request->modulo) {
            $query->where('modulo', $request->modulo);
        }
        if ($request->accion) {
            $query->where('accion', $request->accion);
        }
        if ($request->usuario_id) {
            $query->where('usuario_id', $request->usuario_id);
        }
        if ($request->fecha_desde) {
            $query->whereDate('fecha_registro', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $query->whereDate('fecha_registro', '<=', $request->fecha_hasta);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('descripcion', 'ILIKE', "%{$search}%")
                  ->orWhere('modelo_codigo', 'ILIKE', "%{$search}%")
                  ->orWhere('usuario_nombre_snapshot', 'ILIKE', "%{$search}%");
            });
        }

        $bitacoras = $query->paginate(25);

        // Opciones para filtros
        $modulos = BitacoraGlobal::select('modulo')->distinct()->orderBy('modulo')->pluck('modulo');
        $acciones = BitacoraGlobal::select('accion')->distinct()->orderBy('accion')->pluck('accion');
        $usuarios = User::orderBy('name')->get();

        return view('admin.bitacora.index', compact('bitacoras', 'modulos', 'acciones', 'usuarios'));
    }

    /**
     * Detalle de un registro de bitácora.
     */
    public function show($id)
    {
        $bitacora = BitacoraGlobal::with('usuario')->findOrFail($id);
        return view('admin.bitacora.show', compact('bitacora'));
    }
}