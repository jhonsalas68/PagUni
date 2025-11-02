<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'accion',
        'modulo',
        'descripcion',
        'datos_anteriores',
        'datos_nuevos',
        'usuario_tipo',
        'usuario_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
    ];

    public static function registrar($accion, $modulo, $descripcion, $datosAnteriores = null, $datosNuevos = null)
    {
        return self::create([
            'accion' => $accion,
            'modulo' => $modulo,
            'descripcion' => $descripcion,
            'datos_anteriores' => $datosAnteriores,
            'datos_nuevos' => $datosNuevos,
            'usuario_tipo' => 'sistema',
            'usuario_id' => null,
            'ip_address' => request()->ip() ?? 'localhost',
            'user_agent' => request()->userAgent() ?? 'Sistema',
        ]);
    }
}
