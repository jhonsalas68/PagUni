<?php

namespace App\Exports;

use App\Models\AsistenciaDocente;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Carbon\Carbon;

class AsistenciaExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filtros;
    protected $columnas;

    public function __construct($filtros, $columnas)
    {
        $this->filtros = $filtros;
        $this->columnas = $columnas;
    }

    public function collection()
    {
        $query = AsistenciaDocente::with(['horario.cargaAcademica.grupo.materia', 'horario.cargaAcademica.profesor', 'horario.aula']);

        // Aplicar filtros
        if (!empty($this->filtros['fecha_inicio'])) {
            $query->whereDate('fecha', '>=', $this->filtros['fecha_inicio']);
        }

        if (!empty($this->filtros['fecha_fin'])) {
            $query->whereDate('fecha', '<=', $this->filtros['fecha_fin']);
        }

        if (!empty($this->filtros['docente_id'])) {
            $query->whereHas('horario.cargaAcademica', function($q) {
                $q->where('profesor_id', $this->filtros['docente_id']);
            });
        }

        if (!empty($this->filtros['materia_id'])) {
            $query->whereHas('horario.cargaAcademica.grupo.materia', function($q) {
                $q->where('id', $this->filtros['materia_id']);
            });
        }

        if (!empty($this->filtros['aula_id'])) {
            $query->whereHas('horario', function($q) {
                $q->where('aula_id', $this->filtros['aula_id']);
            });
        }

        if (!empty($this->filtros['estado'])) {
            $query->where('estado', $this->filtros['estado']);
        }

        if (!empty($this->filtros['modalidad'])) {
            $query->where('modalidad', $this->filtros['modalidad']);
        }

        return $query->orderBy('fecha', 'desc')->orderBy('hora_entrada', 'desc')->get();
    }

    public function headings(): array
    {
        $headings = [];
        
        if (in_array('fecha', $this->columnas)) $headings[] = 'Fecha';
        if (in_array('docente', $this->columnas)) $headings[] = 'Docente';
        if (in_array('materia', $this->columnas)) $headings[] = 'Materia';
        if (in_array('grupo', $this->columnas)) $headings[] = 'Grupo';
        if (in_array('aula', $this->columnas)) $headings[] = 'Aula';
        if (in_array('horario', $this->columnas)) $headings[] = 'Horario';
        if (in_array('estado', $this->columnas)) $headings[] = 'Estado';
        if (in_array('modalidad', $this->columnas)) $headings[] = 'Modalidad';
        if (in_array('hora_entrada', $this->columnas)) $headings[] = 'Hora Entrada';
        if (in_array('hora_salida', $this->columnas)) $headings[] = 'Hora Salida';
        if (in_array('duracion', $this->columnas)) $headings[] = 'Duración (min)';
        if (in_array('sesion', $this->columnas)) $headings[] = 'Sesión';

        return $headings;
    }

    public function map($asistencia): array
    {
        $row = [];

        if (in_array('fecha', $this->columnas)) {
            $row[] = Carbon::parse($asistencia->fecha)->format('d/m/Y');
        }
        
        if (in_array('docente', $this->columnas)) {
            $row[] = $asistencia->horario->cargaAcademica->profesor->nombre_completo ?? 'N/A';
        }
        
        if (in_array('materia', $this->columnas)) {
            $row[] = $asistencia->horario->cargaAcademica->grupo->materia->nombre ?? 'N/A';
        }
        
        if (in_array('grupo', $this->columnas)) {
            $row[] = $asistencia->horario->cargaAcademica->grupo->identificador ?? 'N/A';
        }
        
        if (in_array('aula', $this->columnas)) {
            $row[] = $asistencia->horario->aula->codigo_aula ?? 'N/A';
        }
        
        if (in_array('horario', $this->columnas)) {
            $row[] = $asistencia->horario->hora_inicio . ' - ' . $asistencia->horario->hora_fin;
        }
        
        if (in_array('estado', $this->columnas)) {
            $estados = [
                'presente' => 'Presente',
                'tardanza' => 'Tardanza',
                'falta' => 'Falta',
                'justificada' => 'Justificada',
                'pendiente_qr' => 'Pendiente QR'
            ];
            $row[] = $estados[$asistencia->estado] ?? $asistencia->estado;
        }
        
        if (in_array('modalidad', $this->columnas)) {
            $row[] = ucfirst($asistencia->modalidad ?? 'N/A');
        }
        
        if (in_array('hora_entrada', $this->columnas)) {
            $row[] = $asistencia->hora_entrada ?? 'N/A';
        }
        
        if (in_array('hora_salida', $this->columnas)) {
            $row[] = $asistencia->hora_salida ?? 'N/A';
        }
        
        if (in_array('duracion', $this->columnas)) {
            $row[] = $asistencia->duracion_clase ?? 0;
        }
        
        if (in_array('sesion', $this->columnas)) {
            $row[] = $asistencia->numero_sesion ?? 1;
        }

        return $row;
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}