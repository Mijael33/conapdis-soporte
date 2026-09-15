<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BitacoraEquipo;
use App\Models\BitacoraAccesoContrasena;
use App\Models\Sede;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    /**
     * Listado de bitácora combinada (equipos + accesos a contraseñas).
     * Admin ve todo. Otros solo ven lo de su sede/estado.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        // Bitácora de equipos
        $queryEquipos = BitacoraEquipo::with(['equipo.departamento.sede.estado', 'usuario']);

        if (!$esAdmin && !$esAuditor) {
            $queryEquipos->whereHas('equipo.departamento.sede', function ($q) use ($user) {
                $q->where('id', $user->sede_id);
            });
        } elseif ($sedeId) {
            $queryEquipos->whereHas('equipo.departamento.sede', function ($q) use ($sedeId) {
                $q->where('id', $sedeId);
            });
        } elseif ($estadoId) {
            $queryEquipos->whereHas('equipo.departamento.sede', function ($q) use ($estadoId) {
                $q->where('estado_id', $estadoId);
            });
        }

        // Bitácora de accesos a contraseñas
        $queryAccesos = BitacoraAccesoContrasena::with(['sistemaOperativo.equipo.departamento.sede.estado', 'usuario']);

        if (!$esAdmin && !$esAuditor) {
            $queryAccesos->whereHas('sistemaOperativo.equipo.departamento.sede', function ($q) use ($user) {
                $q->where('id', $user->sede_id);
            });
        } elseif ($sedeId) {
            $queryAccesos->whereHas('sistemaOperativo.equipo.departamento.sede', function ($q) use ($sedeId) {
                $q->where('id', $sedeId);
            });
        } elseif ($estadoId) {
            $queryAccesos->whereHas('sistemaOperativo.equipo.departamento.sede', function ($q) use ($estadoId) {
                $q->where('estado_id', $estadoId);
            });
        }

        // Filtros adicionales
        if ($request->fecha_desde) {
            $queryEquipos->whereDate('fecha_registro', '>=', $request->fecha_desde);
            $queryAccesos->whereDate('fecha_acceso', '>=', $request->fecha_desde);
        }
        if ($request->fecha_hasta) {
            $queryEquipos->whereDate('fecha_registro', '<=', $request->fecha_hasta);
            $queryAccesos->whereDate('fecha_acceso', '<=', $request->fecha_hasta);
        }

        // Combinar resultados y ordenar por fecha
        $equipos = $queryEquipos->get()->map(function ($item) {
            $item->tipo = 'equipo';
            $item->fecha_orden = $item->fecha_registro;
            return $item;
        });

        $accesos = $queryAccesos->get()->map(function ($item) {
            $item->tipo = 'contrasena';
            $item->fecha_orden = $item->fecha_acceso;
            return $item;
        });

        $bitacoras = $equipos->concat($accesos)->sortByDesc('fecha_orden')->values();

        // Paginación manual
        $page = request()->get('page', 1);
        $perPage = 20;
        $total = $bitacoras->count();
        $bitacoras = $bitacoras->forPage($page, $perPage);
        $bitacoras = new \Illuminate\Pagination\LengthAwarePaginator(
            $bitacoras,
            $total,
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('admin.bitacora.index', compact('bitacoras'));
    }

    public function show($id)
    {
        $bitacora = BitacoraEquipo::with(['equipo', 'usuario'])->findOrFail($id);
        return view('admin.bitacora.show', compact('bitacora'));
    }
}