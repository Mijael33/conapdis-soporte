<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class ValorizacionExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function headings(): array
    {
        return ['Módulo', 'Cantidad', 'Valor Prudencial (Bs.)', 'Valor Adquisición (Bs.)'];
    }

    public function array(): array
    {
        $rows = [];

        $rows[] = [
            'Equipos de Tecnología',
            $this->data['equipos']['cantidad'],
            number_format($this->data['equipos']['suma_prudencial'], 2, ',', '.'),
            number_format($this->data['equipos']['suma_adquisicion'], 2, ',', '.'),
        ];

        $rows[] = [
            'Componentes',
            $this->data['componentes']['cantidad'],
            number_format($this->data['componentes']['suma_prudencial'], 2, ',', '.'),
            number_format($this->data['componentes']['suma_adquisicion'], 2, ',', '.'),
        ];

        $rows[] = [
            'Bienes Nacionales',
            $this->data['bienes']['cantidad'],
            number_format($this->data['bienes']['suma_prudencial'], 2, ',', '.'),
            number_format($this->data['bienes']['suma_adquisicion'], 2, ',', '.'),
        ];

        $rows[] = [
            'Vehículos',
            $this->data['vehiculos']['cantidad'],
            number_format($this->data['vehiculos']['suma_prudencial'], 2, ',', '.'),
            number_format($this->data['vehiculos']['suma_adquisicion'], 2, ',', '.'),
        ];

        $rows[] = [
            'Equipos de Sonido',
            $this->data['sonido']['cantidad'],
            number_format($this->data['sonido']['suma_prudencial'], 2, ',', '.'),
            number_format($this->data['sonido']['suma_adquisicion'], 2, ',', '.'),
        ];

        $rows[] = ['', '', '', ''];

        $rows[] = [
            'TOTAL GENERAL',
            $this->data['totales']['cantidad'],
            number_format($this->data['totales']['suma_prudencial'], 2, ',', '.'),
            number_format($this->data['totales']['suma_adquisicion'], 2, ',', '.'),
        ];

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '003097']],
            ],
            7 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
            ],
        ];
    }
}