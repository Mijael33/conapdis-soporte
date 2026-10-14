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
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = Vehiculo::with(['categoria', 'sede.estado']);

        // FILTRO GLOBAL
        if (!$user->puedeVerNacional()) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        // Filtros del request
        $request = request();

        if ($request->filled('categoria_id')) {
            $query->where('categoria_vehiculo_id', $request->categoria_id);
        }
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        // BÚSQUEDA AMPLIADA
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_inventario', 'ILIKE', "%{$search}%")
                  ->orWhere('placa', 'ILIKE', "%{$search}%")
                  ->orWhere('marca', 'ILIKE', "%{$search}%")
                  ->orWhere('modelo', 'ILIKE', "%{$search}%")
                  ->orWhere('color', 'ILIKE', "%{$search}%")
                  ->orWhere('serial_motor', 'ILIKE', "%{$search}%")
                  ->orWhere('serial_carroceria', 'ILIKE', "%{$search}%")
                  ->orWhere('estatus', 'ILIKE', "%{$search}%")
                  ->orWhere('usuario_asignado_nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('usuario_asignado_cedula', 'ILIKE', "%{$search}%")
                  ->orWhere('usuario_asignado_cargo', 'ILIKE', "%{$search}%")
                  ->orWhereHas('categoria', function ($q2) use ($search) {
                      $q2->where('nombre', 'ILIKE', "%{$search}%");
                  })
                  ->orWhereHas('sede', function ($q2) use ($search) {
                      $q2->where('nombre_sede', 'ILIKE', "%{$search}%")
                         ->orWhereHas('estado', function ($q3) use ($search) {
                             $q3->where('nombre', 'ILIKE', "%{$search}%");
                         });
                  });
            });
        }

        // Orden: más antiguo primero
        return $query->orderBy('id', 'asc')->get();
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
            'Serial Carrocería',
            'Kilometraje',
            'Estado',
            'Sede',
            'Estatus',
            'Usuario Asignado',
            'Cédula',
            'Cargo',
            'Valor Prudencial (Bs.)',
            'Valor Adquisición (Bs.)',
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
            $vehiculo->serial_carroceria,
            $vehiculo->kilometraje,
            $vehiculo->sede ? $vehiculo->sede->estado->nombre : 'N/A',
            $vehiculo->sede ? $vehiculo->sede->nombre_sede : 'N/A',
            $vehiculo->estatus,
            $vehiculo->usuario_asignado_nombre,
            $vehiculo->usuario_asignado_cedula,
            $vehiculo->usuario_asignado_cargo,
            $vehiculo->valor_prudencial,
            $vehiculo->valor_adquisicion,
        ];
    }
}