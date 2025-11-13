<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoInscripcion extends Model
{
    protected $table = 'periodos_inscripcion';

    protected $fillable = [
        'nombre',
        'periodo_academico',
        'fecha_inicio',
        'fecha_fin',
        'activo',
        'descripcion',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'activo' => 'boolean',
    ];

    // Métodos de negocio
    public function estaActivo(): bool
    {
        return $this->activo 
            && now()->between($this->fecha_inicio, $this->fecha_fin);
    }

    public function activar(): bool
    {
        // Desactivar otros periodos del mismo periodo académico
        self::where('periodo_academico', $this->periodo_academico)
            ->where('id', '!=', $this->id)
            ->update(['activo' => false]);

        $this->activo = true;
        return $this->save();
    }

    public function desactivar(): bool
    {
        $this->activo = false;
        return $this->save();
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function scopeVigentes($query)
    {
        return $query->where('fecha_inicio', '<=', now())
            ->where('fecha_fin', '>=', now());
    }

    public function scopePorPeriodo($query, string $periodo)
    {
        return $query->where('periodo_academico', $periodo);
    }

    // Método estático para obtener el periodo activo actual
    public static function periodoActual(): ?self
    {
        return self::activos()
            ->vigentes()
            ->first();
    }
}
