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

        // Solo aplicamos el filtro global cuando viene del dashboard
        // (con el hidden "aplicar_filtro_global=1"), o cuando el usuario
        // pide limpiar el filtro.
        if ($request->has('aplicar_filtro_global') || $request->has('limpiar_filtro')) {

            if ($request->has('limpiar_filtro')) {
                session()->forget(['filtro_estado_id', 'filtro_sede_id']);
            } else {
                // ESTADO
                if ($request->has('estado_id')) {
                    $valor = $request->input('estado_id');
                    if ($valor === '' || $valor === null) {
                        session()->forget('filtro_estado_id');
                        // Si limpian estado, limpiamos la sede también
                        session()->forget('filtro_sede_id');
                    } else {
                        session(['filtro_estado_id' => $valor]);
                    }
                }

                // SEDE
                if ($request->has('sede_id')) {
                    $valor = $request->input('sede_id');
                    if ($valor === '' || $valor === null) {
                        session()->forget('filtro_sede_id');
                    } else {
                        session(['filtro_sede_id' => $valor]);
                    }
                } else {
                    // Si cambiaron de estado pero NO enviaron sede_id, limpiamos la sede anterior
                    // para que se filtre solo por estado.
                    if ($request->has('estado_id') && $request->input('estado_id') !== '' && $request->input('estado_id') !== null) {
                        session()->forget('filtro_sede_id');
                    }
                }
            }

            return $next($request);
        }

        // Para usuarios SIN acceso nacional: forzamos siempre su sede/estado
        if (!$user->puedeVerNacional()) {
            session(['filtro_estado_id' => $user->estado_id]);
            session(['filtro_sede_id' => $user->sede_id]);
            return $next($request);
        }

        // Para usuarios CON acceso nacional: dejamos lo que haya en sesión
        // (si no hay nada, ven todo).
        return $next($request);
    }
}