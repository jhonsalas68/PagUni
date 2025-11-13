<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estudiante extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellido',
        'email',
        'telefono',
        'cedula',
        'codigo_estudiante',
        'fecha_nacimiento',
        'direccion',
        'password',
        'carrera_id',
        'semestre_actual',
        'estado',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'password' => 'hashed',
    ];

    public function carrera(): BelongsTo
    {
        return $this->belongsTo(Carrera::class);
    }

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function inscripcionesActivas(): HasMany
    {
        return $this->hasMany(Inscripcion::class)->where('estado', 'activo');
    }

    public function asistencias()
    {
        return $this->hasManyThrough(
            AsistenciaEstudiante::class,
            Inscripcion::class,
            'estudiante_id',
            'inscripcion_id'
        );
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    public function calcularPromedioAsistencia(): float
    {
        $inscripciones = $this->inscripcionesActivas;
        
        if ($inscripciones->isEmpty()) {
            return 0.0;
        }

        $totalPorcentaje = $inscripciones->sum(function ($inscripcion) {
            return $inscripcion->calcularPorcentajeAsistencia();
        });

        return round($totalPorcentaje / $inscripciones->count(), 2);
    }
}