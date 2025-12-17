<?php

namespace App\Http\Controllers;

use App\Models\Calificacion;
use App\Models\Grupo;
use App\Models\Inscripcion;
use App\Models\TipoEvaluacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Dompdf\Dompdf;
use Dompdf\Options;

class CalificacionController extends Controller
{
    /**
     * Muestra la lista de grupos del profesor para calificar.
     */
    public function indexDocente()
    {
        $profesor = AuthController::getAuthenticatedUser();
        
        if (!$profesor) {
            \Log::error('CalificacionController::indexDocente - Profesor no autenticado');
            return redirect()->route('login')->with('error', 'Debe iniciar sesión como profesor.');
        }
        
        // Obtener grupos asignados al profesor (a través de Carga Académica -> Grupo -> Materia)
        $grupos = Grupo::whereHas('cargaAcademica', function ($query) use ($profesor) {
            $query->where('profesor_id', $profesor->id);
        })
        ->with('materia')
        ->where('estado', 'activo')
        ->get();

        return view('profesor.calificaciones.index', compact('grupos'));
    }

    /**
     * Muestra la interfaz de gestión de notas para un grupo específico.
     */
    public function gestionNotas($grupo_id)
    {
        $profesor = AuthController::getAuthenticatedUser();
        
        // Debug logging
        \Log::info('CalificacionController::gestionNotas - Debug:', [
            'grupo_id' => $grupo_id,
            'profesor' => $profesor ? $profesor->toArray() : null,
            'session_data' => [
                'user_id' => session('user_id'),
                'user_type' => session('user_type'),
                'profesor_id' => session('profesor_id')
            ]
        ]);
        
        if (!$profesor) {
            \Log::error('CalificacionController::gestionNotas - Profesor no autenticado');
            return redirect()->route('login')->with('error', 'Debe iniciar sesión como profesor.');
        }
        
        $grupo = Grupo::whereHas('cargaAcademica', function ($query) use ($profesor) {
                $query->where('profesor_id', $profesor->id);
            })
            ->with(['materia', 'inscripciones.estudiante', 'inscripciones.calificaciones'])
            ->findOrFail($grupo_id);

        // Obtener Criterios de Evaluación
        $tiposEvaluacion = TipoEvaluacion::where('grupo_id', $grupo_id)->where('estado', 'activo')->get();

        // Si no existen criterios personalizados para este grupo, clonar los globales por defecto
        if ($tiposEvaluacion->isEmpty()) {
            $defaults = TipoEvaluacion::whereNull('grupo_id')->where('estado', 'activo')->get();
            foreach ($defaults as $default) {
                TipoEvaluacion::create([
                    'grupo_id' => $grupo_id,
                    'nombre' => $default->nombre,
                    'ponderacion' => $default->ponderacion,
                    'estado' => 'activo'
                ]);
            }
            $tiposEvaluacion = TipoEvaluacion::where('grupo_id', $grupo_id)->where('estado', 'activo')->get();
        }

        // Obtener IDs de estudiantes inscritos
        $inscripciones = $grupo->inscripciones()->where('estado', 'activo')->get()->sortBy('estudiante.apellidos');

        return view('profesor.calificaciones.gestion', compact('grupo', 'tiposEvaluacion', 'inscripciones'));
    }

    // CRUD de Criterios de Evaluación

