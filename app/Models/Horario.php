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
    ];

    protected $casts = [
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
        'es_semestral' => 'boolean',
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function cargaAcademica(): BelongsTo
    {
        return $this->belongsTo(CargaAcademica::class);
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class);
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

    // Método para validar conflictos mejorado
    public static function validarConflictos($carga_academica_id, $aula_id, $dia_semana, $hora_inicio, $hora_fin, $periodo_academico, $excluir_id = null)
    {
        $query = self::query()
            ->join('carga_academica', 'horarios.carga_academica_id', '=', 'carga_academica.id')
            ->where('horarios.dia_semana', $dia_semana)
            ->where('horarios.periodo_academico', $periodo_academico)
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

        // Obtener la carga académica para verificar el profesor
        $cargaAcademica = \App\Models\CargaAcademica::find($carga_academica_id);
        $profesor_id = $cargaAcademica ? $cargaAcademica->profesor_id : null;

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