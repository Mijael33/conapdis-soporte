<?php

namespace App\Traits;

trait FiltroSedeTrait
{
    /**
     * Aplica el filtro de sede a una query de Equipos.
     */
    protected function filtrarPorSede($query)
    {
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = \App\Models\Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        return $query;
    }

    /**
     * Aplica el filtro de sede a una query de Órdenes de Servicio.
     * Las órdenes se relacionan con equipos, que ahora tienen sede_id directo.
     */
    protected function filtrarOrdenesPorSede($query)
    {
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

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

        return $query;
    }
}