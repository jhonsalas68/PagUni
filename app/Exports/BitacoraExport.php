<?php

namespace App\Exports;

use App\Models\AsistenciaDocente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class BitacoraExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $filtros;
    protected $estadisticas;

    public function __construct($filtros, $estadisticas)
    {
        $this->filtros = $filtros;
        $this->estadisticas = $estadisticas;
    }

    public function collection()
    {
        $fechaInicio = $this->filtros['fecha_inicio'] ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $fechaFin = $this->filtros['fecha_fin'] ?? Carbon::now()->format('Y-m-d');
        $tipoActividad = $this->filtros['tipo_actividad'] ?? 'todas';
        $profesorId = $this->filtros['profesor_id'] ?? null;
        $materiaId = $this->filtros['materia_id'] ?? null;
        $aulaId = $this->filtros['aula_id'] ?? null;

        // Query base para actividades
        $query = AsistenciaDocente::with([
            'horario.cargaAcademica.profesor', 
            'horario.cargaAcademica.grupo.materia', 
            'horario.aula'
        ])->whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);

        // Aplicar filtros
        if ($tipoActividad !== 'todas') {
            switch ($tipoActividad) {
                case 'qr_generados':
                    $query->whereNotNull('qr_token');
                    break;
                case 'asistencias':
                    $query->whereIn('estado', ['presente', 'tardanza']);
                    break;
                case 'faltas':
                    $query->where('estado', 'falta');
                    break;
                case 'justificaciones':
                    $query->where('estado', 'justificada');
                    break;
            }
        }

        if ($profesorId) {
            $query->whereHas('horario.cargaAcademica', function($q) use ($profesorId) {
                $q->where('profesor_id', $profesorId);
            });
        }

        if ($materiaId) {
            $query->whereHas('horario.cargaAcademica.grupo', function($q) use ($materiaId) {
                $q->where('materia_id', $materiaId);
            });
        }

        if ($aulaId) {
            $query->whereHas('horario', function($q) use ($aulaId) {
                $q->where('aula_id', $aulaId);
            });
        }

        return $query->orderBy('asistencia_docente.created_at', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Hora',
            'Docente',
            'Materia',
            'Código Materia',
            'Grupo',
            'Aula',
            'Tipo Actividad',
            'Estado',
            'Modalidad',
            'Hora Entrada',
            'Hora Salida',
            'Duración (min)',
            'Sesión',
            'QR Token',
            'Observaciones'
        ];
    }

    public function map($actividad): array
    {
        return [
            $actividad->created_at->format('d/m/Y'),
            $actividad->created_at->format('H:i:s'),
            $actividad->horario->cargaAcademica->profesor->nombre_completo ?? 'N/A',
            $actividad->horario->cargaAcademica->grupo->materia->nombre ?? 'N/A',
            $actividad->horario->cargaAcademica->grupo->materia->codigo ?? 'N/A',
            $actividad->horario->cargaAcademica->grupo->identificador ?? 'N/A',
            ($actividad->horario->aula->codigo_aula ?? 'N/A') . ' - ' . ($actividad->horario->aula->nombre ?? ''),
            $actividad->qr_token ? 'QR Generado' : 'Registro Asistencia',
            ucfirst($actividad->estado),
            ucfirst($actividad->modalidad ?? 'N/A'),
            $actividad->hora_entrada ?? 'N/A',
            $actividad->hora_salida ?? 'N/A',
            $actividad->duracion_clase ?? 'N/A',
            $actividad->numero_sesion ?? 'N/A',
            $actividad->qr_token ? 'Sí' : 'No',
            $actividad->observaciones ?? ''
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 12]],
        ];
    }

    public function title(): string
    {
        return 'Bitácora de Actividades';
    }
}