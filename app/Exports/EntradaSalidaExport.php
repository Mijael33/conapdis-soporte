<?php

namespace App\Exports;

use App\Models\RegistroEntradaSalida;
use App\Models\Sede;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class EntradaSalidaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = RegistroEntradaSalida::with(['sede.estado', 'usuario']);

        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Tipo',
            'Bien',
            'Código Inventario',
            'Descripción',
            'Sede',
            'Estado',
            'Fecha Salida',
            'Fecha Entrada',
            'Retirado por',
            'Cédula Retira',
            'Autorizado por',
            'Motivo',
            'Destino',
            'Seguridad Salida',
            'Seguridad Entrada',
            'Estado Salida',
            'Estado Entrada',
            'Registrado por',
        ];
    }

    public function map($reg): array
    {
        return [
            $reg->id,
            $reg->tipo,
            $reg->bien_tipo,
            $reg->codigo_inventario,
            $reg->descripcion_bien,
            $reg->sede ? $reg->sede->nombre_sede : 'N/A',
            $reg->sede ? $reg->sede->estado->nombre : 'N/A',
            $reg->fecha_hora_salida ? $reg->fecha_hora_salida->format('d/m/Y H:i') : '',
            $reg->fecha_hora_entrada ? $reg->fecha_hora_entrada->format('d/m/Y H:i') : '',
            $reg->persona_retira_nombre,
            $reg->persona_retira_cedula,
            $reg->autorizado_por_nombre,
            $reg->motivo,
            $reg->destino,
            $reg->seguridad_salida_nombre,
            $reg->seguridad_entrada_nombre,
            $reg->estado_salida,
            $reg->estado_entrada,
            $reg->usuario ? $reg->usuario->name : 'N/A',
        ];
    }
}