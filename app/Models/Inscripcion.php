<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Inscripcion extends Model
{
    protected $table = 'inscripciones';

    protected $fillable = [
        'estudiante_id',
        'grupo_id',
        'periodo_academico',
        'fecha_inscripcion',
        'estado',
        'fecha_baja',
        'motivo_baja',
    ];

    protected $casts = [
        'fecha_inscripcion' => 'datetime',
        'fecha_baja' => 'datetime',
    ];

    protected $appends = [];

    // Relaciones
    public function estudiante(): BelongsTo
    {
        return $this->belongsTo(Estudiante::class);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(AsistenciaEstudiante::class);
    }

    // Métodos de negocio
    public function calcularPorcentajeAsistencia(): float
    {
        $totalClases = $this->asistencias()->count();
        
        if ($totalClases === 0) {
            return 0.0;
        }

        // Solo contar "presente" (sin tardanzas ni justificaciones)
        $clasesPresentes = $this->asistencias()
            ->where('estado', 'presente')
            ->count();

        return round(($clasesPresentes / $totalClases) * 100, 2);
    }

    public function tieneAsistenciaBaja(): bool
    {
        return $this->calcularPorcentajeAsistencia() < 80;
    }

    public function puedeSerDadoDeBaja(): bool
    {
        // Verificar si hay un periodo de inscripción activo
        $periodoActivo = PeriodoInscripcion::where('activo', true)
            ->where('periodo_academico', $this->periodo_academico)
            ->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now())
            ->exists();

        return $this->estado === 'activo' && $periodoActivo;
    }

    public function darDeBaja(string $motivo = null): bool
    {
        if (!$this->puedeSerDadoDeBaja()) {
            return false;
        }

        $this->estado = 'dado_de_baja';
        $this->fecha_baja = now();
        $this->motivo_baja = $motivo;
        
        // Liberar cupo en el grupo
        $this->grupo->decrement('cupo_actual');

        return $this->save();
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorPeriodo($query, string $periodo)
    {
        return $query->where('periodo_academico', $periodo);
    }

    public function scopePorEstudiante($query, int $estudianteId)
    {
        return $query->where('estudiante_id', $estudianteId);
    }
}
