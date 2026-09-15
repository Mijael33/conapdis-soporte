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
            'codigo_inventario',
            'serial_chasis',
            'tipo_equipo',
            'departamento',
            'sede',
            'marca',
            'modelo',
            'usuario_nombre',
            'usuario_cedula',
            'usuario_cargo',
        ];
    }

    public function array(): array
    {
        return [
            ['CONAPDIS-PC-001', 'CHS-001', 'PC de Escritorio', 'Dirección de Tecnología', 'Sede Central CONAPDIS', 'Dell', 'OptiPlex 7080', 'Juan Pérez', 'V-12345678', 'Analista'],
            ['CONAPDIS-LAP-001', 'CHS-002', 'Laptop', 'Recursos Humanos', 'Oficina Regional Carabobo', 'Lenovo', 'ThinkPad X1', 'María Gómez', 'V-87654321', 'Coordinadora'],
        ];
    }
}