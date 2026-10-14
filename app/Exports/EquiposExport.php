<?php

namespace App\Exports;

use App\Models\Equipo;
use App\Models\Sede;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EquiposExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $user = auth()->user();
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = Equipo::with(['tipoEquipo', 'sede.estado', 'componentes']);

        // FILTRO GLOBAL (Sede/Estado) - consistente con el index
        if (!$user->puedeVerNacional()) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        // FILTROS LOCALES del index (si vienen del request)
        if ($this->request->filled('estatus')) {
            $query->where('estatus_general', $this->request->estatus);
        }
        if ($this->request->filled('sede_id')) {
            $query->where('sede_id', $this->request->sede_id);
        }

        // BÚSQUEDA AMPLIADA (mismo criterio que el index)
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_inventario_institucional', 'ILIKE', "%{$search}%")
                  ->orWhere('serial_chasis', 'ILIKE', "%{$search}%")
                  ->orWhere('marca', 'ILIKE', "%{$search}%")
                  ->orWhere('modelo', 'ILIKE', "%{$search}%")
                  ->orWhere('estatus_general', 'ILIKE', "%{$search}%")
                  ->orWhere('usuario_asignado_nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('usuario_asignado_cedula', 'ILIKE', "%{$search}%")
                  ->orWhere('usuario_asignado_cargo', 'ILIKE', "%{$search}%")
                  ->orWhereHas('tipoEquipo', function ($q2) use ($search) {
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

        // Orden: mismo criterio del index (más reciente primero)
        return $query->orderBy('id', 'asc')->get();
    }

    public function headings(): array
    {
        return [
            'Código Inventario',
            'Serial Chasis',
            'Tipo',
            'Marca',
            'Modelo',
            'Estado',
            'Sede',
            'Estatus',
            'Usuario Asignado',
            'Cédula',
            'Cargo',
            'Componentes Instalados',
            'Valor Prudencial (Bs.)',
            'Valor Adquisición (Bs.)',
        ];
    }

    public function map($equipo): array
    {
        $componentes = $equipo->componentes->map(fn($c) => $c->marca . ' ' . $c->modelo . ' (' . $c->serial_unico . ')')->implode('; ');

        return [
            $equipo->codigo_inventario_institucional,
            $equipo->serial_chasis,
            $equipo->tipoEquipo->nombre ?? 'N/A',
            $equipo->marca,
            $equipo->modelo,
            $equipo->sede->estado->nombre ?? 'N/A',
            $equipo->sede->nombre_sede ?? 'N/A',
            $equipo->estatus_general,
            $equipo->usuario_asignado_nombre,
            $equipo->usuario_asignado_cedula,
            $equipo->usuario_asignado_cargo,
            $componentes,
            $equipo->valor_prudencial,
            $equipo->valor_adquisicion,
        ];
    }
}