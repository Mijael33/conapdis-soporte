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
            'serial', 'potencia', 'estatus', 'observaciones',
        ];
    }

    public function array(): array
    {
        return [
            ['SON-001', 'Parlante', 'Sede Central CONAPDIS', 'JBL', 'EON615', 'PAR-001', '1000W', 'Disponible', 'Ejemplo'],
            ['SON-002', 'Micrófono', 'Oficina Regional Carabobo', 'Shure', 'SM58', 'MIC-001', 'N/A', 'Asignado', ''],
        ];
    }
}