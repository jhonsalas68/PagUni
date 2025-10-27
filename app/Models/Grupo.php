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
    ];

    public function materia(): BelongsTo
    {
        return $this->belongsTo(Materia::class);
    }

    public function cargaAcademica(): HasMany
    {
        return $this->hasMany(CargaAcademica::class);
    }

    // Accessor para mostrar nombre completo del grupo
    public function getNombreCompletoAttribute(): string
    {
        return $this->materia->nombre . ' - Grupo ' . $this->identificador;
    }

    // Scope para grupos activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}