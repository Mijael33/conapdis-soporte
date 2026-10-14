<?php

namespace App\Exports;

use App\Models\BienNacional;
use App\Models\Sede;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BienesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        $user = auth()->user();
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = BienNacional::with(['categoria', 'sede.estado']);

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
            $query->where('categoria_bien_id', $request->categoria_id);
        }
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        // BÚSQUEDA AMPLIADA
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_inventario', 'ILIKE', "%{$search}%")
                  ->orWhere('descripcion', 'ILIKE', "%{$search}%")
                  ->orWhere('marca', 'ILIKE', "%{$search}%")
                  ->orWhere('modelo', 'ILIKE', "%{$search}%")
                  ->orWhere('serial', 'ILIKE', "%{$search}%")
                  ->orWhere('color', 'ILIKE', "%{$search}%")
                  ->orWhere('material', 'ILIKE', "%{$search}%")
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
            'Categoría',
            'Descripción',
            'Marca',
            'Modelo',
            'Serial',
            'Color',
            'Material',
            'Estado',
            'Sede',
            'Estatus',
            'Usuario Asignado',
            'Cédula',
            'Cargo',
            'Valor Prudencial (Bs.)',
            'Valor Adquisición (Bs.)',
            'Fecha Adquisición',
        ];
    }

    public function map($bien): array
    {
        return [
            $bien->codigo_inventario,
            $bien->categoria ? $bien->categoria->nombre : 'N/A',
            $bien->descripcion,
            $bien->marca,
            $bien->modelo,
            $bien->serial,
            $bien->color,
            $bien->material,
            $bien->sede ? $bien->sede->estado->nombre : 'N/A',
            $bien->sede ? $bien->sede->nombre_sede : 'N/A',
            $bien->estatus,
            $bien->usuario_asignado_nombre,
            $bien->usuario_asignado_cedula,
            $bien->usuario_asignado_cargo,
            $bien->valor_prudencial,
            $bien->valor_adquisicion,
            $bien->fecha_adquisicion ? $bien->fecha_adquisicion->format('d/m/Y') : null,
        ];
    }
}