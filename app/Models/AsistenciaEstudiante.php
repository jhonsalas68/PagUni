<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AsistenciaEstudiante extends Model
{
    protected $table = 'asistencia_estudiantes';

    protected $fillable = [
        'inscripcion_id',
        'horario_id',
        'fecha',
        'hora_registro',
        'estado',
        'metodo_registro',
        'qr_token',
        'observaciones',
        'justificacion',
        'registrado_por',
        'latitud',
        'longitud',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_registro' => 'datetime',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    // Relaciones
    public function inscripcion(): BelongsTo
    {
        return $this->belongsTo(Inscripcion::class);
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class, 'registrado_por');
    }

    // Métodos de negocio
    public function esTardanza(): bool
    {
        return $this->estado === 'tardanza';
    }

    public function esPresente(): bool
    {
        return in_array($this->estado, ['presente', 'tardanza']);
    }

    public function esAusente(): bool
    {
        return $this->estado === 'ausente';
    }

    public function esJustificado(): bool
    {
        return $this->estado === 'justificado';
    }

    public function marcarPresente(string $metodo = 'qr', ?string $qrToken = null): bool
    {
        $this->estado = 'presente';
        $this->metodo_registro = $metodo;
        $this->hora_registro = now();
        $this->qr_token = $qrToken;

        return $this->save();
    }

    public function marcarTardanza(string $metodo = 'qr', ?string $qrToken = null): bool
    {
        $this->estado = 'tardanza';
        $this->metodo_registro = $metodo;
        $this->hora_registro = now();
        $this->qr_token = $qrToken;

        return $this->save();
    }

    public function justificar(string $justificacion, ?int $profesorId = null): bool
    {
        $this->estado = 'justificado';
        $this->justificacion = $justificacion;
        $this->registrado_por = $profesorId;

        return $this->save();
    }

    // Scopes
    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    public function scopePorEstado($query, string $estado)
    {
        return $query->where('estado', $estado);
    }

    public function scopePresentes($query)
    {
        return $query->whereIn('estado', ['presente', 'tardanza']);
    }

    public function scopeAusentes($query)
    {
        return $query->where('estado', 'ausente');
    }
}
