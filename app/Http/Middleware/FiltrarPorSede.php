<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FiltrarPorSede
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        
        if (!$user) {
            return $next($request);
        }

        if ($user->hasRole('Administrador') || $user->hasRole('Auditor')) {
            if ($request->has('estado_id')) {
                session(['filtro_estado_id' => $request->estado_id]);
            }
            if ($request->has('sede_id')) {
                session(['filtro_sede_id' => $request->sede_id]);
            }
            if ($request->has('limpiar_filtro')) {
                session()->forget(['filtro_estado_id', 'filtro_sede_id']);
            }
        } else {
            session(['filtro_estado_id' => $user->estado_id]);
            session(['filtro_sede_id' => $user->sede_id]);
        }

        return $next($request);
    }
}