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
        \Log::info('Iniciando validación de conflictos', [
            'carga_academica_id' => $carga_academica_id,
            'aula_id' => $aula_id,
            'dia_semana' => $dia_semana,
            'hora_inicio' => $hora_inicio,
            'hora_fin' => $hora_fin,
            'periodo_academico' => $periodo_academico,
            'excluir_id' => $excluir_id
        ]);

        // Obtener la carga académica para verificar el profesor
        $cargaAcademica = \App\Models\CargaAcademica::with(['profesor', 'grupo.materia'])->find($carga_academica_id);
        $profesor_id = $cargaAcademica ? $cargaAcademica->profesor_id : null;

        \Log::info('Información de la carga académica', [
            'profesor_id' => $profesor_id,
            'profesor_nombre' => $cargaAcademica->profesor->nombre_completo ?? 'N/A',
            'materia' => $cargaAcademica->grupo->materia->nombre ?? 'N/A'
        ]);

        // Convertir horas a formato comparable
        $horaInicioComparable = \Carbon\Carbon::createFromFormat('H:i', $hora_inicio);
        $horaFinComparable = \Carbon\Carbon::createFromFormat('H:i', $hora_fin);

        // Query base más robusta para conflictos de horario
        $baseQuery = self::query()
            ->with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula'])
            ->where('dia_semana', $dia_semana)
            ->where('periodo_academico', $periodo_academico)
            ->where(function ($q) use ($hora_inicio, $hora_fin) {
                // Verificar solapamiento real: dos horarios se solapan si uno inicia antes de que termine el otro
                // Fórmula: (inicio1 < fin2) AND (fin1 > inicio2)
                $q->where('hora_inicio', '<', $hora_fin)
                  ->where('hora_fin', '>', $hora_inicio);
            });

        if ($excluir_id) {
            $baseQuery->where('horarios.id', '!=', $excluir_id);
        }

        // Verificar conflictos de profesor
        $conflictosProfesor = $baseQuery->clone()
            ->join('carga_academica', 'horarios.carga_academica_id', '=', 'carga_academica.id')
            ->where('carga_academica.profesor_id', $profesor_id)
            ->select('horarios.*') // Especificar que queremos solo las columnas de horarios
            ->get();

        \Log::info('Conflictos de profesor encontrados', [
            'count' => $conflictosProfesor->count(),
            'conflictos' => $conflictosProfesor->map(function($c) {
                return [
                    'id' => $c->id,
                    'materia' => $c->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                    'aula' => $c->aula->codigo_aula ?? 'N/A',
                    'horario' => $c->hora_inicio . '-' . $c->hora_fin
                ];
            })
        ]);

        // Verificar conflictos de aula
        $conflictosAula = $baseQuery->clone()
            ->where('horarios.aula_id', $aula_id)
            ->get();

        \Log::info('Conflictos de aula encontrados', [
            'count' => $conflictosAula->count(),
            'conflictos' => $conflictosAula->map(function($c) {
                return [
                    'id' => $c->id,
                    'profesor' => $c->cargaAcademica->profesor->nombre_completo ?? 'N/A',
                    'materia' => $c->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                    'horario' => $c->hora_inicio . '-' . $c->hora_fin
                ];
            })
        ]);

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
                'grupo' => $conflicto->cargaAcademica->grupo->identificador ?? 'N/A',
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
                'grupo' => $conflicto->cargaAcademica->grupo->identificador ?? 'N/A',
            ];
        }

        $disponible = $conflictosProfesor->isEmpty() && $conflictosAula->isEmpty();

        \Log::info('Resultado final de validación', [
            'disponible' => $disponible,
            'conflicto_profesor' => !$conflictosProfesor->isEmpty(),
            'conflicto_aula' => !$conflictosAula->isEmpty()
        ]);

        return [
            'disponible' => $disponible,
            'conflicto_profesor' => !$conflictosProfesor->isEmpty(),
            'conflicto_aula' => !$conflictosAula->isEmpty(),
            'detalles_profesor' => $detallesConflictoProfesor,
            'detalles_aula' => $detallesConflictoAula,
            'profesor_nombre' => $cargaAcademica->profesor->nombre_completo ?? 'N/A',
            'materia_nombre' => $cargaAcademica->grupo->materia->nombre ?? 'N/A',
            'total_conflictos' => $conflictosProfesor->count() + $conflictosAula->count(),
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