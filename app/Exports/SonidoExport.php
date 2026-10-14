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
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = EquipoSonido::with(['categoria', 'sede.estado']);

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
            $query->where('categoria_sonido_id', $request->categoria_id);
        }
        if ($request->filled('estatus')) {
            $query->where('estatus', $request->estatus);
        }

        // BÚSQUEDA AMPLIADA
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_inventario', 'ILIKE', "%{$search}%")
                  ->orWhere('serial', 'ILIKE', "%{$search}%")
                  ->orWhere('marca', 'ILIKE', "%{$search}%")
                  ->orWhere('modelo', 'ILIKE', "%{$search}%")
                  ->orWhere('potencia', 'ILIKE', "%{$search}%")
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
            'Serial',
            'Categoría',
            'Marca',
            'Modelo',
            'Potencia',
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
            $equipo->usuario_asignado_cedula,
            $equipo->usuario_asignado_cargo,
            $equipo->valor_prudencial,
            $equipo->valor_adquisicion,
        ];
    }
}