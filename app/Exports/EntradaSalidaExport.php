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

        $query = RegistroEntradaSalida::with(['sede.estado', 'salidaUsuario', 'entradaUsuario']);

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
            'N° Comprobante',
            'Bien Tipo',
            'Código Bien',
            'Descripción Bien',
            'Sede',
            'Estado',
            'Estatus Movimiento',
            'Fecha Salida',
            'Autorizado por',
            'Cédula Autoriza',
            'Cargo Autoriza',
            'Retirado por',
            'Cédula Retira',
            'Cargo Retira',
            'Motivo',
            'Destino',
            'Seguridad Salida',
            'Cédula Seguridad Salida',
            'Estado Bien al Salir',
            'Observaciones Salida',
            'Usuario Registró Salida',
            'Fecha Entrada',
            'Entregado por',
            'Cédula Entrega',
            'Seguridad Entrada',
            'Cédula Seguridad Entrada',
            'Estado Bien al Entrar',
            'Observaciones Entrada',
            'Usuario Registró Entrada',
            'Duración',
        ];
    }

    public function map($reg): array
    {
        return [
            $reg->numero_comprobante,
            $reg->bien_tipo,
            $reg->bien_codigo,
            $reg->bien_descripcion,
            $reg->sede->nombre_sede ?? 'N/A',
            $reg->sede->estado->nombre ?? 'N/A',
            $reg->estatus,
            $reg->fecha_hora_salida ? \App\Helpers\FechaHelper::formatear($reg->fecha_hora_salida) : '',
            $reg->salida_autoriza_nombre,
            $reg->salida_autoriza_cedula,
            $reg->salida_autoriza_cargo,
            $reg->salida_retira_nombre,
            $reg->salida_retira_cedula,
            $reg->salida_retira_cargo,
            $reg->salida_motivo,
            $reg->salida_destino,
            $reg->salida_seguridad_nombre,
            $reg->salida_seguridad_cedula,
            $reg->salida_estado_bien,
            $reg->salida_observaciones,
            $reg->salidaUsuario->name ?? 'N/A',
            $reg->fecha_hora_entrada ? \App\Helpers\FechaHelper::formatear($reg->fecha_hora_entrada) : '',
            $reg->entrada_recibe_nombre,
            $reg->entrada_recibe_cedula,
            $reg->entrada_seguridad_nombre,
            $reg->entrada_seguridad_cedula,
            $reg->entrada_estado_bien,
            $reg->entrada_observaciones,
            $reg->entradaUsuario->name ?? 'N/A',
            $reg->duracion ?? '-',
        ];
    }
}