<?php

namespace App\Exports;

use App\Models\Equipo;
use App\Models\Sede;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EquiposExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = Equipo::with(['tipoEquipo', 'departamento.sede.estado', 'componentes']);

        if (!$esAdmin && !$esAuditor) {
            $query->whereHas('departamento.sede', fn($q) => $q->where('id', $user->sede_id));
        } elseif ($sedeId) {
            $query->whereHas('departamento.sede', fn($q) => $q->where('id', $sedeId));
        } elseif ($estadoId) {
            $query->whereHas('departamento.sede', fn($q) => $q->where('estado_id', $estadoId));
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
            'Departamento',
            'Estatus',
            'Usuario Asignado',
            'Cédula',
            'Cargo',
            'Componentes Instalados',
        ];
    }

    public function map($equipo): array
    {
        $componentes = $equipo->componentes->map(fn($c) => $c->marca . ' ' . $c->modelo . ' (' . $c->serial_unico . ')')->implode('; ');

        return [
            $equipo->codigo_inventario_institucional,
            $equipo->serial_chasis,
            $equipo->tipoEquipo->nombre,
            $equipo->marca,
            $equipo->modelo,
            $equipo->departamento->sede->estado->nombre,
            $equipo->departamento->sede->nombre_sede,
            $equipo->departamento->nombre_departamento,
            $equipo->estatus_general,
            $equipo->usuario_asignado_nombre,
            $equipo->usuario_asignado_cedula,
            $equipo->usuario_asignado_cargo,
            $componentes,
        ];
    }
}