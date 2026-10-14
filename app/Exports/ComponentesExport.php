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
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = Componente::with(['categoria', 'sede.estado', 'equipoActual']);

        // FILTRO GLOBAL
        if (!$user->puedeVerNacional()) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        // Filtros del request (por si el index los envía)
        $request = request();

        if ($request->filled('categoria_id')) {
            $query->where('categoria_componente_id', $request->categoria_id);
        }
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        // BÚSQUEDA AMPLIADA
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('serial_unico', 'ILIKE', "%{$search}%")
                  ->orWhere('marca', 'ILIKE', "%{$search}%")
                  ->orWhere('modelo', 'ILIKE', "%{$search}%")
                  ->orWhere('estatus', 'ILIKE', "%{$search}%")
                  ->orWhere('observaciones', 'ILIKE', "%{$search}%")
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

        // Orden: más antiguo primero (orden de importación)
        return $query->orderBy('id', 'asc')->get();
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
            'Valor Prudencial (Bs.)',
            'Valor Adquisición (Bs.)',
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
            $componente->valor_prudencial,
            $componente->valor_adquisicion,
        ];
    }
}