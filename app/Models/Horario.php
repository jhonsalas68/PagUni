<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'carga_academica_id',
        'aula_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'duracion_horas',
        'tipo_clase',
        'periodo_academico',
        'es_semestral',
        'fecha_inicio',
        'fecha_fin',
        'semanas_duracion',
        'configuracion_dias',
        'usar_configuracion_por_dia'
    ];

    protected $casts = [
        'es_semestral' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'configuracion_dias' => 'array',
        'usar_configuracion_por_dia' => 'boolean',
    ];

    public function cargaAcademica(): BelongsTo
    {
        return $this->belongsTo(CargaAcademica::class);
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
    }

    // Accessors para formatear las horas correctamente
    public function getHoraInicioAttribute($value)
    {
        if (!$value) return null;
        
        // Convertir a formato H:i (ej: 08:00)
        return Carbon::parse($value)->format('H:i');
    }

    public function getHoraFinAttribute($value)
    {
        if (!$value) return null;
        
        // Convertir a formato H:i (ej: 10:00)
        return Carbon::parse($value)->format('H:i');
    }

    // Accessor para descripción completa del horario
    public function getDescripcionCompletaAttribute(): string
    {
        return ucfirst($this->dia_semana) . ' ' . 
               Carbon::parse($this->hora_inicio)->format('H:i') . '-' . 
               Carbon::parse($this->hora_fin)->format('H:i') . ' - ' . 
               $this->aula->codigo_aula;
    }

    // Método para validar disponibilidad de recursos
    public static function validarDisponibilidad($profesor_id, $aula_id, $dia_semana, $hora_inicio, $hora_fin, $periodo, $excluir_id = null)
    {
        $query = self::query()
            ->join('carga_academica', 'horarios.carga_academica_id', '=', 'carga_academica.id')
            ->where('horarios.dia_semana', $dia_semana)
            ->where('horarios.periodo', $periodo)
            ->where('horarios.estado', 'activo')
            ->where(function ($q) use ($hora_inicio, $hora_fin) {
                $q->whereBetween('horarios.hora_inicio', [$hora_inicio, $hora_fin])
                  ->orWhereBetween('horarios.hora_fin', [$hora_inicio, $hora_fin])
                  ->orWhere(function ($q2) use ($hora_inicio, $hora_fin) {
                      $q2->where('horarios.hora_inicio', '<=', $hora_inicio)
                         ->where('horarios.hora_fin', '>=', $hora_fin);
                  });
            });

        if ($excluir_id) {
            $query->where('horarios.id', '!=', $excluir_id);
        }

        // Verificar conflicto de profesor
        $conflicto_profesor = $query->clone()
            ->where('carga_academica.profesor_id', $profesor_id)
            ->exists();

        // Verificar conflicto de aula
        $conflicto_aula = $query->clone()
            ->where('horarios.aula_id', $aula_id)
            ->exists();

        return [
            'disponible' => !$conflicto_profesor && !$conflicto_aula,
            'conflicto_profesor' => $conflicto_profesor,
            'conflicto_aula' => $conflicto_aula,
        ];
    }

    // Métodos para calcular carga horaria
    public function getCargaHorariaSemanalAttribute()
    {
        return $this->duracion_horas ?? 0;
    }

    public function getCargaHorariaMensualAttribute()
    {
        return ($this->duracion_horas ?? 0) * 4; // 4 semanas por mes aproximadamente
    }

    public function getCargaHorariaSemestralAttribute()
    {
        if (!$this->es_semestral) {
            return $this->duracion_horas ?? 0;
        }
        return ($this->duracion_horas ?? 0) * ($this->semanas_duracion ?? 16);
    }

    public function getTotalHorasAttribute()
    {
        return $this->es_semestral ? $this->carga_horaria_semestral : $this->duracion_horas;
    }

    // Método para validar conflictos mejorado con detalles
    public static function validarConflictos($carga_academica_id, $aula_id, $dia_semana, $hora_inicio, $hora_fin, $periodo_academico, $excluir_id = null)
    {
        // Obtener la carga académica para verificar el profesor
        $cargaAcademica = \App\Models\CargaAcademica::with(['profesor', 'grupo.materia'])->find($carga_academica_id);
        $profesor_id = $cargaAcademica ? $cargaAcademica->profesor_id : null;

        // Query base para conflictos de horario
        $baseQuery = self::query()
            ->with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula'])
            ->where('horarios.dia_semana', $dia_semana)
            ->where('horarios.periodo_academico', $periodo_academico)
            ->where(function ($q) use ($hora_inicio, $hora_fin) {
                // Verificar solapamiento de horarios
                $q->where(function ($q1) use ($hora_inicio, $hora_fin) {
                    // Caso 1: El nuevo horario inicia durante un horario existente
                    $q1->where('horarios.hora_inicio', '<=', $hora_inicio)
                       ->where('horarios.hora_fin', '>', $hora_inicio);
                })->orWhere(function ($q2) use ($hora_inicio, $hora_fin) {
                    // Caso 2: El nuevo horario termina durante un horario existente
                    $q2->where('horarios.hora_inicio', '<', $hora_fin)
                       ->where('horarios.hora_fin', '>=', $hora_fin);
                })->orWhere(function ($q3) use ($hora_inicio, $hora_fin) {
                    // Caso 3: El nuevo horario contiene completamente un horario existente
                    $q3->where('horarios.hora_inicio', '>=', $hora_inicio)
                       ->where('horarios.hora_fin', '<=', $hora_fin);
                });
            });

        if ($excluir_id) {
            $baseQuery->where('horarios.id', '!=', $excluir_id);
        }

        // Verificar conflictos de profesor
        $conflictosProfesor = $baseQuery->clone()
            ->join('carga_academica', 'horarios.carga_academica_id', '=', 'carga_academica.id')
            ->where('carga_academica.profesor_id', $profesor_id)
            ->get();

        // Verificar conflictos de aula
        $conflictosAula = $baseQuery->clone()
            ->where('horarios.aula_id', $aula_id)
            ->get();

        // Preparar información detallada de conflictos
        $detallesConflictoProfesor = [];
        $detallesConflictoAula = [];

        foreach ($conflictosProfesor as $conflicto) {
            $detallesConflictoProfesor[] = [
                'horario_id' => $conflicto->id,
                'materia' => $conflicto->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                'aula' => $conflicto->aula->codigo_aula ?? 'N/A',
                'hora_inicio' => $conflicto->hora_inicio,
                'hora_fin' => $conflicto->hora_fin,
                'tipo_clase' => $conflicto->tipo_clase,
            ];
        }

        foreach ($conflictosAula as $conflicto) {
            $detallesConflictoAula[] = [
                'horario_id' => $conflicto->id,
                'profesor' => $conflicto->cargaAcademica->profesor->nombre_completo ?? 'N/A',
                'materia' => $conflicto->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                'hora_inicio' => $conflicto->hora_inicio,
                'hora_fin' => $conflicto->hora_fin,
                'tipo_clase' => $conflicto->tipo_clase,
            ];
        }

        return [
            'disponible' => $conflictosProfesor->isEmpty() && $conflictosAula->isEmpty(),
            'conflicto_profesor' => !$conflictosProfesor->isEmpty(),
            'conflicto_aula' => !$conflictosAula->isEmpty(),
            'detalles_profesor' => $detallesConflictoProfesor,
            'detalles_aula' => $detallesConflictoAula,
            'profesor_nombre' => $cargaAcademica->profesor->nombre_completo ?? 'N/A',
            'materia_nombre' => $cargaAcademica->grupo->materia->nombre ?? 'N/A',
        ];
    }

    // Métodos para configuración por día
    public function getAulaPorDia($dia)
    {
        if (!$this->usar_configuracion_por_dia || !$this->configuracion_dias) {
            return $this->aula_id;
        }

        $config = collect($this->configuracion_dias)->firstWhere('dia', $dia);
        return $config['aula_id'] ?? $this->aula_id;
    }

    public function getTipoClasePorDia($dia)
    {
        if (!$this->usar_configuracion_por_dia || !$this->configuracion_dias) {
            return $this->tipo_clase;
        }

        $config = collect($this->configuracion_dias)->firstWhere('dia', $dia);
        return $config['tipo_clase'] ?? $this->tipo_clase;
    }

    public function getConfiguracionCompleta()
    {
        if (!$this->usar_configuracion_por_dia) {
            return null;
        }

        return collect($this->configuracion_dias)->map(function ($config) {
            $aula = \App\Models\Aula::find($config['aula_id']);
            return [
                'dia' => $config['dia'],
                'dia_nombre' => $this->getNombreDia($config['dia']),
                'aula_id' => $config['aula_id'],
                'aula_nombre' => $aula ? $aula->codigo_aula . ' - ' . $aula->nombre : 'N/A',
                'tipo_clase' => $config['tipo_clase'],
                'tipo_clase_nombre' => ucfirst($config['tipo_clase'])
            ];
        });
    }

    private function getNombreDia($dia)
    {
        $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        return $dias[$dia] ?? 'N/A';
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePeriodo($query, $periodo)
    {
        return $query->where('periodo_academico', $periodo);
    }

    public function scopeDia($query, $dia)
    {
        return $query->where('dia_semana', $dia);
    }

    public function scopeSemestral($query)
    {
        return $query->where('es_semestral', true);
    }
}