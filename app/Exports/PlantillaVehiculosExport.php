<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PlantillaVehiculosExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'codigo_inventario', 'categoria', 'sede', 'placa',
            'marca', 'modelo', 'anio', 'color',
            'serial_motor', 'serial_chasis', 'kilometraje',
            'estatus', 'observaciones',
        ];
    }

    public function array(): array
    {
        return [
            ['VEH-001', 'Sedán', 'Sede Central CONAPDIS', 'ABC123', 'Toyota', 'Corolla', 2023, 'Blanco', 'MOT-001', 'CHS-001', 15000, 'Disponible', 'Ejemplo'],
            ['VEH-002', 'Camioneta', 'Oficina Regional Carabobo', 'XYZ456', 'Ford', 'Ranger', 2022, 'Gris', 'MOT-002', 'CHS-002', 25000, 'Asignado', ''],
        ];
    }
}