    public function storeCriterio(Request $request, $grupo_id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ponderacion' => 'required|integer|min:0|max:100',
        ]);

        TipoEvaluacion::create([
            'grupo_id' => $grupo_id,
            'nombre' => $request->nombre,
            'ponderacion' => $request->ponderacion,
            'estado' => 'activo'
        ]);

        return back()->with('success', 'Criterio agregado correctamente.');
    }

    public function updateCriterio(Request $request, $criterio_id)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'ponderacion' => 'required|integer|min:0|max:100',
        ]);

        $criterio = TipoEvaluacion::findOrFail($criterio_id);
        $criterio->update($request->only('nombre', 'ponderacion'));

        return back()->with('success', 'Criterio actualizado correctamente.');
    }

    public function destroyCriterio($criterio_id)
    {
        $criterio = TipoEvaluacion::findOrFail($criterio_id);
        // Verificar si tiene calificaciones asociadas antes de eliminar?
        // El usuario dijo "quiero opción de eliminar".
        // Si hay notas, cascade delete se encargará (o soft delete si se prefiere).
        // En migration puse onDelete('cascade') a la tabla calificaciones, pero no a criterios?
        // Ah, en calificaciones migration: $table->foreignId('tipo_evaluacion_id')->constrained()->onDelete('cascade');
        // Entonces se borrarán las notas asociadas. Advertencia visual en frontend sería bueno.
        
        $criterio->delete();

        return back()->with('success', 'Criterio eliminado correctamente.');
    }

    /**
     * Guarda las calificaciones enviadas desde el formulario.
     */
    public function store(Request $request)
    {
        // Log para debug
        \Log::info('CalificacionController::store - Datos recibidos:', [
            'all_data' => $request->all(),
            'grupo_id' => $request->grupo_id,
            'tipo_evaluacion_id' => $request->tipo_evaluacion_id,
            'notas' => $request->notas,
            'user_session' => session()->all()
        ]);

        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'tipo_evaluacion_id' => 'required|exists:tipos_evaluacion,id',
            'notas' => 'required|array',
            'notas.*' => 'nullable|numeric|min:0|max:100', // Sistema de 0-100 puntos
        ]);

        $tipoEvaluacionId = $request->tipo_evaluacion_id;
        $notas = $request->notas;

        \Log::info('CalificacionController::store - Después de validación:', [
            'tipo_evaluacion_id' => $tipoEvaluacionId,
            'notas_count' => count($notas),
            'notas_with_values' => array_filter($notas, function($nota) {
                return $nota !== null && $nota !== '';
            })
        ]);

        DB::beginTransaction();
        try {
            $guardadas = 0;
            foreach ($notas as $inscripcionId => $notaValor) {
                if ($notaValor !== null && $notaValor !== '') {
                    \Log::info('Guardando calificación:', [
                        'inscripcion_id' => $inscripcionId,
                        'tipo_evaluacion_id' => $tipoEvaluacionId,
                        'nota' => $notaValor
                    ]);

                    $calificacion = Calificacion::updateOrCreate(
                        [
                            'inscripcion_id' => $inscripcionId,
                            'tipo_evaluacion_id' => $tipoEvaluacionId,
                        ],
                        [
                            'nota' => $notaValor,
                            'fecha' => now(),
                        ]
                    );

                    \Log::info('Calificación guardada:', [
                        'id' => $calificacion->id,
                        'nota' => $calificacion->nota,
                        'created' => $calificacion->wasRecentlyCreated
                    ]);

                    $guardadas++;
                }
            }
            
            DB::commit();
            
            \Log::info('CalificacionController::store - Éxito:', [
                'calificaciones_guardadas' => $guardadas
            ]);

            return back()->with('success', "Calificaciones guardadas correctamente. ($guardadas notas procesadas)");
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('CalificacionController::store - Error:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Error al guardar calificaciones: ' . $e->getMessage());
        }
    }

    /**
     * Muestra el resumen de calificaciones (Sábana de notas).
     */
    public function resumen($grupo_id)
    {
        $profesor = AuthController::getAuthenticatedUser();
        
        $grupo = Grupo::whereHas('cargaAcademica', function ($query) use ($profesor) {
                $query->where('profesor_id', $profesor->id);
            })
            ->with(['materia', 'inscripciones.estudiante', 'inscripciones.calificaciones.tipoEvaluacion'])
            ->findOrFail($grupo_id);

        $tiposEvaluacion = TipoEvaluacion::where('grupo_id', $grupo_id)
            ->where('estado', 'activo')
            ->orderBy('id')
            ->get();
            
        // Fallback visual si no hay criterios (aunque gestionNotas los crea, resumen podría visitarse antes)
        if ($tiposEvaluacion->isEmpty()) {
             $tiposEvaluacion = TipoEvaluacion::whereNull('grupo_id')->where('estado', 'activo')->get();
        }
            
        // Preparar datos para la vista
        $datos = [];
        
        foreach ($grupo->inscripciones as $inscripcion) {
            if ($inscripcion->estado !== 'activo' || !$inscripcion->estudiante) continue;
            
            $estudianteId = $inscripcion->estudiante->id;
            $notasEstudiante = [];
            $notaFinalAcumulada = 0;
            
            // Inicializar notas en vacío
            foreach ($tiposEvaluacion as $tipo) {
                $notasEstudiante[$tipo->id] = '-';
            }
            
            // Llenar con notas existentes
            foreach ($inscripcion->calificaciones as $calificacion) {
                if (!$calificacion->tipoEvaluacion) continue;
                
                $notasEstudiante[$calificacion->tipoEvaluacion->id] = $calificacion->nota;
                
                // Calcular aporte al promedio final
                // Sistema de 0-100 puntos con ponderación %
                $ponderacion = $calificacion->tipoEvaluacion->ponderacion;
                $puntos = ($calificacion->nota / 100) * $ponderacion;
                $notaFinalAcumulada += $puntos;
            }
            
            $notaFinal = round($notaFinalAcumulada, 2);
            $estado = $notaFinal >= 51 ? 'Aprobado' : 'Reprobado';
            
            $datos[] = [
                'estudiante' => $inscripcion->estudiante,
                'codigo' => $inscripcion->estudiante->codigo_estudiante,
                'notas' => $notasEstudiante,
                'promedio' => $notaFinal,
                'estado' => $estado
            ];
        }
        
        // Ordenar por apellido
        usort($datos, function ($a, $b) {
            return strcmp($a['estudiante']->apellido, $b['estudiante']->apellido);
        });

        return view('profesor.calificaciones.resumen', compact('grupo', 'tiposEvaluacion', 'datos'));
    }

    /**
     * Genera el PDF del resumen de calificaciones.
     */
    public function imprimirResumen($grupo_id)
    {
        $profesor = AuthController::getAuthenticatedUser();
        
        $grupo = Grupo::whereHas('cargaAcademica', function ($query) use ($profesor) {
                $query->where('profesor_id', $profesor->id);
            })
            ->with(['materia', 'inscripciones.estudiante', 'inscripciones.calificaciones.tipoEvaluacion'])
            ->findOrFail($grupo_id);

        $tiposEvaluacion = TipoEvaluacion::where('grupo_id', $grupo_id)
            ->where('estado', 'activo')
            ->orderBy('id')
            ->get();

        if ($tiposEvaluacion->isEmpty()) {
             $tiposEvaluacion = TipoEvaluacion::whereNull('grupo_id')->where('estado', 'activo')->get();
        }
            
        // Preparar datos (misma lógica que resumen)
        $datos = [];
        
        foreach ($grupo->inscripciones as $inscripcion) {
            if ($inscripcion->estado !== 'activo' || !$inscripcion->estudiante) continue;
            
            $estudianteId = $inscripcion->estudiante->id;
            $notasEstudiante = [];
            $notaFinalAcumulada = 0;
            
            foreach ($tiposEvaluacion as $tipo) {
                $notasEstudiante[$tipo->id] = '-';
            }
            
            foreach ($inscripcion->calificaciones as $calificacion) {
                if (!$calificacion->tipoEvaluacion) continue;
                
                $notasEstudiante[$calificacion->tipoEvaluacion->id] = $calificacion->nota;
                
                $ponderacion = $calificacion->tipoEvaluacion->ponderacion;
                $puntos = ($calificacion->nota / 100) * $ponderacion;
                $notaFinalAcumulada += $puntos;
            }
            
            $notaFinal = round($notaFinalAcumulada, 2);
            $estado = $notaFinal >= 51 ? 'Aprobado' : 'Reprobado';
            
            $datos[] = [
                'estudiante' => $inscripcion->estudiante,
                'codigo' => $inscripcion->estudiante->codigo_estudiante,
                'notas' => $notasEstudiante,
                'promedio' => $notaFinal,
                'estado' => $estado
            ];
        }
        
        usort($datos, function ($a, $b) {
            return strcmp($a['estudiante']->apellido, $b['estudiante']->apellido);
        });

        $html = view('profesor.calificaciones.pdf', compact('grupo', 'tiposEvaluacion', 'datos'))->render();

        $options = new \Dompdf\Options();
        $options->set('defaultFont', 'Arial');
        
        $dompdf = new \Dompdf\Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="Resumen_Notas_' . $grupo->materia->codigo . '.pdf"');
    }

    /**
     * Vista de estudiante para ver sus notas.
     */
    public function indexEstudiante()
    {
        $estudiante = AuthController::getAuthenticatedUser();
        
        $inscripciones = Inscripcion::with(['grupo.materia', 'calificaciones.tipoEvaluacion'])
            ->where('estudiante_id', $estudiante->id)
            ->where('estado', 'activo')
            ->get();

        // Calcular promedios
        foreach ($inscripciones as $inscripcion) {
            $sumaPonderada = 0;
            $totalPonderacion = 0;
            
            foreach ($inscripcion->calificaciones as $calificacion) {
                $ponderacion = $calificacion->tipoEvaluacion->ponderacion;
                $sumaPonderada += $calificacion->nota * ($ponderacion / 100);
                $totalPonderacion += $ponderacion;
            }
            
            // Si la ponderación no suma 100, ajustamos o mostramos acumulado
            $inscripcion->promedio = $sumaPonderada;
        }

        return view('estudiante.calificaciones.index', compact('inscripciones'));
    }
}
