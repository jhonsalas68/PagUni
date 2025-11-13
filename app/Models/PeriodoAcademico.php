<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodoAcademico extends Model
{
    use HasFactory;

    protected $table = 'periodos_academicos';

    protected $fillable = [
        'codigo',
        'nombre',
        'anio',
        'semestre',
        'fecha_inicio',
        'fecha_fin',
        'estado',
        'es_actual',
        'observaciones'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'es_actual' => 'boolean',
    ];

    // Relaciones
    public function horarios()
    {
        return $this->hasMany(Horario::class, 'periodo', 'codigo');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'periodo_academico', 'codigo');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeActual($query)
    {
        return $query->where('es_actual', true)->first();
    }

    // Métodos
    public static function marcarComoActual($id)
    {
        // Desmarcar todos
        self::query()->update(['es_actual' => false]);
        
        // Marcar el seleccionado
        $periodo = self::find($id);
        if ($periodo) {
            $periodo->es_actual = true;
            $periodo->save();
        }
        
        return $periodo;
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'activo' => 'success',
            'inactivo' => 'secondary',
            'finalizado' => 'danger'
        ];
        
        return $badges[$this->estado] ?? 'secondary';
    }
}
