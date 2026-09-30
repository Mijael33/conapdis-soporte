<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PlantillaEquiposExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'codigo_inventario', 'serial_chasis', 'tipo_equipo', 'sede', 'marca', 'modelo',
            'estatus_general', 'usuario_nombre', 'usuario_cedula', 'usuario_cargo',
            'valor_prudencial', 'valor_adquisicion',
        ];
    }

    public function array(): array
    {
        return [
            ['CONAPDIS-PC-001', 'CHS-001', 'PC de Escritorio', 'Sede Central CONAPDIS', 'Dell', 'OptiPlex 7080', 'Operativo', 'Juan Pérez', 'V-12345678', 'Analista', 1500.00, 1400.00],
            ['CONAPDIS-LAP-001', 'CHS-002', 'Laptop', 'Oficina Regional Carabobo', 'Lenovo', 'ThinkPad X1', 'En Mantenimiento', 'María Gómez', 'V-87654321', 'Coordinadora', 2200.00, ''],
            ['CONAPDIS-PC-002', 'CHS-003', 'PC de Escritorio', 'Sede Central CONAPDIS', 'HP', 'EliteDesk', 'Operativo', '', '', '', '', 1800.00],
        ];
    }
}