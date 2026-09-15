<?php

namespace App\Exports;

use App\Models\EquipoSonido;
use App\Models\Sede;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class SonidoExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = EquipoSonido::with(['categoria', 'sede.estado']);

        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        return $query->orderBy('serial')->get();
    }

    public function headings(): array
    {
        return [
            'Código Inventario',
            'Serial',
            'Categoría',
            'Marca',
            'Modelo',
            'Potencia',
            'Estado',
            'Sede',
            'Estatus',
            'Usuario Asignado',
        ];
    }

    public function map($equipo): array
    {
        return [
            $equipo->codigo_inventario,
            $equipo->serial,
            $equipo->categoria ? $equipo->categoria->nombre : 'N/A',
            $equipo->marca,
            $equipo->modelo,
            $equipo->potencia,
            $equipo->sede ? $equipo->sede->estado->nombre : 'N/A',
            $equipo->sede ? $equipo->sede->nombre_sede : 'N/A',
            $equipo->estatus,
            $equipo->usuario_asignado_nombre,
        ];
    }
}