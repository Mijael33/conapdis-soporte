<?php

namespace App\Exports;

use App\Models\Componente;
use App\Models\Sede;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ComponentesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = Componente::with(['categoria', 'sede.estado', 'equipoActual']);

        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        return $query->orderBy('serial_unico')->get();
    }

    public function headings(): array
    {
        return [
            'Serial Único',
            'Categoría',
            'Marca',
            'Modelo',
            'Estado',
            'Sede',
            'Estatus',
            'Equipo Actual',
            'Observaciones',
        ];
    }

    public function map($componente): array
    {
        $equipoActual = '';
        if ($componente->equipoActual->isNotEmpty()) {
            $equipoActual = $componente->equipoActual->first()->codigo_inventario_institucional;
        }

        return [
            $componente->serial_unico,
            $componente->categoria ? $componente->categoria->nombre : 'N/A',
            $componente->marca,
            $componente->modelo,
            $componente->sede ? $componente->sede->estado->nombre : 'N/A',
            $componente->sede ? $componente->sede->nombre_sede : 'N/A',
            $componente->estatus,
            $equipoActual,
            $componente->observaciones,
        ];
    }
}