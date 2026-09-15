<?php

namespace App\Exports;

use App\Models\Vehiculo;
use App\Models\Sede;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class VehiculosExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = Vehiculo::with(['categoria', 'sede.estado']);

        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        return $query->orderBy('placa')->get();
    }

    public function headings(): array
    {
        return [
            'Código Inventario',
            'Placa',
            'Categoría',
            'Marca',
            'Modelo',
            'Año',
            'Color',
            'Serial Motor',
            'Serial Chasis',
            'Kilometraje',
            'Estado',
            'Sede',
            'Estatus',
            'Usuario Asignado',
        ];
    }

    public function map($vehiculo): array
    {
        return [
            $vehiculo->codigo_inventario,
            $vehiculo->placa,
            $vehiculo->categoria ? $vehiculo->categoria->nombre : 'N/A',
            $vehiculo->marca,
            $vehiculo->modelo,
            $vehiculo->anio,
            $vehiculo->color,
            $vehiculo->serial_motor,
            $vehiculo->serial_chasis,
            $vehiculo->kilometraje,
            $vehiculo->sede ? $vehiculo->sede->estado->nombre : 'N/A',
            $vehiculo->sede ? $vehiculo->sede->nombre_sede : 'N/A',
            $vehiculo->estatus,
            $vehiculo->usuario_asignado_nombre,
        ];
    }
}