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
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = BienNacional::with(['categoria', 'sede.estado']);

        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        return $query->orderBy('codigo_inventario')->get();
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
            'Valor Adquisición',
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
            $bien->valor_adquisicion,
        ];
    }
}