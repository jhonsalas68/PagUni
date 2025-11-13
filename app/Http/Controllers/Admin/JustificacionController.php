<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AsistenciaDocente;
use Illuminate\Http\Request;
use Carbon\Carbon;

class JustificacionController extends Controller
{
    /**
     * Mostrar lista de justificaciones
     */
    public function index(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->endOfMonth()->format('Y-m-d'));
        $tipo = $request->get('tipo');

        $query = AsistenciaDocente::with(['profesor', 'horario.cargaAcademica.grupo.materia', 'horario.aula'])
            ->whereNotNull('justificacion')
            ->whereBetween('fecha', [$fechaInicio, $fechaFin]);

        if ($tipo) {
            $query->where('tipo_justificacion', $tipo);
        }

        $justificaciones = $query->orderBy('fecha_justificacion', 'desc')
            ->paginate(15)
            ->withQueryString();

        // Estadísticas
        $estadisticas = [
            'medica' => AsistenciaDocente::whereNotNull('justificacion')
                ->where('tipo_justificacion', 'medica')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->count(),
            'personal' => AsistenciaDocente::whereNotNull('justificacion')
                ->where('tipo_justificacion', 'personal')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->count(),
            'academica' => AsistenciaDocente::whereNotNull('justificacion')
                ->where('tipo_justificacion', 'academica')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->count(),
            'administrativa' => AsistenciaDocente::whereNotNull('justificacion')
                ->where('tipo_justificacion', 'administrativa')
                ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                ->count(),
        ];

        return response()
            ->view('admin.justificaciones.index', compact('justificaciones', 'estadisticas'))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
