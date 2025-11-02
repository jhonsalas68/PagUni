<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CargaHorariaExport implements FromArray, WithHeadings, WithStyles, WithTitle
{
    protected $reporte;
    protected $periodo;

    public function __construct($reporte, $periodo)
    {
        $this->reporte = $reporte;
        $this->periodo = $periodo;
    }

    public function array(): array
    {
        return $this->reporte;
    }

    public function headings(): array
    {
        return [
            'Docente',
            'Materia',
            'Grupo',
            'Horas Asignadas',
            'Horas Impartidas',
            'Porcentaje Cumplimiento (%)'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'F:F' => ['numberFormat' => ['formatCode' => '0.00"%"']],
        ];
    }

    public function title(): string
    {
        return 'Carga Horaria ' . $this->periodo;
    }
}