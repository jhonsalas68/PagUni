<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profesor extends Model
{
    use HasFactory;

    protected $table = 'profesores';

    protected $fillable = [
        'codigo_docente',
        'nombre',
        'apellido',
        'email',
        'telefono',
        'cedula',
        'especialidad',
        'tipo_contrato',
        'password',
        'estado',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function inscripciones(): HasMany
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return $this->nombre . ' ' . $this->apellido;
    }

    // Scope para profesores activos
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    // Método para desactivar profesor
    public function desactivar()
    {
        $this->update(['estado' => 'inactivo']);
    }

    // Método para activar profesor
    public function activar()
    {
        $this->update(['estado' => 'activo']);
    }

    public function cargaAcademica(): HasMany
    {
        return $this->hasMany(CargaAcademica::class);
    }
}