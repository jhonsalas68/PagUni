<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class AsistenciaDocente extends Model
{
    use HasFactory;

    protected $table = 'asistencia_docente';

    protected $fillable = [
        'profesor_id',
        'horario_id',
        'qr_token',
        'qr_generado_at',
        'qr_escaneado_at',
        'fecha',
        'hora_entrada',
        'hora_salida',
        'estado',
        'modalidad',
        'numero_sesion',
        'justificacion',
        'tipo_justificacion',
        'justificado_por',
        'fecha_justificacion',
        'metadata',
        'validado_en_horario',
        'observaciones',
        'ip_escaneo',
        'ubicacion_escaneo',
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_entrada' => 'datetime:H:i',
        'hora_salida' => 'datetime:H:i',
        'qr_generado_at' => 'datetime',
        'qr_escaneado_at' => 'datetime',
        'fecha_justificacion' => 'datetime',
        'metadata' => 'array',
        'ubicacion_escaneo' => 'array',
        'validado_en_horario' => 'boolean',
    ];

    // Relaciones
    public function profesor(): BelongsTo
    {
        return $this->belongsTo(Profesor::class);
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class);
    }

    public function justificadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'justificado_por');
    }

    // Scopes
    public function scopeHoy($query)
    {
        return $query->whereDate('fecha', today());
    }

    public function scopePresentes($query)
    {
        return $query->whereIn('estado', ['presente', 'en_clase']);
    }

    public function scopeAusentes($query)
    {
        return $query->where('estado', 'ausente');
    }

    public function scopeEnClase($query)
    {
        return $query->where('estado', 'en_clase');
    }

    public function scopePorProfesor($query, $profesorId)
    {
        return $query->where('profesor_id', $profesorId);
    }

    public function scopePorFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    // Métodos de negocio
    
    /**
     * Generar código QR para una clase
     */
    public static function generarQR($profesorId, $horarioId, $modalidad = 'presencial')
    {
        $fecha = today();
        
        // Obtener el número de sesión (cuántas veces se ha dado esta clase hoy)
        $numeroSesion = self::where([
            'profesor_id' => $profesorId,
            'horario_id' => $horarioId,
            'fecha' => $fecha,
        ])->count() + 1;

        // Generar token único
        $qrToken = self::generarTokenQR($profesorId, $horarioId, $numeroSesion);

        // Crear o actualizar registro de asistencia
        $asistencia = self::updateOrCreate(
            [
                'profesor_id' => $profesorId,
                'horario_id' => $horarioId,
                'fecha' => $fecha,
                'numero_sesion' => $numeroSesion,
            ],
            [
                'qr_token' => $qrToken,
                'qr_generado_at' => now(),
                'modalidad' => $modalidad,
                'estado' => 'pendiente_qr',
                'validado_en_horario' => false,
            ]
        );

        return $asistencia;
    }

    /**
     * Procesar escaneo de código QR
     */
    public static function procesarEscaneoQR($qrToken, $ipEscaneo = null, $ubicacionEscaneo = null)
    {
        $asistencia = self::where('qr_token', $qrToken)
            ->whereNotNull('qr_generado_at')
            ->whereNull('qr_escaneado_at')
            ->first();

        if (!$asistencia) {
            throw new \Exception('Código QR inválido o ya utilizado');
        }

        // Verificar que el QR no haya expirado (válido por 30 minutos)
        if ($asistencia->qr_generado_at->diffInMinutes(now()) > 30) {
            throw new \Exception('El código QR ha expirado. Genera uno nuevo.');
        }

        // Validar horario
        $horario = Horario::find($asistencia->horario_id);
        $esValido = self::validarDentroDeHorario($horario, now());

        // Actualizar registro
        $asistencia->update([
            'qr_escaneado_at' => now(),
            'hora_entrada' => now()->format('H:i'),
            'estado' => $esValido ? 'presente' : 'tardanza',
            'validado_en_horario' => $esValido,
            'ip_escaneo' => $ipEscaneo,
            'ubicacion_escaneo' => $ubicacionEscaneo,
            'metadata' => array_merge($asistencia->metadata ?? [], [
                'qr_escaneado_ip' => $ipEscaneo,
                'qr_escaneado_timestamp' => now()->toISOString(),
                'user_agent' => request()->userAgent(),
            ]),
        ]);

        return $asistencia;
    }

    /**
     * Generar token único para QR
     */
    private static function generarTokenQR($profesorId, $horarioId, $numeroSesion)
    {
        $data = [
            'profesor_id' => $profesorId,
            'horario_id' => $horarioId,
            'numero_sesion' => $numeroSesion,
            'fecha' => today()->toDateString(),
            'timestamp' => now()->timestamp,
            'random' => \Str::random(10),
        ];

        return hash('sha256', json_encode($data));
    }

    public static function registrarEntrada($profesorId, $horarioId, $horaEntrada = null)
    {
        $horaEntrada = $horaEntrada ?? now();
        $fecha = $horaEntrada->toDateString();
        
        // Buscar o crear registro de asistencia
        $asistencia = self::firstOrCreate(
            [
                'profesor_id' => $profesorId,
                'horario_id' => $horarioId,
                'fecha' => $fecha,
            ],
            [
                'estado' => 'ausente',
                'validado_en_horario' => false,
            ]
        );

        // Validar si está dentro del horario
        $horario = Horario::find($horarioId);
        $esValido = self::validarDentroDeHorario($horario, $horaEntrada);

        // Actualizar registro
        $asistencia->update([
            'hora_entrada' => $horaEntrada->format('H:i'),
            'estado' => $esValido ? 'en_clase' : 'tardanza',
            'validado_en_horario' => $esValido,
            'metadata' => array_merge($asistencia->metadata ?? [], [
                'ip_entrada' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'timestamp_entrada' => $horaEntrada->toISOString(),
            ]),
        ]);

        return $asistencia;
    }

    public static function registrarSalida($profesorId, $horarioId, $horaSalida = null)
    {
        $horaSalida = $horaSalida ?? now();
        $fecha = $horaSalida->toDateString();
        
        $asistencia = self::where([
            'profesor_id' => $profesorId,
            'horario_id' => $horarioId,
            'fecha' => $fecha,
        ])->first();

        if (!$asistencia) {
            throw new \Exception('No se encontró registro de entrada para este horario.');
        }

        // Validar si la salida está dentro del horario
        $horario = Horario::find($horarioId);
        $esValidoSalida = self::validarSalidaDentroDeHorario($horario, $horaSalida);

        // Actualizar registro
        $asistencia->update([
            'hora_salida' => $horaSalida->format('H:i'),
            'estado' => $asistencia->estado === 'en_clase' && $esValidoSalida ? 'presente' : $asistencia->estado,
            'metadata' => array_merge($asistencia->metadata ?? [], [
                'ip_salida' => request()->ip(),
                'timestamp_salida' => $horaSalida->toISOString(),
            ]),
        ]);

        return $asistencia;
    }

    public static function validarDentroDeHorario($horario, $horaRegistro, $toleranciaMinutos = 15)
    {
        if (!$horario) return false;

        try {
            // Manejo robusto de formatos de hora
            $horaInicio = self::parseHora($horario->hora_inicio);
            $horaFin = self::parseHora($horario->hora_fin);
            $horaRegistroFormateada = Carbon::createFromFormat('H:i', $horaRegistro->format('H:i'));

            // Validación estricta: solo dentro del horario asignado
            $horaInicioConTolerancia = $horaInicio->copy()->subMinutes($toleranciaMinutos);
            
            return $horaRegistroFormateada->between($horaInicioConTolerancia, $horaFin);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validación estricta sin tolerancia
     */
    public static function validarEstrictamenteDentroDeHorario($horario, $horaRegistro)
    {
        if (!$horario) return false;

        try {
            $horaInicio = self::parseHora($horario->hora_inicio);
            $horaFin = self::parseHora($horario->hora_fin);
            $horaRegistroFormateada = Carbon::createFromFormat('H:i', $horaRegistro->format('H:i'));

            return $horaRegistroFormateada->between($horaInicio, $horaFin);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Método auxiliar para parsear horas en diferentes formatos
     */
    private static function parseHora($hora)
    {
        try {
            return Carbon::createFromFormat('H:i:s', $hora);
        } catch (\Exception $e) {
            try {
                return Carbon::createFromFormat('H:i', $hora);
            } catch (\Exception $e2) {
                return Carbon::parse($hora);
            }
        }
    }

    public static function validarSalidaDentroDeHorario($horario, $horaSalida)
    {
        if (!$horario) return false;

        $horaFin = Carbon::createFromFormat('H:i', $horario->hora_fin);
        $horaSalidaFormateada = Carbon::createFromFormat('H:i', $horaSalida->format('H:i'));

        // Permitir salida hasta 30 minutos después del fin de clase
        $horaFinConTolerancia = $horaFin->copy()->addMinutes(30);
        
        return $horaSalidaFormateada->lte($horaFinConTolerancia);
    }

    public function justificar($justificacion, $tipoJustificacion, $justificadoPor = null)
    {
        $this->update([
            'estado' => 'justificado',
            'justificacion' => $justificacion,
            'tipo_justificacion' => $tipoJustificacion,
            'justificado_por' => $justificadoPor,
            'fecha_justificacion' => now(),
        ]);

        return $this;
    }

    /**
     * Justificar falta posterior
     */
    public static function justificarFaltaPosterior($profesorId, $horarioId, $fecha, $justificacion, $tipoJustificacion, $justificadoPor)
    {
        // Buscar o crear registro de asistencia
        $asistencia = self::firstOrCreate(
            [
                'profesor_id' => $profesorId,
                'horario_id' => $horarioId,
                'fecha' => $fecha,
                'numero_sesion' => 1,
            ],
            [
                'estado' => 'ausente',
                'validado_en_horario' => false,
            ]
        );

        // Justificar
        $asistencia->justificar($justificacion, $tipoJustificacion, $justificadoPor);

        return $asistencia;
    }

    /**
     * Registrar modalidad especial
     */
    public static function registrarModalidadEspecial($profesorId, $horarioId, $fecha, $modalidad, $observaciones, $registradoPor)
    {
        $asistencia = self::firstOrCreate(
            [
                'profesor_id' => $profesorId,
                'horario_id' => $horarioId,
                'fecha' => $fecha,
                'numero_sesion' => 1,
            ],
            [
                'estado' => 'presente',
                'modalidad' => $modalidad,
                'observaciones' => $observaciones,
                'validado_en_horario' => true,
                'justificado_por' => $registradoPor,
                'fecha_justificacion' => now(),
            ]
        );

        return $asistencia;
    }

    // Accessors
    public function getEstadoTextoAttribute()
    {
        $estados = [
            'presente' => 'Presente',
            'ausente' => 'Ausente',
            'tardanza' => 'Tardanza',
            'justificado' => 'Justificado',
            'en_clase' => 'En Clase',
            'pendiente_qr' => 'QR Generado',
        ];

        return $estados[$this->estado] ?? 'Desconocido';
    }

    public function getEstadoColorAttribute()
    {
        $colores = [
            'presente' => 'success',
            'ausente' => 'danger',
            'tardanza' => 'warning',
            'justificado' => 'info',
            'en_clase' => 'primary',
            'pendiente_qr' => 'warning',
        ];

        return $colores[$this->estado] ?? 'secondary';
    }

    public function getDuracionClaseAttribute()
    {
        if (!$this->hora_entrada || !$this->hora_salida) {
            return null;
        }

        try {
            // Intentar diferentes formatos de fecha
            if (strlen($this->hora_entrada) > 5) {
                // Formato completo con fecha
                $entrada = Carbon::parse($this->hora_entrada);
                $salida = Carbon::parse($this->hora_salida);
            } else {
                // Solo formato de hora
                $entrada = Carbon::createFromFormat('H:i', $this->hora_entrada);
                $salida = Carbon::createFromFormat('H:i', $this->hora_salida);
            }

            return $entrada->diffInMinutes($salida);
        } catch (\Exception $e) {
            return null;
        }
    }
}