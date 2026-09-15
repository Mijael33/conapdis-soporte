<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PlantillaComponentesExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'categoria',
            'marca',
            'modelo',
            'serial_unico',
            'sede',
            'estatus',
            'observaciones',
        ];
    }

    public function array(): array
    {
        return [
            ['CPU', 'Intel', 'Core i7-14700K', 'CPU-INTEL-001', 'Sede Central CONAPDIS', 'Disponible', 'Ejemplo'],
            ['RAM', 'Corsair', 'Vengeance DDR5', 'RAM-COR-001', 'Sede Central CONAPDIS', 'Disponible', 'Ejemplo'],
            ['Almacenamiento', 'Samsung', '990 Pro', 'SSD-SAM-001', 'Oficina Regional Carabobo', 'Disponible', ''],
        ];
    }
}