<?php

namespace App\Traits;

trait FiltroSedeTrait
{
    /**
     * Determina si el usuario actual puede ver todo a nivel nacional.
     *
     * Usa User::puedeVerNacional() que cubre:
     *  - Admin (por lista config en config/institucional.php)
     *  - Admin por tener todos los permisos
     *  - Auditor
     *  - Cualquier rol con permiso 'acceso.nacional'
     */
    protected function usuarioPuedeVerNacional(): bool
    {
        $user = auth()->user();
        return $user && $user->puedeVerNacional();
    }

    /**
     * Aplica el filtro global de sede/estado a cualquier query.
     *
     * Uso:
     *   $query = $this->filtrarPorSede($query);
     *
     * Funciona con modelos que tengan columna `sede_id` directa.
     */
    protected function filtrarPorSede($query)
    {
        $user = auth()->user();
        $puedeVerNacional = $this->usuarioPuedeVerNacional();

        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        // Usuarios con acceso nacional (admin, auditor o con permiso):
        // pueden filtrar por cualquier sede/estado o ver todo si no filtran.
        if ($puedeVerNacional) {
            if ($sedeId) {
                $query->where('sede_id', $sedeId);
            } elseif ($estadoId) {
                $sedeIds = \App\Models\Sede::where('estado_id', $estadoId)->pluck('id');
                $query->whereIn('sede_id', $sedeIds);
            }
            // Si no hay filtro en sesión, ve todo
        } else {
            // Usuarios sin acceso nacional: SOLO ven su sede (forzado).
            if ($sedeId) {
                $query->where('sede_id', $sedeId);
            } elseif ($user && $user->sede_id) {
                $query->where('sede_id', $user->sede_id);
            }
        }

        return $query;
    }

    /**
     * Aplica el filtro de sede a una query de Órdenes de Servicio.
     * Las órdenes se relacionan con equipos, que tienen sede_id directo.
     */
    protected function filtrarOrdenesPorSede($query)
    {
        $user = auth()->user();
        $puedeVerNacional = $this->usuarioPuedeVerNacional();

        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        if ($puedeVerNacional) {
            if ($sedeId) {
                $query->whereHas('equipo', function ($q) use ($sedeId) {
                    $q->where('sede_id', $sedeId);
                });
            } elseif ($estadoId) {
                $sedeIds = \App\Models\Sede::where('estado_id', $estadoId)->pluck('id');
                $query->whereHas('equipo', function ($q) use ($sedeIds) {
                    $q->whereIn('sede_id', $sedeIds);
                });
            }
        } else {
            if ($sedeId) {
                $query->whereHas('equipo', function ($q) use ($sedeId) {
                    $q->where('sede_id', $sedeId);
                });
            } elseif ($user && $user->sede_id) {
                $query->whereHas('equipo', function ($q) use ($user) {
                    $q->where('sede_id', $user->sede_id);
                });
            }
        }

        return $query;
    }
}