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
            'categoria', 'marca', 'modelo', 'serial_unico', 'sede',
            'estatus', 'valor_prudencial', 'valor_adquisicion', 'observaciones',
        ];
    }

    public function array(): array
    {
        return [
            ['CPU', 'Intel', 'Core i7-14700K', 'CPU-INTEL-001', 'Sede Central CONAPDIS', 'Disponible', 3500.00, 3200.00, 'Ejemplo'],
            ['RAM', 'Corsair', 'Vengeance DDR5', 'RAM-COR-001', 'Sede Central CONAPDIS', 'Disponible', 1200.00, '', 'Ejemplo'],
            ['Almacenamiento', 'Samsung', '990 Pro', 'SSD-SAM-001', 'Oficina Regional Carabobo', 'Disponible', '', 900.00, ''],
        ];
    }
}