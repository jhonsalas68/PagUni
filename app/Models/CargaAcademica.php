<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CargaAcademica extends Model
{
    use HasFactory;

    protected $table = 'carga_academica';

    protected $fillable = [
        'profesor_id',
        'grupo_id',
        'periodo',
        'estado',
    ];

    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class);
    }

    public function grupo(): BelongsTo
    {
        return $this->belongsTo(Grupo::class);
    }

    public function horarios(): HasMany
    {
        return $this->hasMany(Horario::class);
    }

    // Relación indirecta con materia a través del grupo
    public function materia()
    {
        return $this->hasOneThrough(Materia::class, Grupo::class, 'id', 'id', 'grupo_id', 'materia_id');
    }

    // Accessor para descripción completa
    public function getDescripcionCompletaAttribute(): string
    {
        return $this->profesor->nombre_completo . ' - ' . $this->grupo->nombre_completo . ' (' . $this->periodo . ')';
    }

    // Scope para carga activa
    public function scopeAsignada($query)
    {
        return $query->where('estado', 'asignado');
    }

    // Scope por período
    public function scopePeriodo($query, $periodo)
    {
        return $query->where('periodo', $periodo);
    }
}