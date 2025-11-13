<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Grupo extends Model
{
    use HasFactory;

    protected $fillable = [
        'identificador',
        'materia_id',
        'capacidad_maxima',
        'estado',
        'cupo_maximo',
        'cupo_actual',
        'permite_inscripcion',
    ];

    protected $casts = [
        'permite_inscripcion' => 'boolean',
    ];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function cargaAcademica(): HasMany
    {
        return $this->hasMany(CargaAcademica::class);
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function inscripcionesActivas(): HasMany
    {
        return $this->hasMany(Inscripcion::class)->where('estado', 'activo');
    }

    public function horarios()
    {
        return $this->hasManyThrough(
            Horario::class,
            CargaAcademica::class,
            'grupo_id',           // Foreign key en carga_academica
            'carga_academica_id', // Foreign key en horarios
            'id',                 // Local key en grupos
            'id'                  // Local key en carga_academica
        );
    }

    // Accessor para mostrar nombre completo del grupo
    public function getNombreCompletoAttribute(): string
    {
        return $this->materia->nombre . ' - Grupo ' . $this->identificador;
    }

    // Métodos de negocio
    public function tieneCupoDisponible(): bool
    {
        return $this->cupo_actual < $this->cupo_maximo && $this->permite_inscripcion;
    }

    public function cuposDisponibles(): int
    {
        return max(0, $this->cupo_maximo - $this->cupo_actual);
    }

    public function incrementarCupo(): bool
    {
        if ($this->cupo_actual < $this->cupo_maximo) {
            $this->increment('cupo_actual');
            return true;
        }
        return false;
    }

    public function decrementarCupo(): bool
    {
        if ($this->cupo_actual > 0) {
            $this->decrement('cupo_actual');
            return true;
        }
        return false;
    }

    // Scope para grupos activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeConCupo($query)
    {
        return $query->whereColumn('cupo_actual', '<', 'cupo_maximo')
            ->where('permite_inscripcion', true);
    }
}