<?php

namespace App\Exports;

use App\Models\Grupo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RendimientoExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithCustomStartCell
{
    protected $materiaId;
    protected $grupoId;
    protected $estudiantes;
    protected $estadisticas;
    protected $materia;
    protected $grupo;

    public function __construct($materiaId, $grupoId)
    {
        $this->materiaId = $materiaId;
        $this->grupoId = $grupoId;
        $this->loadData();
    }

    protected function loadData()
    {
        $this->estudiantes = [];
        $this->estadisticas = [
            'total' => 0,
            'aprobados' => 0,
            'reprobados' => 0,
            'promedio_general' => 0
        ];

        if ($this->grupoId) {
            $grupoResult = Grupo::with(['materia', 'inscripciones' => function($query) {
                $query->where('estado', 'activo')->with(['estudiante', 'calificaciones.tipoEvaluacion']);
            }])->find($this->grupoId);
                
            if ($grupoResult && $grupoResult->inscripciones) {
                $this->materia = $grupoResult->materia;
                $this->grupo = $grupoResult;
                $totalNotas = 0;
                
                foreach ($grupoResult->inscripciones as $inscripcion) {
                    if (!$inscripcion->estudiante) continue;
                    
                    $notaFinal = 0;
                    $acumulado = 0;
                    
                    if ($inscripcion->calificaciones && $inscripcion->calificaciones->count() > 0) {
                        foreach ($inscripcion->calificaciones as $cal) {
                            if (!$cal->tipoEvaluacion) continue;
                            
                            $ponderacion = $cal->tipoEvaluacion->ponderacion ?? 0;
                            $nota = $cal->nota ?? 0;
                            
                            $puntos = ($nota / 100) * $ponderacion;
                            $acumulado += $puntos;
                        }
                    }
                    
                    $notaFinal = round($acumulado, 2);
                    $estado = $notaFinal >= 51 ? 'APROBADO' : 'REPROBADO';
                    
                    $this->estudiantes[] = [
                        'codigo' => $inscripcion->estudiante->codigo_estudiante,
                        'nombres' => $inscripcion->estudiante->nombre . ' ' . $inscripcion->estudiante->apellido,
                        'nota_final' => $notaFinal,
                        'estado' => $estado
                    ];
                    
                    if ($estado === 'APROBADO') $this->estadisticas['aprobados']++;
                    else $this->estadisticas['reprobados']++;
                    
                    $totalNotas += $notaFinal;
                }
                
                $this->estadisticas['total'] = count($this->estudiantes);
                if ($this->estadisticas['total'] > 0) {
                    $this->estadisticas['promedio_general'] = round($totalNotas / $this->estadisticas['total'], 2);
                }
            }
        }
    }

    public function collection()
    {
        $data = collect();
        
        // Información del reporte
        $data->push(['REPORTE DE RENDIMIENTO ACADÉMICO']);
        $data->push(['Universidad Autónoma Gabriel René Moreno']);
        $data->push(['Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones']);
        $data->push(['']);
        
        // Información de la materia
        $data->push(['Materia:', $this->materia ? $this->materia->nombre . ' (' . $this->materia->codigo . ')' : 'N/A']);
        $data->push(['Grupo:', $this->grupo ? 'Grupo ' . $this->grupo->identificador : 'N/A']);
        $data->push(['Fecha:', now()->format('d/m/Y H:i:s')]);
        $data->push(['Periodo:', '2024-2']);
        $data->push(['']);
        
        // Estadísticas
        $data->push(['ESTADÍSTICAS GENERALES']);
        $data->push(['Total Estudiantes:', $this->estadisticas['total']]);
        $data->push(['Aprobados:', $this->estadisticas['aprobados']]);
        $data->push(['Reprobados:', $this->estadisticas['reprobados']]);
        $data->push(['Promedio General:', $this->estadisticas['promedio_general']]);
        $data->push(['']);
        
        // Encabezados de la tabla
        $data->push(['DETALLE DE ESTUDIANTES']);
        $data->push(['']);
        
        // Datos de estudiantes
        foreach ($this->estudiantes as $estudiante) {
            $data->push([
                $estudiante['codigo'],
                $estudiante['nombres'],
                $estudiante['nota_final'],
                $estudiante['estado']
            ]);
        }
        
        return $data;
    }

    public function headings(): array
    {
        return [
            'Código',
            'Estudiante',
            'Nota Final',
            'Estado'
        ];
    }

    public function startCell(): string
    {
        return 'A18'; // Los datos de estudiantes empiezan en la fila 18
    }

    public function styles(Worksheet $sheet)
    {
        // Título principal
        $sheet->mergeCells('A1:D1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A1')->getFont()->getColor()->setRGB('FFFFFF');
        
        // Subtítulos
        $sheet->mergeCells('A2:D2');
        $sheet->mergeCells('A3:D3');
        $sheet->getStyle('A2:A3')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A2:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Información de la materia
        $sheet->getStyle('A5:A8')->getFont()->setBold(true);
        
        // Estadísticas
        $sheet->mergeCells('A10:D10');
        $sheet->getStyle('A10')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A10')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A10')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E7E6E6');
        
        $sheet->getStyle('A11:A14')->getFont()->setBold(true);
        
        // Título de la tabla
        $sheet->mergeCells('A16:D16');
        $sheet->getStyle('A16')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A16')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A16')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('E7E6E6');
        
        // Encabezados de la tabla
        $sheet->getStyle('A18:D18')->getFont()->setBold(true);
        $sheet->getStyle('A18:D18')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('4472C4');
        $sheet->getStyle('A18:D18')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A18:D18')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Bordes para la tabla de datos
        $lastRow = 18 + count($this->estudiantes);
        $sheet->getStyle("A18:D{$lastRow}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // Centrar columnas específicas
        $sheet->getStyle("A19:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("C19:C{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle("D19:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // Colorear estados
        for ($i = 19; $i <= $lastRow; $i++) {
            $estado = $sheet->getCell("D{$i}")->getValue();
            if ($estado === 'APROBADO') {
                $sheet->getStyle("D{$i}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D4EDDA');
                $sheet->getStyle("D{$i}")->getFont()->getColor()->setRGB('155724');
            } elseif ($estado === 'REPROBADO') {
                $sheet->getStyle("D{$i}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('F8D7DA');
                $sheet->getStyle("D{$i}")->getFont()->getColor()->setRGB('721C24');
            }
        }
        
        // Ajustar ancho de columnas
        $sheet->getColumnDimension('A')->setWidth(15);
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        
        return [];
    }

    public function title(): string
    {
        return 'Rendimiento Académico';
    }
}