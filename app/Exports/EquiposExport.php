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
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = Equipo::with(['tipoEquipo', 'sede.estado', 'componentes']);

        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        if ($this->request->filled('estatus')) {
            $query->where('estatus_general', $this->request->estatus);
        }
        if ($this->request->filled('sede_id')) {
            $query->where('sede_id', $this->request->sede_id);
        }
        if ($this->request->filled('search')) {
            $search = $this->request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_inventario_institucional', 'ILIKE', "%{$search}%")
                  ->orWhere('serial_chasis', 'ILIKE', "%{$search}%")
                  ->orWhere('marca', 'ILIKE', "%{$search}%")
                  ->orWhere('modelo', 'ILIKE', "%{$search}%")
                  ->orWhere('usuario_asignado_nombre', 'ILIKE', "%{$search}%")
                  ->orWhere('usuario_asignado_cedula', 'ILIKE', "%{$search}%");
            });
        }

        return $query->orderBy('codigo_inventario_institucional')->get();
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