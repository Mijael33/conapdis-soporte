<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PlantillaSonidoExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'codigo_inventario', 'categoria', 'sede', 'marca', 'modelo',
            'serial', 'potencia', 'estatus',
            'usuario_asignado_nombre', 'usuario_asignado_cedula', 'usuario_asignado_cargo',
            'valor_prudencial', 'valor_adquisicion', 'observaciones',
        ];
    }

    public function array(): array
    {
        return [
            ['SON-001', 'Parlante', 'Sede Central CONAPDIS', 'JBL', 'EON615', 'PAR-001', '1000W', 'Disponible', 'Juan Pérez', 'V-12345678', 'Analista', 2500.00, 2300.00, 'Ejemplo'],
            ['SON-002', 'Micrófono', 'Oficina Regional Carabobo', 'Shure', 'SM58', 'MIC-001', 'N/A', 'Asignado', 'María Gómez', 'V-87654321', 'Coordinadora', 500.00, '', ''],
            ['SON-003', 'Consola', 'Sede Central CONAPDIS', 'Yamaha', 'MG10XU', 'CON-001', 'N/A', 'Disponible', '', '', '', '', 900.00, 'Ejemplo solo valor adq.'],
        ];
    }
}