<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipoEvaluacion extends Model
{
    use HasFactory;

    protected $table = 'tipos_evaluacion';

    protected $fillable = [
        'grupo_id',
        'nombre',
        'ponderacion',
        'estado',
    ];

    public function calificaciones()
    {
        return $this->hasMany(Calificacion::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }
}
