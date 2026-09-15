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
            $query->whereHas('departamento.sede', function ($q) use ($sedeId) {
                $q->where('id', $sedeId);
            });
        } elseif ($estadoId) {
            $query->whereHas('departamento.sede', function ($q) use ($estadoId) {
                $q->where('estado_id', $estadoId);
            });
        }

        return $query;
    }

    /**
     * Aplica el filtro de sede a una query de Órdenes de Servicio.
     */
    protected function filtrarOrdenesPorSede($query)
    {
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        if ($sedeId) {
            $query->whereHas('equipo.departamento.sede', function ($q) use ($sedeId) {
                $q->where('id', $sedeId);
            });
        } elseif ($estadoId) {
            $query->whereHas('equipo.departamento.sede', function ($q) use ($estadoId) {
                $q->where('estado_id', $estadoId);
            });
        }

        return $query;
    }
}