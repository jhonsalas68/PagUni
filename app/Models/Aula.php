<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo_aula',
        'nombre',
        'tipo_aula',
        'edificio',
        'piso',
        'capacidad',
        'descripcion',
        'equipamiento',
        'estado',
        'tiene_aire_acondicionado',
        'tiene_proyector',
        'tiene_computadoras',
        'acceso_discapacitados',
    ];

    protected $casts = [
        'equipamiento' => 'array',
        'tiene_aire_acondicionado' => 'boolean',
        'tiene_proyector' => 'boolean',
        'tiene_computadoras' => 'boolean',
        'acceso_discapacitados' => 'boolean',
    ];

    // Accessor para mostrar el tipo de aula de forma legible
    public function getTipoAulaLegibleAttribute()
    {
        $tipos = [
            'aula' => 'Aula Regular',
            'laboratorio' => 'Laboratorio',
            'auditorio' => 'Auditorio',
            'sala_conferencias' => 'Sala de Conferencias',
            'biblioteca' => 'Biblioteca'
        ];

        return $tipos[$this->tipo_aula] ?? $this->tipo_aula;
    }

    // Accessor para mostrar el estado de forma legible
    public function getEstadoLegibleAttribute()
    {
        $estados = [
            'disponible' => 'Disponible',
            'ocupada' => 'Ocupada',
            'mantenimiento' => 'En Mantenimiento',
            'fuera_servicio' => 'Fuera de Servicio'
        ];

        return $estados[$this->estado] ?? $this->estado;
    }

    // Método para obtener la ubicación completa
    public function getUbicacionCompletaAttribute()
    {
        return "{$this->edificio} - Piso {$this->piso} - {$this->codigo_aula}";
    }

    // Scope para filtrar por tipo de aula
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo_aula', $tipo);
    }

    // Scope para filtrar por edificio
    public function scopeEdificio($query, $edificio)
    {
        return $query->where('edificio', $edificio);
    }

    // Scope para aulas disponibles
    public function scopeDisponibles($query)
    {
        return $query->where('estado', 'disponible');
    }
}