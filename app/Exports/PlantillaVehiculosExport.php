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
            'serial_motor', 'serial_carroceria', 'kilometraje',
            'estatus',
            'usuario_asignado_nombre', 'usuario_asignado_cedula', 'usuario_asignado_cargo',
            'valor_prudencial', 'valor_adquisicion', 'observaciones',
        ];
    }

    public function array(): array
    {
        return [
            ['VEH-001', 'Sedán', 'Sede Central CONAPDIS', 'ABC123', 'Toyota', 'Corolla', 2023, 'Blanco', 'MOT-001', 'CAR-001', 15000, 'Disponible', 'Juan Pérez', 'V-12345678', 'Analista', 250000.00, 230000.00, 'Ejemplo'],
            ['VEH-002', 'Camioneta', 'Oficina Regional Carabobo', 'XYZ456', 'Ford', 'Ranger', 2022, 'Gris', 'MOT-002', 'CAR-002', 25000, 'Asignado', 'María Gómez', 'V-87654321', 'Coordinadora', 350000.00, '', ''],
            ['VEH-003', 'Moto', 'Sede Central CONAPDIS', 'ABC789', 'Bajaj', 'Boxer', 2023, 'Negro', 'MOT-003', 'CAR-003', 5000, 'Disponible', '', '', '', '', 3500.00, 'Ejemplo solo valor adq.'],
        ];
    }
}