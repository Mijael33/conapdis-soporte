<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PlantillaBienesExport implements FromArray, WithHeadings, ShouldAutoSize
{
    public function headings(): array
    {
        return [
            'codigo_inventario', 'categoria', 'sede', 'descripcion',
            'marca', 'modelo', 'serial', 'color', 'material',
            'estatus', 'usuario_asignado', 'cedula_asignado', 'cargo_asignado',
            'valor_adquisicion', 'fecha_adquisicion', 'observaciones',
        ];
    }

    public function array(): array
    {
        return [
            ['BIEN-001', 'Escritorio', 'Sede Central CONAPDIS', 'Escritorio ejecutivo de madera', 'Genérico', 'E-2000', 'ESC-001', 'Marrón', 'Madera', 'Disponible', 'Juan Pérez', 'V-12345678', 'Analista', 1500.00, '2024-01-15', 'Ejemplo'],
            ['BIEN-002', 'Silla', 'Oficina Regional Carabobo', 'Silla ergonómica con apoyabrazos', 'Herman Miller', 'Aeron', 'SIL-001', 'Negro', 'Malla', 'Asignado', 'María Gómez', 'V-87654321', 'Coordinadora', 800.00, '2024-02-20', ''],
        ];
    }
}