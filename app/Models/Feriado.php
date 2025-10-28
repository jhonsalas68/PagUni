<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Feriado extends Model
{
    use HasFactory;

    protected $fillable = [
        'fecha_inicio',
        'fecha_fin',
        'descripcion',
        'tipo',
        'activo'
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'activo' => 'boolean',
    ];

    /**
     * Reglas de validación
     */
    public static function rules($id = null)
    {
        return [
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'descripcion' => 'required|string|max:255',
            'tipo' => 'required|in:feriado,receso,asueto',
            'activo' => 'boolean'
        ];
    }

    /**
     * Verificar si el feriado está activo
     */
    public function isActive()
    {
        return $this->activo;
    }

    /**
     * Verificar si una fecha está en el rango del feriado
     */
    public function isInRange($date)
    {
        $fecha = Carbon::parse($date);
        $inicio = $this->fecha_inicio;
        $fin = $this->fecha_fin ?? $this->fecha_inicio;
        
        return $fecha->between($inicio, $fin);
    }

    /**
     * Verificar superposición con otro rango de fechas
     */
    public function overlaps($startDate, $endDate = null)
    {
        $start = Carbon::parse($startDate);
        $end = $endDate ? Carbon::parse($endDate) : $start;
        
        $feriadoStart = $this->fecha_inicio;
        $feriadoEnd = $this->fecha_fin ?? $this->fecha_inicio;
        
        return $start->lte($feriadoEnd) && $end->gte($feriadoStart);
    }

    /**
     * Scope para feriados activos
     */
    public function scopeActive($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para feriados en un período específico
     */
    public function scopeInPeriod($query, $start, $end)
    {
        return $query->where(function($q) use ($start, $end) {
            $q->where('fecha_inicio', '<=', $end)
              ->where(function($q2) use ($start) {
                  $q2->whereNull('fecha_fin')
                     ->where('fecha_inicio', '>=', $start);
              })
              ->orWhere(function($q3) use ($start) {
                  $q3->whereNotNull('fecha_fin')
                     ->where('fecha_fin', '>=', $start);
              });
        });
    }

    /**
     * Verificar si una fecha específica es feriado
     */
    public static function esFeriado($fecha)
    {
        $fecha = Carbon::parse($fecha);
        
        return self::active()
            ->where(function($query) use ($fecha) {
                $query->where('fecha_inicio', '<=', $fecha)
                      ->where(function($q) use ($fecha) {
                          $q->whereNull('fecha_fin')
                            ->where('fecha_inicio', $fecha)
                            ->orWhere(function($q2) use ($fecha) {
                                $q2->whereNotNull('fecha_fin')
                                   ->where('fecha_fin', '>=', $fecha);
                            });
                      });
            })
            ->exists();
    }

    /**
     * Obtener feriados activos para un período
     */
    public static function getActiveFeriados($fechaInicio, $fechaFin)
    {
        return self::active()
            ->inPeriod($fechaInicio, $fechaFin)
            ->orderBy('fecha_inicio')
            ->get();
    }

    /**
     * Validar superposición con feriados existentes
     */
    public static function checkOverlap($fechaInicio, $fechaFin = null, $excluirId = null)
    {
        $query = self::active();
        
        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }
        
        $end = $fechaFin ?? $fechaInicio;
        
        $conflictos = $query->where(function($q) use ($fechaInicio, $end) {
            $q->where('fecha_inicio', '<=', $end)
              ->where(function($q2) use ($fechaInicio) {
                  $q2->whereNull('fecha_fin')
                     ->where('fecha_inicio', '>=', $fechaInicio)
                     ->orWhere(function($q3) use ($fechaInicio) {
                         $q3->whereNotNull('fecha_fin')
                            ->where('fecha_fin', '>=', $fechaInicio);
                     });
              });
        })->get();
        
        return [
            'tiene_conflicto' => $conflictos->isNotEmpty(),
            'conflictos' => $conflictos,
            'mensaje' => $conflictos->isNotEmpty() 
                ? 'Error: La fecha se superpone con un feriado ya registrado: ' . $conflictos->pluck('descripcion')->implode(', ')
                : 'Sin conflictos'
        ];
    }

    /**
     * Obtener días lectivos en un rango (excluyendo feriados)
     */
    public static function getDiasLectivos($fechaInicio, $fechaFin)
    {
        $inicio = Carbon::parse($fechaInicio);
        $fin = Carbon::parse($fechaFin);
        $diasLectivos = [];
        
        while ($inicio->lte($fin)) {
            if (!self::esFeriado($inicio) && $inicio->isWeekday()) {
                $diasLectivos[] = $inicio->copy();
            }
            $inicio->addDay();
        }
        
        return $diasLectivos;
    }

    /**
     * Accessor para mostrar el rango de fechas
     */
    public function getRangoFechasAttribute()
    {
        if ($this->fecha_fin) {
            return $this->fecha_inicio->format('d/m/Y') . ' - ' . $this->fecha_fin->format('d/m/Y');
        }
        
        return $this->fecha_inicio->format('d/m/Y');
    }

    /**
     * Accessor para mostrar el tipo formateado
     */
    public function getTipoFormateadoAttribute()
    {
        $tipos = [
            'feriado' => 'Feriado',
            'receso' => 'Receso',
            'asueto' => 'Asueto'
        ];
        
        return $tipos[$this->tipo] ?? ucfirst($this->tipo);
    }
}
