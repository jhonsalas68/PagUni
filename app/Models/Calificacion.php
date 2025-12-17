<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calificacion extends Model
{
    use HasFactory;

    protected $table = 'calificaciones';

    protected $fillable = [
        'inscripcion_id',
        'tipo_evaluacion_id',
        'nota',
        'fecha',
        'observaciones',
    ];

    protected $casts = [
        'nota' => 'decimal:2',
        'fecha' => 'date',
    ];

    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function tipoEvaluacion(): BelongsTo
    {
        return $this->belongsTo(TipoEvaluacion::class);
    }
}
