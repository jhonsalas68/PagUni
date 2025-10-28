<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Horario;
use App\Models\CargaAcademica;
use App\Models\Aula;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $horarios = Horario::with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula'])
                          ->orderBy('dia_semana')
                          ->orderBy('hora_inicio')
                          ->get();
        
        return view('admin.horarios.index', compact('horarios'));
    }

    public function create()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $cargasAcademicas = CargaAcademica::with(['profesor', 'grupo.materia'])->get();
        $aulas = Aula::where('estado', 'disponible')->orderBy('codigo_aula')->get();
        
        // Obtener horarios existentes para mostrar en el formulario
        $horariosExistentes = Horario::with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula'])
                                   ->orderBy('periodo_academico', 'desc')
                                   ->orderBy('dia_semana')
                                   ->orderBy('hora_inicio')
                                   ->get();
        
        return view('admin.horarios.create', compact('cargasAcademicas', 'aulas', 'horariosExistentes'));
    }

    public function store(Request $request)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        $validationRules = [
            'carga_academica_id' => 'required|exists:carga_academica,id',
            'dias_semana' => 'required|array|min:1',
            'dias_semana.*' => 'integer|min:1|max:7',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'duracion_horas' => 'required|numeric|min:0.1',
            'periodo_academico' => 'required|string|max:20',
            'es_semestral' => 'boolean',
            'fecha_inicio' => 'nullable|date',
            'fecha_fin' => 'nullable|date|after_or_equal:fecha_inicio',
            'semanas_duracion' => 'nullable|integer|min:1|max:20',
            'usar_configuracion_por_dia' => 'boolean',
        ];

        // Si no usa configuración por día, validar aula y tipo_clase globales
        if (!$request->has('usar_configuracion_por_dia')) {
            $validationRules['aula_id'] = 'required|exists:aulas,id';
            $validationRules['tipo_clase'] = 'required|in:teorica,practica,laboratorio';
        } else {
            // Si usa configuración por día, validar cada configuración
            $validationRules['config_dias'] = 'required|array';
            $validationRules['config_dias.*.aula_id'] = 'required|exists:aulas,id';
            $validationRules['config_dias.*.tipo_clase'] = 'required|in:teorica,practica,laboratorio';
        }

        $request->validate($validationRules);

        $horariosCreados = 0;
        $conflictos = [];
        $usarConfiguracionPorDia = $request->has('usar_configuracion_por_dia');

        // Procesar configuración por día si está habilitada
        $configuracionDias = null;
        if ($usarConfiguracionPorDia && $request->has('config_dias')) {
            $configuracionDias = [];
            $configDias = $request->input('config_dias', []);
            foreach ($configDias as $dia => $config) {
                if (in_array($dia, $request->input('dias_semana', []))) {
                    $configuracionDias[] = [
                        'dia' => (int)$dia,
                        'aula_id' => (int)$config['aula_id'],
                        'tipo_clase' => $config['tipo_clase']
                    ];
                }
            }
        }

        // Crear horario para cada día seleccionado
        foreach ($request->dias_semana as $dia) {
            // Determinar aula y tipo de clase para este día
            if ($usarConfiguracionPorDia && $configuracionDias) {
                $configDia = collect($configuracionDias)->firstWhere('dia', (int)$dia);
                $aulaId = $configDia['aula_id'] ?? $request->aula_id;
                $tipoClase = $configDia['tipo_clase'] ?? $request->tipo_clase;
            } else {
                $aulaId = $request->aula_id;
                $tipoClase = $request->tipo_clase;
            }

            // Usar el método mejorado de validación de conflictos
            $validacion = Horario::validarConflictos(
                $request->carga_academica_id,
                $aulaId,
                $dia,
                $request->hora_inicio,
                $request->hora_fin,
                $request->periodo_academico
            );

            if (!$validacion['disponible']) {
                $diasNombres = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                $detallesConflicto = [];
                
                if ($validacion['conflicto_profesor']) {
                    $detallesConflicto[] = "Profesor {$validacion['profesor_nombre']} ocupado";
                }
                if ($validacion['conflicto_aula']) {
                    $detallesConflicto[] = "Aula ocupada";
                }
                
                $conflictos[] = $diasNombres[$dia] . ': ' . implode(' y ', $detallesConflicto);
            } else {
                // Crear el horario para este día
                Horario::create([
                    'carga_academica_id' => $request->carga_academica_id,
                    'aula_id' => $aulaId,
                    'dia_semana' => $dia,
                    'hora_inicio' => $request->hora_inicio,
                    'hora_fin' => $request->hora_fin,
                    'duracion_horas' => $request->duracion_horas,
                    'tipo_clase' => $tipoClase,
                    'periodo_academico' => $request->periodo_academico,
                    'es_semestral' => $request->has('es_semestral'),
                    'fecha_inicio' => $request->fecha_inicio,
                    'fecha_fin' => $request->fecha_fin,
                    'semanas_duracion' => $request->semanas_duracion ?? 16,
                    'configuracion_dias' => $configuracionDias,
                    'usar_configuracion_por_dia' => $usarConfiguracionPorDia,
                ]);
                $horariosCreados++;
            }
        }

        // Preparar mensaje de respuesta
        $mensaje = '';
        if ($horariosCreados > 0) {
            $mensaje = "Se crearon {$horariosCreados} horario(s) exitosamente.";
        }
        
        if (!empty($conflictos)) {
            $diasConflicto = implode(', ', $conflictos);
            if ($horariosCreados > 0) {
                $mensaje .= " Conflictos encontrados en: {$diasConflicto}.";
            } else {
                return back()->withErrors(['error' => "Conflictos de horario encontrados en: {$diasConflicto}"])
                            ->withInput();
            }
        }

        return redirect()->route('admin.horarios.index')
            ->with('success', $mensaje);
    }

    public function show(Horario $horario)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $horario->load(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula']);
        return view('admin.horarios.show', compact('horario'));
    }

    public function edit(Horario $horario)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $cargasAcademicas = CargaAcademica::with(['profesor', 'grupo.materia'])->get();
        $aulas = Aula::where('estado', 'disponible')->orderBy('codigo_aula')->get();
        
        // CU-12: Obtener todos los horarios de la misma carga académica (registros múltiples)
        $horariosMateria = Horario::with(['aula'])
                                 ->where('carga_academica_id', $horario->carga_academica_id)
                                 ->orderBy('dia_semana')
                                 ->orderBy('hora_inicio')
                                 ->get();
        
        // Obtener horarios existentes excluyendo los de la misma materia
        $horariosExistentes = Horario::with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula'])
                                   ->where('carga_academica_id', '!=', $horario->carga_academica_id)
                                   ->orderBy('periodo_academico', 'desc')
                                   ->orderBy('dia_semana')
                                   ->orderBy('hora_inicio')
                                   ->get();
        
        return view('admin.horarios.edit', compact('horario', 'cargasAcademicas', 'aulas', 'horariosExistentes', 'horariosMateria'));
    }

    public function update(Request $request, Horario $horario)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        $validationRules = [
            'carga_academica_id' => 'required|exists:carga_academica,id',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'duracion_horas' => 'required|numeric|min:0.1',
            'periodo_academico' => 'nullable|string|max:20',
            'es_semestral' => 'boolean',
            'usar_configuracion_por_dia' => 'boolean',
        ];

        // Si no usa configuración por día, validar campos simples
        if (!$request->has('usar_configuracion_por_dia')) {
            $validationRules['aula_id'] = 'required|exists:aulas,id';
            $validationRules['dia_semana'] = 'required|integer|min:1|max:7';
            $validationRules['tipo_clase'] = 'required|in:teorica,practica,laboratorio';
        } else {
            // Si usa configuración por día, validar cada configuración
            $validationRules['dias_semana'] = 'required|array|min:1';
            $validationRules['dias_semana.*'] = 'integer|min:1|max:7';
            $validationRules['config_dias'] = 'required|array';
            $validationRules['config_dias.*.aula_id'] = 'required|exists:aulas,id';
            $validationRules['config_dias.*.tipo_clase'] = 'required|in:teorica,practica,laboratorio';
        }

        $request->validate($validationRules);

        // Validación adicional para asegurar que aula_id no sea null
        if (!$request->has('usar_configuracion_por_dia')) {
            if (!$request->aula_id) {
                return response()->json([
                    'success' => false,
                    'error' => 'Debe seleccionar un aula.',
                    'field' => 'aula_id'
                ], 422);
            }
        } else {
            // Si usa configuración por día, usar la primera aula configurada como aula principal
            if ($request->has('config_dias') && is_array($request->input('config_dias'))) {
                $configDias = $request->input('config_dias');
                $primeraConfiguracion = reset($configDias);
                if (isset($primeraConfiguracion['aula_id'])) {
                    // Crear un nuevo array con todos los datos del request más el aula_id
                    $requestData = $request->all();
                    $requestData['aula_id'] = $primeraConfiguracion['aula_id'];
                    $request->replace($requestData);
                }
            }
        }

        // LOG DE DATOS PARA UPDATE
        \Log::info('=== MÉTODO UPDATE - VALIDACIÓN FINAL ===', [
            'horario_id' => $horario->id,
            'datos_request' => [
                'carga_academica_id' => $request->carga_academica_id,
                'aula_id' => $request->aula_id,
                'dia_semana' => $request->dia_semana,
                'hora_inicio' => $request->hora_inicio,
                'hora_fin' => $request->hora_fin,
                'periodo_academico' => $request->periodo_academico ?? $horario->periodo_academico
            ]
        ]);

        // Validar conflictos excluyendo el horario actual
        $validacion = Horario::validarConflictos(
            $request->carga_academica_id,
            $request->aula_id,
            $request->dia_semana,
            $request->hora_inicio,
            $request->hora_fin,
            $request->periodo_academico ?? $horario->periodo_academico,
            $horario->id // Excluir el horario actual
        );

        \Log::info('Resultado validación en UPDATE', [
            'disponible' => $validacion['disponible'],
            'conflicto_profesor' => $validacion['conflicto_profesor'],
            'conflicto_aula' => $validacion['conflicto_aula'],
            'total_conflictos' => $validacion['total_conflictos'] ?? 0
        ]);

        if (!$validacion['disponible']) {
            $mensajesError = [];
            
            if ($validacion['conflicto_profesor']) {
                $mensajesError[] = "Conflicto de profesor: {$validacion['profesor_nombre']} ya tiene clase asignada en este horario.";
            }
            
            if ($validacion['conflicto_aula']) {
                $mensajesError[] = "Conflicto de aula: El aula ya está ocupada en este horario.";
            }
            
            \Log::warning('UPDATE RECHAZADO por conflictos', [
                'mensajes_error' => $mensajesError,
                'detalles_conflictos' => $validacion
            ]);
            
            return back()->withErrors(['error' => implode(' ', $mensajesError)])->withInput();
        }

        // NOTA: La validación de conflictos ya se hizo arriba con validarConflictos()
        // Esta validación duplicada se elimina para evitar inconsistencias
        // La validación completa y correcta está en el método validarConflictos()

        // Registrar cambios para auditoría
        $cambiosRealizados = [];
        $camposAComparar = [
            'carga_academica_id' => 'Carga Académica',
            'aula_id' => 'Aula',
            'dia_semana' => 'Día',
            'hora_inicio' => 'Hora Inicio',
            'hora_fin' => 'Hora Fin',
            'tipo_clase' => 'Tipo de Clase'
        ];

        foreach ($camposAComparar as $campo => $nombre) {
            if ($horario->$campo != $request->$campo) {
                $valorAnterior = $horario->$campo;
                $valorNuevo = $request->$campo;
                
                // Formatear valores para mejor legibilidad
                if ($campo === 'dia_semana') {
                    $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                    $valorAnterior = $dias[$valorAnterior] ?? $valorAnterior;
                    $valorNuevo = $dias[$valorNuevo] ?? $valorNuevo;
                } elseif ($campo === 'aula_id') {
                    $aulaAnterior = \App\Models\Aula::find($valorAnterior);
                    $aulaNueva = \App\Models\Aula::find($valorNuevo);
                    $valorAnterior = $aulaAnterior ? $aulaAnterior->codigo_aula : $valorAnterior;
                    $valorNuevo = $aulaNueva ? $aulaNueva->codigo_aula : $valorNuevo;
                }
                
                $cambiosRealizados[] = "{$nombre}: {$valorAnterior} → {$valorNuevo}";
            }
        }

        // Preparar datos para actualización
        $datosActualizacion = [
            'carga_academica_id' => $request->carga_academica_id,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'duracion_horas' => $request->duracion_horas,
            'periodo_academico' => $request->periodo_academico ?? $horario->periodo_academico,
            'es_semestral' => $request->has('es_semestral'),
        ];

        // Solo actualizar campos simples si no usa configuración por día
        if (!$request->has('usar_configuracion_por_dia')) {
            $datosActualizacion['aula_id'] = $request->aula_id;
            $datosActualizacion['dia_semana'] = $request->dia_semana;
            $datosActualizacion['tipo_clase'] = $request->tipo_clase;
            $datosActualizacion['usar_configuracion_por_dia'] = false;
            $datosActualizacion['configuracion_dias'] = null;
        } else {
            // Configuración por día
            $datosActualizacion['usar_configuracion_por_dia'] = true;
            $datosActualizacion['configuracion_dias'] = $request->input('config_dias', []);
            
            // Usar la primera configuración como valores principales
            $configDias = $request->input('config_dias', []);
            if (!empty($configDias) && is_array($configDias)) {
                $primeraConfig = reset($configDias);
                $datosActualizacion['aula_id'] = $primeraConfig['aula_id'] ?? $horario->aula_id;
                $datosActualizacion['tipo_clase'] = $primeraConfig['tipo_clase'] ?? $horario->tipo_clase;
                $diasSemana = $request->input('dias_semana', []);
                $datosActualizacion['dia_semana'] = !empty($diasSemana) ? $diasSemana[0] : $horario->dia_semana;
            }
        }

        $horario->update($datosActualizacion);

        $mensaje = 'Horario reasignado exitosamente.';
        if (!empty($cambiosRealizados)) {
            $mensaje .= ' Cambios realizados: ' . implode(', ', $cambiosRealizados);
        }

        return redirect()->route('admin.horarios.index')
            ->with('success', $mensaje);
    }

    public function destroy(Horario $horario)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        $horario->delete();

        return redirect()->route('admin.horarios.index')
            ->with('success', 'Horario eliminado exitosamente.');
    }

    /**
     * API endpoint para validar disponibilidad en tiempo real
     */
    public function validarDisponibilidad(Request $request)
    {
        // LOG DETALLADO DE LA PETICIÓN
        \Log::info('=== VALIDACIÓN DE DISPONIBILIDAD INICIADA ===', [
            'timestamp' => now()->toDateTimeString(),
            'datos_recibidos' => $request->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        $request->validate([
            'carga_academica_id' => 'required|exists:carga_academica,id',
            'aula_id' => 'required|exists:aulas,id',
            'dia_semana' => 'required|integer|min:1|max:7',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'periodo_academico' => 'required|string|max:20',
            'excluir_id' => 'nullable|integer',
        ]);

        // LOG DE DATOS VALIDADOS
        \Log::info('Datos validados correctamente', [
            'carga_academica_id' => $request->carga_academica_id,
            'aula_id' => $request->aula_id,
            'dia_semana' => $request->dia_semana,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'periodo_academico' => $request->periodo_academico,
            'excluir_id' => $request->excluir_id
        ]);

        // Obtener información adicional para el log
        $aula = \App\Models\Aula::find($request->aula_id);
        $cargaAcademica = \App\Models\CargaAcademica::with(['profesor', 'grupo.materia'])->find($request->carga_academica_id);
        
        \Log::info('Información contextual', [
            'aula_codigo' => $aula ? $aula->codigo_aula : 'N/A',
            'profesor' => $cargaAcademica && $cargaAcademica->profesor ? $cargaAcademica->profesor->nombre_completo : 'N/A',
            'materia' => $cargaAcademica && $cargaAcademica->grupo && $cargaAcademica->grupo->materia ? $cargaAcademica->grupo->materia->nombre : 'N/A'
        ]);

        $validacion = Horario::validarConflictos(
            $request->carga_academica_id,
            $request->aula_id,
            $request->dia_semana,
            $request->hora_inicio,
            $request->hora_fin,
            $request->periodo_academico,
            $request->excluir_id
        );

        // LOG DEL RESULTADO DE VALIDACIÓN
        \Log::info('Resultado de validación obtenido', [
            'disponible' => $validacion['disponible'],
            'conflicto_profesor' => $validacion['conflicto_profesor'],
            'conflicto_aula' => $validacion['conflicto_aula'],
            'total_conflictos' => $validacion['total_conflictos'] ?? 0,
            'detalles_profesor_count' => count($validacion['detalles_profesor'] ?? []),
            'detalles_aula_count' => count($validacion['detalles_aula'] ?? [])
        ]);

        // Preparar respuesta con información detallada
        $response = [
            'disponible' => $validacion['disponible'],
            'conflictos' => [],
            'mensaje' => '',
        ];

        if (!$validacion['disponible']) {
            $mensajes = [];
            
            if ($validacion['conflicto_profesor']) {
                $detalleProfesor = $validacion['detalles_profesor'][0] ?? [];
                $materiaConflicto = $detalleProfesor['materia'] ?? 'otra materia';
                $aulaConflicto = $detalleProfesor['aula'] ?? 'N/A';
                $grupoConflicto = $detalleProfesor['grupo'] ?? '';
                $horarioConflicto = ($detalleProfesor['hora_inicio'] ?? '') . '-' . ($detalleProfesor['hora_fin'] ?? '');
                
                $grupoTexto = $grupoConflicto ? " (Grupo {$grupoConflicto})" : '';
                
                $mensajes[] = "🚫 CONFLICTO DE DOCENTE: El profesor {$validacion['profesor_nombre']} ya tiene clase de '{$materiaConflicto}'{$grupoTexto} en aula {$aulaConflicto} de {$horarioConflicto}";
                $response['conflictos']['profesor'] = [
                    'tipo' => 'profesor',
                    'mensaje' => "Conflicto de Docente: {$validacion['profesor_nombre']} ya tiene clase asignada",
                    'detalles' => $validacion['detalles_profesor'],
                    'detalle_especifico' => "Ya tiene clase de '{$materiaConflicto}'{$grupoTexto} en aula {$aulaConflicto} de {$horarioConflicto}",
                    'materia_conflicto' => $materiaConflicto,
                    'aula_conflicto' => $aulaConflicto,
                    'grupo_conflicto' => $grupoConflicto,
                    'horario_conflicto' => $horarioConflicto
                ];
            }

            if ($validacion['conflicto_aula']) {
                $detalleAula = $validacion['detalles_aula'][0] ?? [];
                $profesorConflicto = $detalleAula['profesor'] ?? 'otro profesor';
                $materiaConflicto = $detalleAula['materia'] ?? 'otra materia';
                $grupoConflicto = $detalleAula['grupo'] ?? '';
                $horarioConflicto = ($detalleAula['hora_inicio'] ?? '') . '-' . ($detalleAula['hora_fin'] ?? '');
                
                $aula = \App\Models\Aula::find($request->aula_id);
                $aulaNombre = $aula ? $aula->codigo_aula : 'N/A';
                $grupoTexto = $grupoConflicto ? " (Grupo {$grupoConflicto})" : '';
                
                $mensajes[] = "🚫 CONFLICTO DE AULA: El aula {$aulaNombre} ya está ocupada por {$profesorConflicto} con la materia '{$materiaConflicto}'{$grupoTexto} de {$horarioConflicto}";
                $response['conflictos']['aula'] = [
                    'tipo' => 'aula',
                    'mensaje' => "Conflicto de Aula: {$aulaNombre} ya está ocupada",
                    'detalles' => $validacion['detalles_aula'],
                    'detalle_especifico' => "Ocupada por {$profesorConflicto} con '{$materiaConflicto}'{$grupoTexto} de {$horarioConflicto}",
                    'profesor_conflicto' => $profesorConflicto,
                    'materia_conflicto' => $materiaConflicto,
                    'grupo_conflicto' => $grupoConflicto,
                    'horario_conflicto' => $horarioConflicto
                ];
            }

            $response['mensaje'] = implode('. ', $mensajes);
            $response['total_conflictos'] = $validacion['total_conflictos'] ?? 0;
        } else {
            $response['mensaje'] = "Horario disponible para {$validacion['profesor_nombre']} - {$validacion['materia_nombre']}";
        }

        // LOG DE LA RESPUESTA FINAL
        \Log::info('=== RESPUESTA FINAL DE VALIDACIÓN ===', [
            'disponible' => $response['disponible'],
            'mensaje' => $response['mensaje'],
            'tiene_conflictos' => !empty($response['conflictos']),
            'tipos_conflicto' => array_keys($response['conflictos']),
            'response_completa' => $response
        ]);

        return response()->json($response);
    }

    /**
     * Obtener logs de validación para debugging
     */
    public function obtenerLogsValidacion(Request $request)
    {
        try {
            // Leer los últimos logs del archivo de Laravel
            $logPath = storage_path('logs/laravel.log');
            
            if (!file_exists($logPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo de logs no encontrado',
                    'logs' => []
                ]);
            }
            
            // Leer las últimas 50 líneas del log
            $lines = [];
            $file = new SplFileObject($logPath, 'r');
            $file->seek(PHP_INT_MAX);
            $totalLines = $file->key();
            
            $startLine = max(0, $totalLines - 100);
            $file->seek($startLine);
            
            $validationLogs = [];
            while (!$file->eof()) {
                $line = $file->current();
                $file->next();
                
                // Filtrar solo logs relacionados con validación
                if (strpos($line, 'VALIDACIÓN DE DISPONIBILIDAD') !== false || 
                    strpos($line, 'validación de conflictos') !== false ||
                    strpos($line, 'Resultado de validación') !== false ||
                    strpos($line, 'Conflictos de') !== false) {
                    
                    $validationLogs[] = [
                        'timestamp' => date('Y-m-d H:i:s'),
                        'message' => trim($line),
                        'context' => null
                    ];
                }
            }
            
            return response()->json([
                'success' => true,
                'logs' => array_slice($validationLogs, -20), // Últimos 20 logs
                'total' => count($validationLogs)
            ]);
            
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener logs: ' . $e->getMessage(),
                'logs' => []
            ]);
        }
    }

    /**
     * Generar sugerencias inteligentes para reasignación
     */
    public function sugerirAlternativas(Request $request, Horario $horario)
    {
        try {
            \Log::info('Iniciando sugerirAlternativas para horario ID: ' . $horario->id);
            
            // Validación simple
            $tipoSugerencia = $request->get('tipo_sugerencia', 'todo');

            // Cargar relaciones necesarias
            $horario->load(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula']);
            
            // Obtener todos los horarios de la misma materia para contexto
            $horariosMateria = Horario::where('carga_academica_id', $horario->carga_academica_id)
                ->with(['aula'])
                ->orderBy('dia_semana')
                ->orderBy('hora_inicio')
                ->get();
            
            \Log::info('Contexto de la materia: ' . $horariosMateria->count() . ' horarios totales');

            $sugerencias = [
                'aulas' => [],
                'horarios' => [],
                'profesores' => [],
                'distribuciones' => [] // Nueva sección para distribuciones inteligentes
            ];

            // Información del contexto de la materia
            $contextoMateria = [
                'horarios_totales' => $horariosMateria->count(),
                'dias_utilizados' => $horariosMateria->pluck('dia_semana')->unique()->sort()->values(),
                'aulas_utilizadas' => $horariosMateria->pluck('aula_id')->unique()->count(),
                'tipos_clase' => $horariosMateria->pluck('tipo_clase')->unique()->values(),
                'horas_semanales_actuales' => $horariosMateria->sum('duracion_horas'),
                'horas_semanales_requeridas' => $horario->cargaAcademica->grupo->materia->horas_semanales ?? 4
            ];

            // Sugerencias de aulas considerando el contexto
            if ($tipoSugerencia === 'aula' || $tipoSugerencia === 'todo') {
                \Log::info('Generando sugerencias de aulas inteligentes...');
                $sugerencias['aulas'] = $this->sugerirAulasInteligentes($horario, $horariosMateria);
                \Log::info('Aulas sugeridas: ' . count($sugerencias['aulas']));
            }

            // Sugerencias de horarios considerando distribución semanal
            if ($tipoSugerencia === 'horario' || $tipoSugerencia === 'todo') {
                \Log::info('Generando sugerencias de horarios inteligentes...');
                $sugerencias['horarios'] = $this->generarSugerenciasHorarioInteligentes($horario, $horariosMateria);
                \Log::info('Horarios sugeridos: ' . count($sugerencias['horarios']));
            }

            // Sugerencias de distribuciones completas (nueva funcionalidad)
            if ($tipoSugerencia === 'distribucion' || $tipoSugerencia === 'todo') {
                \Log::info('Generando distribuciones inteligentes...');
                $sugerencias['distribuciones'] = $this->generarDistribucionesInteligentes($horario, $horariosMateria, $contextoMateria);
                \Log::info('Distribuciones sugeridas: ' . count($sugerencias['distribuciones']));
            }

            // Sugerencias de profesores (opcional)
            if ($tipoSugerencia === 'profesor' || $tipoSugerencia === 'todo') {
                \Log::info('Generando sugerencias de profesores...');
                $sugerencias['profesores'] = $this->sugerirProfesoresAlternativos($horario);
                \Log::info('Profesores sugeridos: ' . count($sugerencias['profesores']));
            }

            $response = [
                'success' => true,
                'sugerencias' => $sugerencias,
                'contexto_materia' => $contextoMateria,
                'horario_actual' => [
                    'id' => $horario->id,
                    'materia' => $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                    'profesor' => $horario->cargaAcademica->profesor->nombre_completo ?? 'N/A',
                    'aula' => $horario->aula->codigo_aula ?? 'N/A',
                    'dia' => $horario->dia_semana,
                    'hora_inicio' => $horario->hora_inicio,
                    'hora_fin' => $horario->hora_fin,
                    'tipo_clase' => $horario->tipo_clase
                ],
                'debug_info' => [
                    'tipo_sugerencia' => $tipoSugerencia,
                    'timestamp' => now(),
                    'total_sugerencias' => array_sum([
                        count($sugerencias['aulas']),
                        count($sugerencias['horarios']),
                        count($sugerencias['profesores']),
                        count($sugerencias['distribuciones'])
                    ])
                ]
            ];

            \Log::info('Respuesta generada exitosamente');
            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Error en sugerirAlternativas: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Error al generar sugerencias: ' . $e->getMessage(),
                'debug' => [
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'horario_id' => $horario->id ?? 'N/A'
                ]
            ], 500);
        }
    }

    private function sugerirAulasAlternativas($horario)
    {
        try {
            $aulasDisponibles = Aula::where('estado', 'disponible')
                ->where('id', '!=', $horario->aula_id)
                ->limit(10) // Limitar para evitar sobrecarga
                ->get();

            $sugerencias = [];
            
            foreach ($aulasDisponibles as $aula) {
                try {
                    $validacion = Horario::validarConflictos(
                        $horario->carga_academica_id,
                        $aula->id,
                        $horario->dia_semana,
                        $horario->hora_inicio,
                        $horario->hora_fin,
                        $horario->periodo_academico ?? '2024-2',
                        $horario->id
                    );

                    if ($validacion['disponible']) {
                        $compatibilidad = $this->calcularCompatibilidadAula($aula, $horario);
                        
                        $sugerencias[] = [
                            'aula_id' => $aula->id,
                            'codigo' => $aula->codigo_aula ?? 'N/A',
                            'nombre' => $aula->nombre ?? 'Sin nombre',
                            'capacidad' => $aula->capacidad ?? 0,
                            'tipo' => $aula->tipo_aula ?? 'general',
                            'compatibilidad' => $compatibilidad,
                            'razon' => $this->obtenerRazonCompatibilidad($compatibilidad)
                        ];
                    }
                } catch (\Exception $e) {
                    // Continuar con la siguiente aula si hay error
                    continue;
                }
            }

            // Ordenar por compatibilidad
            usort($sugerencias, function($a, $b) {
                return $b['compatibilidad'] <=> $a['compatibilidad'];
            });

            return array_slice($sugerencias, 0, 5); // Top 5 sugerencias
        } catch (\Exception $e) {
            \Log::error('Error en sugerirAulasAlternativas: ' . $e->getMessage());
            return [];
        }
    }

    private function sugerirHorariosAlternativos($horario)
    {
        try {
            $sugerencias = [];
            $diasSemana = [1, 2, 3, 4, 5]; // Solo Lunes a Viernes para simplificar
            $horariosComunes = [
                ['08:00', '10:00'], ['10:00', '12:00'], ['14:00', '16:00'], ['16:00', '18:00']
            ];

            foreach ($diasSemana as $dia) {
                if ($dia == $horario->dia_semana) continue;

                foreach ($horariosComunes as $franja) {
                    try {
                        $validacion = Horario::validarConflictos(
                            $horario->carga_academica_id,
                            $horario->aula_id,
                            $dia,
                            $franja[0],
                            $franja[1],
                            $horario->periodo_academico ?? '2024-2',
                            $horario->id
                        );

                        if ($validacion['disponible']) {
                            $dias = ['', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
                            $sugerencias[] = [
                                'dia' => $dia,
                                'dia_nombre' => $dias[$dia] ?? 'N/A',
                                'hora_inicio' => $franja[0],
                                'hora_fin' => $franja[1],
                                'preferencia' => $this->calcularPreferenciaHorario($dia, $franja[0])
                            ];
                        }
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }

            // Ordenar por preferencia
            usort($sugerencias, function($a, $b) {
                return $b['preferencia'] <=> $a['preferencia'];
            });

            return array_slice($sugerencias, 0, 6);
        } catch (\Exception $e) {
            \Log::error('Error en sugerirHorariosAlternativos: ' . $e->getMessage());
            return [];
        }
    }

    private function sugerirProfesoresAlternativos($horario)
    {
        try {
            // Simplificar: solo buscar profesores disponibles básicos
            $profesores = \App\Models\Profesor::where('estado', 'activo')
                ->where('id', '!=', $horario->cargaAcademica->profesor_id ?? 0)
                ->limit(5)
                ->get();

            $sugerencias = [];
            
            foreach ($profesores as $profesor) {
                try {
                    // Verificación básica de disponibilidad
                    $conflictos = Horario::whereHas('cargaAcademica', function($query) use ($profesor) {
                        $query->where('profesor_id', $profesor->id);
                    })
                    ->where('dia_semana', $horario->dia_semana)
                    ->where('periodo_academico', $horario->periodo_academico ?? '2024-2')
                    ->where('id', '!=', $horario->id)
                    ->where('hora_inicio', '<', $horario->hora_fin)
                    ->where('hora_fin', '>', $horario->hora_inicio)
                    ->exists();

                    if (!$conflictos) {
                        $sugerencias[] = [
                            'profesor_id' => $profesor->id,
                            'nombre' => $profesor->nombre_completo ?? 'Sin nombre',
                            'especialidad' => $profesor->especialidad ?? 'General',
                            'experiencia' => $profesor->anos_experiencia ?? 0
                        ];
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            return $sugerencias;
        } catch (\Exception $e) {
            \Log::error('Error en sugerirProfesoresAlternativos: ' . $e->getMessage());
            return [];
        }
    }

    private function calcularCompatibilidadAula($aula, $horario)
    {
        $puntuacion = 50; // Base

        // Compatibilidad por tipo de clase
        $tipoClase = $horario->tipo_clase;
        $tipoAula = $aula->tipo_aula ?? 'general';

        if (($tipoClase === 'laboratorio' && $tipoAula === 'laboratorio') ||
            ($tipoClase === 'practica' && in_array($tipoAula, ['laboratorio', 'taller'])) ||
            ($tipoClase === 'teorica' && in_array($tipoAula, ['aula', 'auditorio', 'general']))) {
            $puntuacion += 30;
        }

        // Compatibilidad por capacidad (asumiendo grupo de 30 estudiantes promedio)
        $capacidadRequerida = 30;
        if ($aula->capacidad >= $capacidadRequerida && $aula->capacidad <= $capacidadRequerida * 1.5) {
            $puntuacion += 20;
        } elseif ($aula->capacidad >= $capacidadRequerida) {
            $puntuacion += 10;
        }

        return min(100, $puntuacion);
    }

    private function obtenerRazonCompatibilidad($compatibilidad)
    {
        if ($compatibilidad >= 90) return 'Excelente compatibilidad';
        if ($compatibilidad >= 70) return 'Buena compatibilidad';
        if ($compatibilidad >= 50) return 'Compatibilidad aceptable';
        return 'Baja compatibilidad';
    }

    /**
     * Funciones auxiliares para cálculos inteligentes
     */
    private function calcularCompatibilidadAulaInteligente($aula, $horario, $aulasUtilizadas)
    {
        $puntuacion = 50; // Base

        // Bonus por consistencia: si ya se usa esta aula para la materia
        if (in_array($aula->id, $aulasUtilizadas)) {
            $puntuacion += 25;
        }

        // Compatibilidad por tipo de clase
        $tipoClase = $horario->tipo_clase;
        $tipoAula = $aula->tipo_aula ?? 'general';

        if (($tipoClase === 'laboratorio' && $tipoAula === 'laboratorio') ||
            ($tipoClase === 'practica' && in_array($tipoAula, ['laboratorio', 'taller'])) ||
            ($tipoClase === 'teorica' && in_array($tipoAula, ['aula', 'auditorio', 'general']))) {
            $puntuacion += 30;
        }

        // Compatibilidad por capacidad
        $capacidadRequerida = 30; // Estimado
        if ($aula->capacidad >= $capacidadRequerida && $aula->capacidad <= $capacidadRequerida * 1.5) {
            $puntuacion += 20;
        } elseif ($aula->capacidad >= $capacidadRequerida) {
            $puntuacion += 10;
        }

        return min(100, $puntuacion);
    }

    private function obtenerRazonCompatibilidadInteligente($aula, $horario, $aulasUtilizadas, $compatibilidad)
    {
        $razones = [];
        
        if (in_array($aula->id, $aulasUtilizadas)) {
            $razones[] = 'Ya utilizada por la materia';
        }
        
        if ($aula->tipo_aula === $horario->tipo_clase || 
            ($horario->tipo_clase === 'teorica' && in_array($aula->tipo_aula, ['aula', 'auditorio']))) {
            $razones[] = 'Tipo compatible';
        }
        
        if ($compatibilidad >= 90) {
            return 'Excelente: ' . implode(', ', $razones);
        } elseif ($compatibilidad >= 70) {
            return 'Buena: ' . implode(', ', $razones);
        } else {
            return 'Aceptable: ' . implode(', ', $razones);
        }
    }

    private function categorizarAula($aula, $horario, $aulasUtilizadas)
    {
        if ($aula->id == $horario->aula_id) return 'actual';
        if (in_array($aula->id, $aulasUtilizadas)) return 'utilizada';
        if ($aula->tipo_aula === $horario->tipo_clase) return 'compatible';
        return 'alternativa';
    }

    private function calcularPreferenciaHorarioInteligente($alt, $horario, $diasUtilizados, $horasUtilizadas)
    {
        $puntuacion = 50;

        // Bonus por consistencia de días (días ya utilizados por la materia)
        if (in_array($alt['dia'], $diasUtilizados)) {
            $puntuacion += 20;
        }

        // Bonus por consistencia de horarios
        foreach ($horasUtilizadas as $horaUsada) {
            if ($alt['hora_inicio'] === $horaUsada['inicio']) {
                $puntuacion += 15;
                break;
            }
        }

        // Preferencia por días (Lunes-Viernes)
        if ($alt['dia'] >= 1 && $alt['dia'] <= 5) {
            $puntuacion += 15;
        }

        // Preferencia por horarios
        $horaNum = (int)substr($alt['hora_inicio'], 0, 2);
        if ($horaNum >= 8 && $horaNum <= 12) {
            $puntuacion += 20; // Mañana
        } elseif ($horaNum >= 14 && $horaNum <= 18) {
            $puntuacion += 15; // Tarde
        }

        return min(100, $puntuacion);
    }

    private function obtenerRazonPreferencia($alt, $diasUtilizados, $horasUtilizadas)
    {
        $razones = [];
        
        if (in_array($alt['dia'], $diasUtilizados)) {
            $razones[] = 'Día ya utilizado';
        }
        
        foreach ($horasUtilizadas as $horaUsada) {
            if ($alt['hora_inicio'] === $horaUsada['inicio']) {
                $razones[] = 'Horario consistente';
                break;
            }
        }
        
        $horaNum = (int)substr($alt['hora_inicio'], 0, 2);
        if ($horaNum >= 8 && $horaNum <= 12) {
            $razones[] = 'Horario matutino';
        }
        
        return implode(', ', $razones) ?: 'Horario disponible';
    }

    private function categorizarHorario($alt, $diasUtilizados, $horasUtilizadas)
    {
        if (in_array($alt['dia'], $diasUtilizados)) return 'consistente';
        
        foreach ($horasUtilizadas as $horaUsada) {
            if ($alt['hora_inicio'] === $horaUsada['inicio']) return 'mismo_horario';
        }
        
        return 'alternativo';
    }

    private function buscarAulasDisponiblesParaHorario($alt, $horario)
    {
        return Aula::where('estado', 'disponible')
            ->whereNotExists(function($query) use ($alt, $horario) {
                $query->select(\DB::raw(1))
                    ->from('horarios')
                    ->whereColumn('horarios.aula_id', 'aulas.id')
                    ->where('horarios.dia_semana', $alt['dia'])
                    ->where('horarios.periodo_academico', $horario->periodo_academico ?? '2024-2')
                    ->where('horarios.id', '!=', $horario->id)
                    ->where('horarios.hora_inicio', '<', $alt['hora_fin'])
                    ->where('horarios.hora_fin', '>', $alt['hora_inicio']);
            })
            ->orderBy('codigo_aula')
            ->limit(3)
            ->get()
            ->toArray();
    }

    private function calcularPreferenciaHorario($dia, $hora)
    {
        $puntuacion = 50;

        // Preferencia por días (Lunes-Viernes mejor que Sábado)
        if ($dia >= 1 && $dia <= 5) {
            $puntuacion += 20;
        }

        // Preferencia por horarios (mañana y tarde mejor que noche)
        $horaNum = (int)substr($hora, 0, 2);
        if ($horaNum >= 7 && $horaNum <= 12) {
            $puntuacion += 30; // Mañana
        } elseif ($horaNum >= 14 && $horaNum <= 18) {
            $puntuacion += 20; // Tarde
        } else {
            $puntuacion += 5; // Noche
        }

        return $puntuacion;
    }

    /**
     * Obtener horarios relacionados para reasignación masiva
     */
    public function obtenerHorariosRelacionados(Request $request, Horario $horario)
    {
        $tipo = $request->get('tipo', 'materia');
        
        $query = Horario::with(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula'])
                        ->where('id', '!=', $horario->id);

        switch ($tipo) {
            case 'materia':
                $query->whereHas('cargaAcademica.grupo', function($q) use ($horario) {
                    $q->where('materia_id', $horario->cargaAcademica->grupo->materia_id);
                });
                break;
            case 'profesor':
                $query->whereHas('cargaAcademica', function($q) use ($horario) {
                    $q->where('profesor_id', $horario->cargaAcademica->profesor_id);
                });
                break;
            case 'aula':
                $query->where('aula_id', $horario->aula_id);
                break;
            case 'grupo':
                $query->where('carga_academica_id', $horario->carga_academica_id);
                break;
        }

        $horariosRelacionados = $query->orderBy('dia_semana')
                                     ->orderBy('hora_inicio')
                                     ->get();

        return response()->json([
            'success' => true,
            'horarios' => $horariosRelacionados->map(function($h) {
                $dias = ['', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
                return [
                    'id' => $h->id,
                    'materia' => $h->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                    'profesor' => $h->cargaAcademica->profesor->nombre_completo ?? 'N/A',
                    'aula' => $h->aula->codigo_aula ?? 'N/A',
                    'dia' => $dias[$h->dia_semana] ?? 'N/A',
                    'horario' => $h->hora_inicio . '-' . $h->hora_fin,
                    'tipo_clase' => ucfirst($h->tipo_clase)
                ];
            })
        ]);
    }

    /**
     * Método de prueba para verificar que todo funciona
     */
    public function testSugerencias(Horario $horario)
    {
        try {
            // Cargar relaciones
            $horario->load(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula']);
            
            // Verificar datos básicos
            $aulas = Aula::where('estado', 'disponible')->count();
            $profesores = \App\Models\Profesor::where('estado', 'activo')->count();
            
            return response()->json([
                'success' => true,
                'message' => 'Test exitoso - Sistema funcionando correctamente',
                'horario_id' => $horario->id,
                'timestamp' => now(),
                'horario_info' => [
                    'materia' => $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                    'profesor' => $horario->cargaAcademica->profesor->nombre_completo ?? 'N/A',
                    'aula' => $horario->aula->codigo_aula ?? 'N/A',
                    'dia' => $horario->dia_semana,
                    'horario' => $horario->hora_inicio . '-' . $horario->hora_fin
                ],
                'sistema_info' => [
                    'aulas_disponibles' => $aulas,
                    'profesores_activos' => $profesores,
                    'total_horarios' => Horario::count()
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error en test: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Debug de sugerencias paso a paso
     */
    public function debugSugerencias(Horario $horario)
    {
        try {
            $debug = [];
            
            // Paso 1: Cargar horario
            $debug['paso_1'] = 'Cargando horario...';
            $horario->load(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula']);
            $debug['horario_cargado'] = true;
            
            // Paso 2: Obtener horarios de la materia
            $debug['paso_2'] = 'Obteniendo horarios de la materia...';
            $horariosMateria = Horario::where('carga_academica_id', $horario->carga_academica_id)
                ->with(['aula'])
                ->orderBy('dia_semana')
                ->orderBy('hora_inicio')
                ->get();
            $debug['horarios_materia_count'] = $horariosMateria->count();
            
            // Paso 3: Crear contexto
            $debug['paso_3'] = 'Creando contexto...';
            $contextoMateria = [
                'horarios_totales' => $horariosMateria->count(),
                'dias_utilizados' => $horariosMateria->pluck('dia_semana')->unique()->sort()->values(),
                'aulas_utilizadas' => $horariosMateria->pluck('aula_id')->unique()->count(),
                'tipos_clase' => $horariosMateria->pluck('tipo_clase')->unique()->values(),
                'horas_semanales_actuales' => $horariosMateria->sum('duracion_horas'),
                'horas_semanales_requeridas' => $horario->cargaAcademica->grupo->materia->horas_semanales ?? 4
            ];
            $debug['contexto_creado'] = true;
            
            // Paso 4: Probar función de aulas
            $debug['paso_4'] = 'Probando sugerencias de aulas...';
            try {
                $aulasSimples = $this->sugerirAulasInteligentes($horario, $horariosMateria);
                $debug['aulas_sugeridas'] = count($aulasSimples);
                $debug['aulas_exitoso'] = true;
            } catch (\Exception $e) {
                $debug['aulas_error'] = $e->getMessage();
                $debug['aulas_exitoso'] = false;
            }
            
            return response()->json([
                'success' => true,
                'debug' => $debug,
                'contexto' => $contextoMateria
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'debug' => $debug ?? []
            ], 500);
        }
    }

    /**
     * Test de actualización para verificar que no hay errores
     */
    public function testUpdate(Horario $horario)
    {
        try {
            // Simular datos de actualización
            $request = new Request([
                'carga_academica_id' => $horario->carga_academica_id,
                'aula_id' => $horario->aula_id,
                'dia_semana' => $horario->dia_semana,
                'hora_inicio' => $horario->hora_inicio,
                'hora_fin' => $horario->hora_fin,
                'duracion_horas' => $horario->duracion_horas,
                'tipo_clase' => $horario->tipo_clase,
                'periodo_academico' => $horario->periodo_academico,
            ]);

            // Probar validación sin errores
            $validationRules = [
                'carga_academica_id' => 'required|exists:carga_academica,id',
                'aula_id' => 'required|exists:aulas,id',
                'dia_semana' => 'required|integer|min:1|max:7',
                'tipo_clase' => 'required|in:teorica,practica,laboratorio',
            ];

            $validator = \Validator::make($request->all(), $validationRules);

            return response()->json([
                'success' => true,
                'message' => 'Test de actualización exitoso - Sin errores de propiedades',
                'validation_passed' => !$validator->fails(),
                'horario_id' => $horario->id,
                'datos_test' => $request->all()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error en test: ' . $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    /**
     * Debug simple para identificar problemas
     */
    public function debugSimple(Horario $horario)
    {
        try {
            $horario->load(['cargaAcademica.grupo.materia', 'aula']);
            
            $horariosMateria = Horario::where('carga_academica_id', $horario->carga_academica_id)
                ->with(['aula'])
                ->get();
            
            $horariosInfo = $horariosMateria->map(function($h) {
                return [
                    'id' => $h->id,
                    'dia' => $h->dia_semana,
                    'hora_inicio_raw' => $h->getAttributes()['hora_inicio'] ?? 'null',
                    'hora_fin_raw' => $h->getAttributes()['hora_fin'] ?? 'null',
                    'aula' => $h->aula->codigo_aula ?? 'N/A'
                ];
            });
            
            return response()->json([
                'success' => true,
                'horario_id' => $horario->id,
                'horarios_info' => $horariosInfo,
                'debug' => 'Datos básicos obtenidos correctamente'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }

    /**
     * Versión GET de sugerencias para pruebas
     */
    public function sugerenciasGet(Horario $horario)
    {
        try {
            // Cargar relaciones necesarias
            $horario->load(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula']);
            
            // Obtener todos los horarios de la misma materia para contexto
            $horariosMateria = Horario::where('carga_academica_id', $horario->carga_academica_id)
                ->with(['aula'])
                ->orderBy('dia_semana')
                ->orderBy('hora_inicio')
                ->get();
            
            // Información del contexto de la materia
            $contextoMateria = [
                'horarios_totales' => $horariosMateria->count(),
                'dias_utilizados' => $horariosMateria->pluck('dia_semana')->unique()->sort()->values(),
                'aulas_utilizadas' => $horariosMateria->pluck('aula_id')->unique()->count(),
                'tipos_clase' => $horariosMateria->pluck('tipo_clase')->unique()->values(),
                'horas_semanales_actuales' => $horariosMateria->sum('duracion_horas'),
                'horas_semanales_requeridas' => $horario->cargaAcademica->grupo->materia->horas_semanales ?? 4
            ];

            // Obtener información detallada de todos los horarios de la materia
            $horariosCompletos = $horariosMateria->map(function($h) use ($horario) {
                $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
                
                return [
                    'id' => $h->id,
                    'dia' => $h->dia_semana,
                    'dia_nombre' => $dias[$h->dia_semana] ?? 'N/A',
                    'hora_inicio' => $this->formatearHora($h->getAttributes()['hora_inicio'] ?? $h->hora_inicio),
                    'hora_fin' => $this->formatearHora($h->getAttributes()['hora_fin'] ?? $h->hora_fin),
                    'aula_codigo' => $h->aula->codigo_aula ?? 'N/A',
                    'aula_nombre' => $h->aula->nombre ?? 'N/A',
                    'tipo_clase' => ucfirst($h->tipo_clase ?? 'teorica'),
                    'es_actual' => $h->id === $horario->id
                ];
            })->sortBy('dia')->values();

            // Generar sugerencias simples por ahora
            $sugerencias = [
                'aulas' => $this->sugerirAulasSimples($horario),
                'horarios' => $this->sugerirHorariosSimples($horario),
                'distribuciones' => []
            ];

            return response()->json([
                'success' => true,
                'sugerencias' => $sugerencias,
                'contexto_materia' => $contextoMateria,
                'horarios_completos' => $horariosCompletos,
                'horario_actual' => [
                    'id' => $horario->id,
                    'materia' => $horario->cargaAcademica->grupo->materia->nombre ?? 'N/A',
                    'profesor' => $horario->cargaAcademica->profesor->nombre_completo ?? 'N/A',
                    'aula' => $horario->aula->codigo_aula ?? 'N/A',
                    'dia' => $horario->dia_semana,
                    'hora_inicio' => $this->formatearHora($horario->getAttributes()['hora_inicio'] ?? $horario->hora_inicio),
                    'hora_fin' => $this->formatearHora($horario->getAttributes()['hora_fin'] ?? $horario->hora_fin),
                    'tipo_clase' => $horario->tipo_clase ?? 'teorica'
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => basename($e->getFile())
            ], 500);
        }
    }

    /**
     * Sugerencias inteligentes de aulas con información completa
     */
    private function sugerirAulasSimples($horario)
    {
        try {
            // Obtener todos los horarios de la materia para contexto
            $horariosMateria = Horario::where('carga_academica_id', $horario->carga_academica_id)
                ->with(['aula'])
                ->orderBy('dia_semana')
                ->get();
            
            $aulas = Aula::where('estado', 'disponible')
                ->where('id', '!=', $horario->aula_id)
                ->orderBy('codigo_aula')
                ->limit(5)
                ->get();

            return $aulas->map(function($aula) use ($horario, $horariosMateria) {
                // Verificar disponibilidad
                $disponible = !Horario::where('aula_id', $aula->id)
                    ->where('dia_semana', $horario->dia_semana)
                    ->where('periodo_academico', $horario->periodo_academico ?? '2024-2')
                    ->where('id', '!=', $horario->id)
                    ->where('hora_inicio', '<', $horario->hora_fin)
                    ->where('hora_fin', '>', $horario->hora_inicio)
                    ->exists();

                if (!$disponible) {
                    return null; // Filtrar aulas no disponibles
                }

                // Calcular compatibilidad inteligente
                $compatibilidad = $this->calcularCompatibilidadAulaCompleta($aula, $horario, $horariosMateria);
                
                // Generar descripción completa del cambio
                $descripcionCompleta = $this->generarDescripcionCambioAula($aula, $horario, $horariosMateria);
                
                return [
                    'tipo_sugerencia' => 'simple',
                    'aula_id' => $aula->id,
                    'codigo' => $aula->codigo_aula ?? 'N/A',
                    'nombre' => $aula->nombre ?? 'Sin nombre',
                    'capacidad' => $aula->capacidad ?? 0,
                    'tipo' => $aula->tipo_aula ?? 'general',
                    'compatibilidad' => $compatibilidad,
                    'razon' => $this->obtenerRazonCompatibilidadCompleta($aula, $horario, $horariosMateria),
                    'descripcion' => $descripcionCompleta,
                    'impacto' => $this->calcularImpactoCambio($aula, $horario, $horariosMateria),
                    'patron_resultante' => $this->generarPatronResultante($aula, $horario, $horariosMateria)
                ];
            })->filter()->values()->toArray(); // Filtrar nulls y reindexar
        } catch (\Exception $e) {
            \Log::error('Error en sugerirAulasSimples: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Sugerencias inteligentes de horarios con información completa
     */
    private function sugerirHorariosSimples($horario)
    {
        try {
            // Obtener todos los horarios de la materia para contexto
            $horariosMateria = Horario::where('carga_academica_id', $horario->carga_academica_id)
                ->with(['aula'])
                ->orderBy('dia_semana')
                ->get();
            
            $diasUtilizados = $horariosMateria->pluck('dia_semana')->unique()->toArray();
            $horasUtilizadas = $horariosMateria->map(function($h) {
                return [
                    'inicio' => $this->formatearHora($h->hora_inicio),
                    'fin' => $this->formatearHora($h->hora_fin)
                ];
            })->toArray();
            
            // Horarios alternativos inteligentes
            $horariosAlternativos = [
                ['dia' => 1, 'dia_nombre' => 'Lunes', 'hora_inicio' => '08:00', 'hora_fin' => '10:00'],
                ['dia' => 2, 'dia_nombre' => 'Martes', 'hora_inicio' => '08:00', 'hora_fin' => '10:00'],
                ['dia' => 3, 'dia_nombre' => 'Miércoles', 'hora_inicio' => '08:00', 'hora_fin' => '10:00'],
                ['dia' => 4, 'dia_nombre' => 'Jueves', 'hora_inicio' => '08:00', 'hora_fin' => '10:00'],
                ['dia' => 5, 'dia_nombre' => 'Viernes', 'hora_inicio' => '08:00', 'hora_fin' => '10:00'],
                ['dia' => 2, 'dia_nombre' => 'Martes', 'hora_inicio' => '10:00', 'hora_fin' => '12:00'],
                ['dia' => 4, 'dia_nombre' => 'Jueves', 'hora_inicio' => '10:00', 'hora_fin' => '12:00'],
                ['dia' => 3, 'dia_nombre' => 'Miércoles', 'hora_inicio' => '14:00', 'hora_fin' => '16:00'],
                ['dia' => 5, 'dia_nombre' => 'Viernes', 'hora_inicio' => '14:00', 'hora_fin' => '16:00']
            ];

            $sugerencias = [];
            
            foreach ($horariosAlternativos as $alt) {
                // Saltar si es el mismo día y hora actual
                if ($alt['dia'] == $horario->dia_semana && 
                    $alt['hora_inicio'] == $this->formatearHora($horario->hora_inicio)) {
                    continue;
                }
                
                // Verificar disponibilidad del profesor
                $disponible = $this->verificarDisponibilidadProfesor($horario, $alt['dia'], $alt['hora_inicio'], $alt['hora_fin']);
                
                if ($disponible) {
                    // Contar aulas disponibles
                    $aulasDisponibles = $this->contarAulasDisponibles($alt, $horario);
                    
                    if ($aulasDisponibles > 0) {
                        $preferencia = $this->calcularPreferenciaHorarioCompleta($alt, $horario, $diasUtilizados, $horasUtilizadas);
                        $categoria = $this->categorizarHorarioCompleto($alt, $diasUtilizados, $horasUtilizadas);
                        $patronResultante = $this->generarPatronResultanteHorario($alt, $horario, $horariosMateria);
                        
                        $sugerencias[] = [
                            'tipo_sugerencia' => $categoria,
                            'dia' => $alt['dia'],
                            'dia_nombre' => $alt['dia_nombre'],
                            'hora_inicio' => $alt['hora_inicio'],
                            'hora_fin' => $alt['hora_fin'],
                            'preferencia' => $preferencia,
                            'razon_preferencia' => $this->obtenerRazonPreferenciaCompleta($alt, $diasUtilizados, $horasUtilizadas),
                            'aulas_disponibles' => $aulasDisponibles,
                            'categoria' => $categoria,
                            'impacto' => $this->calcularImpactoCambioHorario($alt, $horario, $horariosMateria),
                            'patron_resultante' => $patronResultante
                        ];
                    }
                }
            }

            // Ordenar por preferencia
            usort($sugerencias, function($a, $b) {
                return $b['preferencia'] <=> $a['preferencia'];
            });

            return array_slice($sugerencias, 0, 6); // Top 6 sugerencias
        } catch (\Exception $e) {
            \Log::error('Error en sugerirHorariosSimples: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Contar aulas disponibles para un horario específico
     */
    private function contarAulasDisponibles($alt, $horario)
    {
        return Aula::where('estado', 'disponible')
            ->whereNotExists(function($query) use ($alt, $horario) {
                $query->select(\DB::raw(1))
                    ->from('horarios')
                    ->whereColumn('horarios.aula_id', 'aulas.id')
                    ->where('horarios.dia_semana', $alt['dia'])
                    ->where('horarios.periodo_academico', $horario->periodo_academico ?? '2024-2')
                    ->where('horarios.id', '!=', $horario->id)
                    ->where('horarios.hora_inicio', '<', $alt['hora_fin'])
                    ->where('horarios.hora_fin', '>', $alt['hora_inicio']);
            })
            ->count();
    }

    /**
     * Calcular preferencia de horario considerando el contexto completo
     */
    private function calcularPreferenciaHorarioCompleta($alt, $horario, $diasUtilizados, $horasUtilizadas)
    {
        $puntuacion = 50;

        // Bonus por consistencia de días (días ya utilizados por la materia)
        if (in_array($alt['dia'], $diasUtilizados)) {
            $puntuacion += 25;
        }

        // Bonus por consistencia de horarios
        foreach ($horasUtilizadas as $horaUsada) {
            if ($alt['hora_inicio'] === $horaUsada['inicio']) {
                $puntuacion += 20;
                break;
            }
        }

        // Preferencia por días (Lunes-Viernes)
        if ($alt['dia'] >= 1 && $alt['dia'] <= 5) {
            $puntuacion += 15;
        }

        // Preferencia por horarios
        $horaNum = (int)substr($alt['hora_inicio'], 0, 2);
        if ($horaNum >= 8 && $horaNum <= 12) {
            $puntuacion += 20; // Mañana
        } elseif ($horaNum >= 14 && $horaNum <= 18) {
            $puntuacion += 15; // Tarde
        }

        // Bonus por patrones conocidos (Martes-Jueves, Lunes-Miércoles-Viernes)
        if (in_array($alt['dia'], [2, 4]) && count(array_intersect($diasUtilizados, [2, 4])) > 0) {
            $puntuacion += 10; // Patrón Martes-Jueves
        }
        if (in_array($alt['dia'], [1, 3, 5]) && count(array_intersect($diasUtilizados, [1, 3, 5])) > 0) {
            $puntuacion += 10; // Patrón Lunes-Miércoles-Viernes
        }

        return min(100, $puntuacion);
    }

    /**
     * Categorizar horario según el contexto
     */
    private function categorizarHorarioCompleto($alt, $diasUtilizados, $horasUtilizadas)
    {
        if (in_array($alt['dia'], $diasUtilizados)) {
            return 'mismo_patron';
        }
        
        foreach ($horasUtilizadas as $horaUsada) {
            if ($alt['hora_inicio'] === $horaUsada['inicio']) {
                return 'mismo_horario';
            }
        }
        
        return 'patron_alternativo';
    }

    /**
     * Obtener razón de preferencia completa
     */
    private function obtenerRazonPreferenciaCompleta($alt, $diasUtilizados, $horasUtilizadas)
    {
        $razones = [];
        
        if (in_array($alt['dia'], $diasUtilizados)) {
            $razones[] = 'Día ya utilizado por la materia';
        }
        
        foreach ($horasUtilizadas as $horaUsada) {
            if ($alt['hora_inicio'] === $horaUsada['inicio']) {
                $razones[] = 'Horario consistente con la materia';
                break;
            }
        }
        
        $horaNum = (int)substr($alt['hora_inicio'], 0, 2);
        if ($horaNum >= 8 && $horaNum <= 12) {
            $razones[] = 'Horario matutino preferido';
        } elseif ($horaNum >= 14 && $horaNum <= 18) {
            $razones[] = 'Horario vespertino adecuado';
        }
        
        return implode(', ', $razones) ?: 'Horario disponible';
    }

    /**
     * Calcular impacto del cambio de horario
     */
    private function calcularImpactoCambioHorario($alt, $horario, $horariosMateria)
    {
        $diasUtilizados = $horariosMateria->pluck('dia_semana')->unique()->toArray();
        
        if (in_array($alt['dia'], $diasUtilizados)) {
            return 'Bajo - Mantiene patrón de días de la materia';
        } elseif (count($diasUtilizados) === 1) {
            return 'Medio - Expande a nuevo día';
        } else {
            return 'Alto - Cambia distribución semanal';
        }
    }

    /**
     * Generar patrón resultante después del cambio de horario
     */
    private function generarPatronResultanteHorario($alt, $horario, $horariosMateria)
    {
        $diasCortos = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'];
        
        $patron = [];
        
        foreach ($horariosMateria as $h) {
            if ($h->id === $horario->id) {
                // Este es el horario que estamos cambiando
                $diaTexto = $diasCortos[$alt['dia']] ?? 'N/A';
                $aulaTexto = $h->aula->codigo_aula ?? 'N/A';
                if ($h->aula->tipo_aula === 'laboratorio' || stripos($h->aula->codigo_aula, 'lab') !== false) {
                    $aulaTexto = 'Lab ' . str_replace(['Lab', 'LAB', 'Laboratorio'], '', $aulaTexto);
                }
            } else {
                // Otros horarios mantienen su configuración
                $diaTexto = $diasCortos[$h->dia_semana] ?? 'N/A';
                $aulaTexto = $h->aula->codigo_aula ?? 'N/A';
                if ($h->aula->tipo_aula === 'laboratorio' || stripos($h->aula->codigo_aula, 'lab') !== false) {
                    $aulaTexto = 'Lab ' . str_replace(['Lab', 'LAB', 'Laboratorio'], '', $aulaTexto);
                }
            }
            
            $patron[] = $diaTexto . ' ' . $aulaTexto;
        }
        
        return implode(' - ', $patron);
    }

    /**
     * Sugerir aulas inteligentes considerando patrones de días y tipos de clase
     */
    private function sugerirAulasInteligentes($horario, $horariosMateria)
    {
        try {
            // Analizar el patrón de la materia
            $patronMateria = $this->analizarPatronMateria($horariosMateria);
            
            $sugerencias = [];
            
            // 1. Sugerencias para el día específico (cambio simple de aula)
            $sugerencias = array_merge($sugerencias, $this->sugerirAulasPorDia($horario, $patronMateria));
            
            // 2. Sugerencias de intercambio entre días (si hay múltiples días)
            if ($patronMateria['dias_count'] > 1) {
                $sugerencias = array_merge($sugerencias, $this->sugerirIntercambioAulas($horario, $horariosMateria, $patronMateria));
            }
            
            // 3. Sugerencias de cambio de tipo de clase (si es mixta)
            if ($patronMateria['es_mixta']) {
                $sugerencias = array_merge($sugerencias, $this->sugerirCambioTipoClase($horario, $patronMateria));
            }

            // Ordenar por relevancia y compatibilidad
            usort($sugerencias, function($a, $b) {
                // Priorizar por tipo de sugerencia
                $prioridad = ['intercambio' => 3, 'cambio_tipo' => 2, 'simple' => 1];
                $prioridadA = $prioridad[$a['tipo_sugerencia']] ?? 0;
                $prioridadB = $prioridad[$b['tipo_sugerencia']] ?? 0;
                
                if ($prioridadA !== $prioridadB) {
                    return $prioridadB <=> $prioridadA;
                }
                
                // Luego por compatibilidad
                return $b['compatibilidad'] <=> $a['compatibilidad'];
            });

            return array_slice($sugerencias, 0, 8); // Top 8 sugerencias
        } catch (\Exception $e) {
            \Log::error('Error en sugerirAulasInteligentes: ' . $e->getMessage());
            return [];
        }
    }

    private function generarSugerenciasHorarioInteligentes($horario, $horariosMateria)
    {
        try {
            // Analizar el patrón de la materia
            $patronMateria = $this->analizarPatronMateria($horariosMateria);
            
            $sugerencias = [];
            
            // 1. Sugerencias manteniendo el mismo patrón de días
            $sugerencias = array_merge($sugerencias, $this->sugerirHorariosMismoPatron($horario, $patronMateria));
            
            // 2. Sugerencias cambiando a patrones alternativos
            $sugerencias = array_merge($sugerencias, $this->sugerirPatronesAlternativos($horario, $patronMateria));
            
            // 3. Sugerencias de intercambio entre días existentes
            if ($patronMateria['dias_count'] > 1) {
                $sugerencias = array_merge($sugerencias, $this->sugerirIntercambioDias($horario, $horariosMateria, $patronMateria));
            }

            // Ordenar por relevancia y preferencia
            usort($sugerencias, function($a, $b) {
                // Priorizar por tipo de sugerencia
                $prioridad = ['mismo_patron' => 3, 'intercambio_dias' => 2, 'patron_alternativo' => 1];
                $prioridadA = $prioridad[$a['tipo_sugerencia']] ?? 0;
                $prioridadB = $prioridad[$b['tipo_sugerencia']] ?? 0;
                
                if ($prioridadA !== $prioridadB) {
                    return $prioridadB <=> $prioridadA;
                }
                
                return $b['preferencia'] <=> $a['preferencia'];
            });

            return array_slice($sugerencias, 0, 8); // Top 8 sugerencias
        } catch (\Exception $e) {
            \Log::error('Error en generarSugerenciasHorarioInteligentes: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Sugerir horarios manteniendo el mismo patrón de días
     */
    private function sugerirHorariosMismoPatron($horario, $patronMateria)
    {
        $sugerencias = [];
        $horariosAlternativos = [
            ['08:00', '10:00'], ['10:00', '12:00'], ['14:00', '16:00'], ['16:00', '18:00']
        ];

        foreach ($horariosAlternativos as $franja) {
            if ($franja[0] !== $horario->hora_inicio) {
                // Verificar disponibilidad del profesor en este nuevo horario
                $disponible = $this->verificarDisponibilidadProfesor($horario, $horario->dia_semana, $franja[0], $franja[1]);
                
                if ($disponible) {
                    $aulasSugeridas = $this->buscarAulasDisponiblesParaHorario([
                        'dia' => $horario->dia_semana,
                        'hora_inicio' => $franja[0],
                        'hora_fin' => $franja[1]
                    ], $horario);

                    if (!empty($aulasSugeridas)) {
                        $preferencia = $this->calcularPreferenciaHorario($horario->dia_semana, $franja[0]);
                        
                        $sugerencias[] = [
                            'tipo_sugerencia' => 'mismo_patron',
                            'dia' => $horario->dia_semana,
                            'dia_nombre' => $this->getNombreDia($horario->dia_semana),
                            'hora_inicio' => $franja[0],
                            'hora_fin' => $franja[1],
                            'preferencia' => $preferencia + 20, // Bonus por mantener patrón
                            'razon_preferencia' => 'Mantiene el mismo día, solo cambia horario',
                            'aulas_disponibles' => count($aulasSugeridas),
                            'mejor_aula' => $aulasSugeridas[0] ?? null,
                            'categoria' => 'mismo_patron',
                            'impacto' => 'Mínimo - Solo cambia horario'
                        ];
                    }
                }
            }
        }

        return $sugerencias;
    }

    /**
     * Sugerir patrones alternativos de días
     */
    private function sugerirPatronesAlternativos($horario, $patronMateria)
    {
        $sugerencias = [];
        
        // Patrones alternativos según el patrón actual
        $patronesAlternativos = $this->obtenerPatronesAlternativos($patronMateria);
        
        foreach ($patronesAlternativos as $patron) {
            foreach ($patron['dias'] as $dia) {
                if ($dia !== $horario->dia_semana) {
                    $disponible = $this->verificarDisponibilidadProfesor($horario, $dia, $horario->hora_inicio, $horario->hora_fin);
                    
                    if ($disponible) {
                        $aulasSugeridas = $this->buscarAulasDisponiblesParaHorario([
                            'dia' => $dia,
                            'hora_inicio' => $horario->hora_inicio,
                            'hora_fin' => $horario->hora_fin
                        ], $horario);

                        if (!empty($aulasSugeridas)) {
                            $preferencia = $this->calcularPreferenciaPatronAlternativo($patron, $patronMateria);
                            
                            $sugerencias[] = [
                                'tipo_sugerencia' => 'patron_alternativo',
                                'dia' => $dia,
                                'dia_nombre' => $this->getNombreDia($dia),
                                'hora_inicio' => $horario->hora_inicio,
                                'hora_fin' => $horario->hora_fin,
                                'preferencia' => $preferencia,
                                'razon_preferencia' => $patron['descripcion'],
                                'aulas_disponibles' => count($aulasSugeridas),
                                'mejor_aula' => $aulasSugeridas[0] ?? null,
                                'categoria' => 'patron_alternativo',
                                'impacto' => 'Medio - Cambia patrón de días'
                            ];
                        }
                    }
                }
            }
        }

        return array_slice($sugerencias, 0, 4);
    }

    /**
     * Sugerir intercambio entre días existentes de la materia
     */
    private function sugerirIntercambioDias($horario, $horariosMateria, $patronMateria)
    {
        $sugerencias = [];
        
        foreach ($horariosMateria as $otroHorario) {
            if ($otroHorario->id !== $horario->id) {
                // Verificar si podemos intercambiar los días
                $puedeIntercambiar = $this->verificarIntercambioDias($horario, $otroHorario);
                
                if ($puedeIntercambiar) {
                    $preferencia = $this->calcularPreferenciaIntercambioDias($horario, $otroHorario);
                    
                    $sugerencias[] = [
                        'tipo_sugerencia' => 'intercambio_dias',
                        'dia' => $otroHorario->dia_semana,
                        'dia_nombre' => $this->getNombreDia($otroHorario->dia_semana),
                        'hora_inicio' => $otroHorario->hora_inicio,
                        'hora_fin' => $otroHorario->hora_fin,
                        'preferencia' => $preferencia,
                        'razon_preferencia' => 'Intercambiar con ' . $this->getNombreDia($otroHorario->dia_semana),
                        'aulas_disponibles' => 1,
                        'mejor_aula' => ['codigo_aula' => $otroHorario->aula->codigo_aula ?? 'N/A'],
                        'categoria' => 'intercambio_dias',
                        'impacto' => 'Alto - Intercambia 2 días de la materia',
                        'horario_destino' => $otroHorario->id
                    ];
                }
            }
        }

        return $sugerencias;
    }

    /**
     * Obtener patrones alternativos según el patrón actual
     */
    private function obtenerPatronesAlternativos($patronMateria)
    {
        $patrones = [];
        
        switch ($patronMateria['patron_dias']) {
            case 'alternos_lmv': // Lun, Mié, Vie
                $patrones[] = [
                    'dias' => [2, 4], // Mar, Jue
                    'descripcion' => 'Cambiar a patrón Martes-Jueves'
                ];
                break;
                
            case 'alternos_mj': // Mar, Jue
                $patrones[] = [
                    'dias' => [1, 3, 5], // Lun, Mié, Vie
                    'descripcion' => 'Cambiar a patrón Lunes-Miércoles-Viernes'
                ];
                break;
                
            case 'unico':
                $patrones[] = [
                    'dias' => [2, 4], // Mar, Jue
                    'descripcion' => 'Expandir a Martes-Jueves'
                ];
                $patrones[] = [
                    'dias' => [1, 3], // Lun, Mié
                    'descripcion' => 'Expandir a Lunes-Miércoles'
                ];
                break;
        }
        
        return $patrones;
    }

    /**
     * Funciones auxiliares para horarios
     */
    private function verificarDisponibilidadProfesor($horario, $dia, $horaInicio, $horaFin)
    {
        return !Horario::whereHas('cargaAcademica', function($query) use ($horario) {
            $query->where('profesor_id', $horario->cargaAcademica->profesor_id);
        })
        ->where('dia_semana', $dia)
        ->where('periodo_academico', $horario->periodo_academico ?? '2024-2')
        ->where('id', '!=', $horario->id)
        ->where('hora_inicio', '<', $horaFin)
        ->where('hora_fin', '>', $horaInicio)
        ->exists();
    }

    private function verificarIntercambioDias($horario1, $horario2)
    {
        // Verificar que el profesor esté disponible en los horarios intercambiados
        $disponible1 = $this->verificarDisponibilidadProfesor($horario1, $horario2->dia_semana, $horario2->hora_inicio, $horario2->hora_fin);
        $disponible2 = $this->verificarDisponibilidadProfesor($horario2, $horario1->dia_semana, $horario1->hora_inicio, $horario1->hora_fin);
        
        return $disponible1 && $disponible2;
    }

    private function calcularPreferenciaPatronAlternativo($patron, $patronActual)
    {
        $puntuacion = 50;
        
        // Bonus por patrones conocidos
        if (in_array($patron['dias'], [[2, 4], [1, 3, 5]])) {
            $puntuacion += 20;
        }
        
        // Bonus si reduce días (concentración)
        if (count($patron['dias']) < $patronActual['dias_count']) {
            $puntuacion += 15;
        }
        
        return $puntuacion;
    }

    private function calcularPreferenciaIntercambioDias($horario1, $horario2)
    {
        $puntuacion = 60;
        
        // Bonus si mejora la distribución semanal
        $diferenciaDias = abs($horario1->dia_semana - $horario2->dia_semana);
        if ($diferenciaDias >= 2) {
            $puntuacion += 15; // Mejor distribución
        }
        
        // Bonus si los horarios son similares
        if ($horario1->hora_inicio === $horario2->hora_inicio) {
            $puntuacion += 10;
        }
        
        return $puntuacion;
    }

    /**
     * Generar distribuciones inteligentes completas para la materia
     */
    private function generarDistribucionesInteligentes($horario, $horariosMateria, $contextoMateria)
    {
        try {
            $distribuciones = [];
            $horasRequeridas = $contextoMateria['horas_semanales_requeridas'] ?? 4;
            $horasActuales = $contextoMateria['horas_semanales_actuales'] ?? 0;
            
            // Distribución 1: Optimizar horarios actuales (mantener días, cambiar horarios)
            if ($horariosMateria->count() > 1) {
                $diasUtilizados = $horariosMateria->pluck('dia_semana')->unique()->sort()->values();
                $diasNombres = [];
                foreach ($diasUtilizados as $dia) {
                    $nombres = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie'];
                    $diasNombres[] = $nombres[$dia] ?? 'N/A';
                }
                
                $distribuciones[] = [
                    'tipo' => 'optimizacion',
                    'titulo' => 'Optimizar Distribución Actual',
                    'descripcion' => 'Mantener los mismos días pero optimizar horarios',
                    'dias_count' => $horariosMateria->count(),
                    'dias' => $diasUtilizados->toArray(),
                    'dias_nombres' => $diasNombres,
                    'horas_por_dia' => round($horasRequeridas / $horariosMateria->count(), 1),
                    'franjas_sugeridas' => $this->generarFranjasSugeridas($horariosMateria->count()),
                    'preferencia' => 85,
                    'ventajas' => ['Mantiene estructura actual', 'Horarios optimizados', 'Fácil transición']
                ];
            }
            
            // Distribución 2: Concentrar en menos días
            if ($horariosMateria->count() > 2) {
                $distribuciones[] = [
                    'tipo' => 'concentracion',
                    'titulo' => 'Concentrar en 2 Días',
                    'descripcion' => 'Reducir a 2 días con sesiones más largas',
                    'dias_count' => 2,
                    'dias' => [2, 4],
                    'dias_nombres' => ['Mar', 'Jue'],
                    'horas_por_dia' => round($horasRequeridas / 2, 1),
                    'franjas_sugeridas' => [['08:00', '10:00'], ['14:00', '16:00']],
                    'preferencia' => 75,
                    'ventajas' => ['Menos días de clase', 'Sesiones intensivas', 'Más tiempo libre']
                ];
            }
            
            // Distribución 3: Distribuir en más días (si actualmente son pocos)
            if ($horariosMateria->count() < 3 && $horasRequeridas >= 4) {
                $distribuciones[] = [
                    'tipo' => 'distribucion',
                    'titulo' => 'Distribuir en 3 Días',
                    'descripcion' => 'Repartir en Lunes, Miércoles y Viernes',
                    'dias_count' => 3,
                    'dias' => [1, 3, 5],
                    'dias_nombres' => ['Lun', 'Mié', 'Vie'],
                    'horas_por_dia' => round($horasRequeridas / 3, 1),
                    'franjas_sugeridas' => [['08:00', '09:30'], ['08:00', '09:30'], ['08:00', '09:30']],
                    'preferencia' => 80,
                    'ventajas' => ['Mejor distribución', 'Sesiones más cortas', 'Aprendizaje espaciado']
                ];
            }

            // Ordenar por preferencia
            usort($distribuciones, function($a, $b) {
                return $b['preferencia'] <=> $a['preferencia'];
            });

            return array_slice($distribuciones, 0, 3); // Top 3 distribuciones
        } catch (\Exception $e) {
            \Log::error('Error en generarDistribucionesInteligentes: ' . $e->getMessage());
            return [];
        }
    }

    private function generarFranjasSugeridas($cantidadDias)
    {
        $franjas = [
            ['08:00', '10:00'],
            ['10:00', '12:00'],
            ['14:00', '16:00'],
            ['16:00', '18:00']
        ];
        
        return array_slice($franjas, 0, $cantidadDias);
    }

    private function seleccionarMejoresDias($cantidad)
    {
        // Días preferidos: Martes y Jueves para concentración
        $diasPreferidos = [2, 4, 1, 3, 5]; // Mar, Jue, Lun, Mié, Vie
        return array_slice($diasPreferidos, 0, $cantidad);
    }

    /**
     * Analizar el patrón de una materia (días, tipos de clase, aulas)
     */
    private function analizarPatronMateria($horariosMateria)
    {
        $dias = $horariosMateria->pluck('dia_semana')->unique()->sort()->values()->toArray();
        $tiposClase = $horariosMateria->pluck('tipo_clase')->unique()->values()->toArray();
        $aulas = $horariosMateria->pluck('aula_id')->unique()->values()->toArray();
        
        // Mapear días por tipo de clase
        $diasPorTipo = [];
        foreach ($horariosMateria as $h) {
            $diasPorTipo[$h->tipo_clase][] = [
                'dia' => $h->dia_semana,
                'aula_id' => $h->aula_id,
                'horario_id' => $h->id
            ];
        }
        
        return [
            'dias_count' => count($dias),
            'dias' => $dias,
            'tipos_clase' => $tiposClase,
            'aulas' => $aulas,
            'es_mixta' => count($tiposClase) > 1,
            'es_multi_aula' => count($aulas) > 1,
            'dias_por_tipo' => $diasPorTipo,
            'patron_dias' => $this->identificarPatronDias($dias)
        ];
    }

    /**
     * Identificar el patrón de días (alternos, consecutivos, etc.)
     */
    private function identificarPatronDias($dias)
    {
        if (count($dias) <= 1) return 'unico';
        
        // Verificar si son alternos (1,3,5 o 2,4)
        if ($dias === [1, 3, 5]) return 'alternos_lmv';
        if ($dias === [2, 4]) return 'alternos_mj';
        if ($dias === [1, 3]) return 'alternos_lm';
        if ($dias === [3, 5]) return 'alternos_mv';
        
        // Verificar si son consecutivos
        $consecutivos = true;
        for ($i = 1; $i < count($dias); $i++) {
            if ($dias[$i] - $dias[$i-1] !== 1) {
                $consecutivos = false;
                break;
            }
        }
        
        return $consecutivos ? 'consecutivos' : 'mixto';
    }

    /**
     * Sugerir aulas para el día específico (cambio simple)
     */
    private function sugerirAulasPorDia($horario, $patronMateria)
    {
        $sugerencias = [];
        $aulas = Aula::where('estado', 'disponible')
            ->where('id', '!=', $horario->aula_id)
            ->orderBy('codigo_aula')
            ->get();

        foreach ($aulas as $aula) {
            // Verificar disponibilidad
            $disponible = !Horario::where('aula_id', $aula->id)
                ->where('dia_semana', $horario->dia_semana)
                ->where('periodo_academico', $horario->periodo_academico ?? '2024-2')
                ->where('id', '!=', $horario->id)
                ->where('hora_inicio', '<', $horario->hora_fin)
                ->where('hora_fin', '>', $horario->hora_inicio)
                ->exists();

            if ($disponible) {
                $compatibilidad = $this->calcularCompatibilidadAulaInteligente($aula, $horario, $patronMateria['aulas']);
                
                $sugerencias[] = [
                    'tipo_sugerencia' => 'simple',
                    'aula_id' => $aula->id,
                    'codigo' => $aula->codigo_aula ?? 'N/A',
                    'nombre' => $aula->nombre ?? 'Sin nombre',
                    'capacidad' => $aula->capacidad ?? 0,
                    'tipo' => $aula->tipo_aula ?? 'general',
                    'compatibilidad' => $compatibilidad,
                    'razon' => $this->obtenerRazonCompatibilidadInteligente($aula, $horario, $patronMateria['aulas'], $compatibilidad),
                    'descripcion' => "Cambiar aula del " . $this->getNombreDia($horario->dia_semana) . " a {$aula->codigo_aula}",
                    'impacto' => 'Solo afecta este día',
                    'categoria' => in_array($aula->id, $patronMateria['aulas']) ? 'utilizada' : 'nueva'
                ];
            }
        }

        return array_slice($sugerencias, 0, 4);
    }

    /**
     * Sugerir intercambio de aulas entre días
     */
    private function sugerirIntercambioAulas($horario, $horariosMateria, $patronMateria)
    {
        $sugerencias = [];
        
        // Solo si hay múltiples aulas en uso
        if (!$patronMateria['es_multi_aula']) {
            return $sugerencias;
        }

        foreach ($horariosMateria as $otroHorario) {
            if ($otroHorario->id === $horario->id || $otroHorario->aula_id === $horario->aula_id) {
                continue;
            }

            // Verificar si el intercambio es posible (sin conflictos)
            $puedeIntercambiar = $this->verificarIntercambioAulas($horario, $otroHorario);
            
            if ($puedeIntercambiar) {
                $compatibilidad = $this->calcularCompatibilidadIntercambio($horario, $otroHorario);
                
                $sugerencias[] = [
                    'tipo_sugerencia' => 'intercambio',
                    'aula_id' => $otroHorario->aula_id,
                    'codigo' => $otroHorario->aula->codigo_aula ?? 'N/A',
                    'nombre' => $otroHorario->aula->nombre ?? 'Sin nombre',
                    'capacidad' => $otroHorario->aula->capacidad ?? 0,
                    'tipo' => $otroHorario->aula->tipo_aula ?? 'general',
                    'compatibilidad' => $compatibilidad,
                    'razon' => $this->obtenerRazonIntercambio($horario, $otroHorario),
                    'descripcion' => "Intercambiar aulas: " . $this->getNombreDia($horario->dia_semana) . " ↔ " . $this->getNombreDia($otroHorario->dia_semana),
                    'impacto' => 'Afecta 2 días de la materia',
                    'categoria' => 'intercambio',
                    'horario_destino' => $otroHorario->id
                ];
            }
        }

        return $sugerencias;
    }

    /**
     * Sugerir cambio de tipo de clase (teórica ↔ práctica)
     */
    private function sugerirCambioTipoClase($horario, $patronMateria)
    {
        $sugerencias = [];
        
        // Solo si la materia es mixta (tiene diferentes tipos de clase)
        if (!$patronMateria['es_mixta']) {
            return $sugerencias;
        }

        $tipoActual = $horario->tipo_clase;
        $tiposAlternativos = array_diff($patronMateria['tipos_clase'], [$tipoActual]);

        foreach ($tiposAlternativos as $nuevoTipo) {
            // Buscar aulas apropiadas para el nuevo tipo
            $aulasCompatibles = $this->buscarAulasParaTipo($nuevoTipo, $horario);
            
            foreach ($aulasCompatibles as $aula) {
                $compatibilidad = $this->calcularCompatibilidadCambioTipo($horario, $nuevoTipo, $aula);
                
                $sugerencias[] = [
                    'tipo_sugerencia' => 'cambio_tipo',
                    'aula_id' => $aula->id,
                    'codigo' => $aula->codigo_aula ?? 'N/A',
                    'nombre' => $aula->nombre ?? 'Sin nombre',
                    'capacidad' => $aula->capacidad ?? 0,
                    'tipo' => $aula->tipo_aula ?? 'general',
                    'compatibilidad' => $compatibilidad,
                    'razon' => "Cambiar de {$tipoActual} a {$nuevoTipo}",
                    'descripcion' => "Cambiar a clase {$nuevoTipo} en {$aula->codigo_aula}",
                    'impacto' => 'Cambia tipo de clase y aula',
                    'categoria' => 'cambio_tipo',
                    'nuevo_tipo' => $nuevoTipo
                ];
            }
        }

        return array_slice($sugerencias, 0, 3);
    }

    /**
     * Funciones auxiliares para los nuevos cálculos
     */
    private function verificarIntercambioAulas($horario1, $horario2)
    {
        // Verificar que no haya conflictos si intercambiamos las aulas
        $conflicto1 = Horario::where('aula_id', $horario2->aula_id)
            ->where('dia_semana', $horario1->dia_semana)
            ->where('periodo_academico', $horario1->periodo_academico)
            ->where('id', '!=', $horario1->id)
            ->where('hora_inicio', '<', $horario1->hora_fin)
            ->where('hora_fin', '>', $horario1->hora_inicio)
            ->exists();

        $conflicto2 = Horario::where('aula_id', $horario1->aula_id)
            ->where('dia_semana', $horario2->dia_semana)
            ->where('periodo_academico', $horario2->periodo_academico)
            ->where('id', '!=', $horario2->id)
            ->where('hora_inicio', '<', $horario2->hora_fin)
            ->where('hora_fin', '>', $horario2->hora_inicio)
            ->exists();

        return !$conflicto1 && !$conflicto2;
    }

    private function calcularCompatibilidadIntercambio($horario1, $horario2)
    {
        $puntuacion = 60; // Base para intercambios

        // Bonus si mejora la distribución de tipos
        if ($horario1->tipo_clase !== $horario2->tipo_clase) {
            $puntuacion += 20;
        }

        // Bonus si las aulas son más apropiadas para cada tipo
        if ($this->aulaEsCompatibleConTipo($horario2->aula, $horario1->tipo_clase) &&
            $this->aulaEsCompatibleConTipo($horario1->aula, $horario2->tipo_clase)) {
            $puntuacion += 15;
        }

        return min(100, $puntuacion);
    }

    private function obtenerRazonIntercambio($horario1, $horario2)
    {
        $razones = [];
        
        if ($horario1->tipo_clase !== $horario2->tipo_clase) {
            $razones[] = "Mejor distribución de tipos de clase";
        }
        
        if ($this->aulaEsCompatibleConTipo($horario2->aula, $horario1->tipo_clase)) {
            $razones[] = "Aula más apropiada para " . $horario1->tipo_clase;
        }
        
        return implode(', ', $razones) ?: 'Intercambio de aulas';
    }

    private function buscarAulasParaTipo($tipoClase, $horario)
    {
        $query = Aula::where('estado', 'disponible');
        
        // Filtrar por tipo de aula apropiado
        switch ($tipoClase) {
            case 'laboratorio':
                $query->where('tipo_aula', 'laboratorio');
                break;
            case 'practica':
                $query->whereIn('tipo_aula', ['laboratorio', 'taller', 'practica']);
                break;
            case 'teorica':
                $query->whereIn('tipo_aula', ['aula', 'auditorio', 'general']);
                break;
        }

        return $query->whereNotExists(function($subquery) use ($horario) {
            $subquery->select(\DB::raw(1))
                ->from('horarios')
                ->whereColumn('horarios.aula_id', 'aulas.id')
                ->where('horarios.dia_semana', $horario->dia_semana)
                ->where('horarios.periodo_academico', $horario->periodo_academico ?? '2024-2')
                ->where('horarios.id', '!=', $horario->id)
                ->where('horarios.hora_inicio', '<', $horario->hora_fin)
                ->where('horarios.hora_fin', '>', $horario->hora_inicio);
        })->limit(3)->get();
    }

    private function calcularCompatibilidadCambioTipo($horario, $nuevoTipo, $aula)
    {
        $puntuacion = 50;

        // Compatibilidad perfecta de tipo
        if ($this->aulaEsCompatibleConTipo($aula, $nuevoTipo)) {
            $puntuacion += 40;
        }

        // Bonus por capacidad
        if ($aula->capacidad >= 20 && $aula->capacidad <= 50) {
            $puntuacion += 10;
        }

        return min(100, $puntuacion);
    }

    private function aulaEsCompatibleConTipo($aula, $tipoClase)
    {
        $tipoAula = $aula->tipo_aula ?? 'general';
        
        switch ($tipoClase) {
            case 'laboratorio':
                return $tipoAula === 'laboratorio';
            case 'practica':
                return in_array($tipoAula, ['laboratorio', 'taller', 'practica']);
            case 'teorica':
                return in_array($tipoAula, ['aula', 'auditorio', 'general']);
            default:
                return true;
        }
    }

    private function getNombreDia($dia)
    {
        $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        return $dias[$dia] ?? 'N/A';
    }

    /**
     * CU-12: Validar cambios específicos para modificación componente por componente
     */
    public function validarCambiosCU12(Request $request, Horario $horario)
    {
        try {
            \Log::info('CU-12: Iniciando validación de cambios para horario ID: ' . $horario->id);
            
            $request->validate([
                'carga_academica_id' => 'required|exists:carga_academica,id',
                'aula_id' => 'required|exists:aulas,id',
                'dia_semana' => 'required|integer|min:1|max:7',
                'hora_inicio' => 'required|date_format:H:i',
                'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
                'tipo_clase' => 'required|in:teorica,practica,laboratorio',
                'periodo_academico' => 'required|string|max:20',
            ]);

            // Obtener información del horario actual para comparación
            $horario->load(['cargaAcademica.profesor', 'cargaAcademica.grupo.materia', 'aula']);
            
            // Detectar qué componentes han cambiado
            $cambios = $this->detectarCambiosComponentes($horario, $request);
            
            // Ejecutar CU-11: Validación de Cruces para la nueva tripleta
            $validacion = Horario::validarConflictos(
                $request->carga_academica_id,
                $request->aula_id,
                $request->dia_semana,
                $request->hora_inicio,
                $request->hora_fin,
                $request->periodo_academico,
                $horario->id // Excluir el horario actual
            );

            // Preparar respuesta detallada según CU-12
            $response = [
                'disponible' => $validacion['disponible'],
                'cambios_detectados' => $cambios,
                'validacion_cu11' => $validacion,
                'mensaje_cu12' => '',
                'conflictos_detallados' => [],
                'nueva_tripleta' => [
                    'docente' => $horario->cargaAcademica->profesor->nombre_completo ?? 'N/A',
                    'aula' => $this->obtenerInfoAula($request->aula_id),
                    'franja' => $this->getNombreDia($request->dia_semana) . ' ' . $request->hora_inicio . '-' . $request->hora_fin,
                    'tipo_clase' => ucfirst($request->tipo_clase)
                ]
            ];

            if ($validacion['disponible']) {
                // Sin conflictos - Validación exitosa
                $response['mensaje_cu12'] = 'Validación Exitosa: Sin Conflictos. La nueva asignación está completamente libre.';
                $response['puede_guardar'] = true;
                
                \Log::info('CU-12: Validación exitosa - Sin conflictos detectados');
                
            } else {
                // Conflictos detectados - Preparar mensajes detallados
                $response['puede_guardar'] = false;
                $mensajesConflicto = [];
                
                if ($validacion['conflicto_profesor']) {
                    $mensajesConflicto[] = "Conflicto de Docente: {$validacion['profesor_nombre']} ya tiene clase asignada en este horario";
                    $response['conflictos_detallados']['profesor'] = [
                        'tipo' => 'docente',
                        'mensaje' => "El docente {$validacion['profesor_nombre']} ya tiene una clase asignada",
                        'solucion' => 'Ajuste la hora o cambie el docente'
                    ];
                }

                if ($validacion['conflicto_aula']) {
                    $aulaInfo = $this->obtenerInfoAula($request->aula_id);
                    $mensajesConflicto[] = "Conflicto de Aula: {$aulaInfo['codigo']} ya está ocupada en este horario";
                    $response['conflictos_detallados']['aula'] = [
                        'tipo' => 'aula',
                        'mensaje' => "El aula {$aulaInfo['codigo']} ya está ocupada",
                        'solucion' => 'Ajuste la hora o elija otra aula'
                    ];
                }

                $response['mensaje_cu12'] = 'Conflictos Detectados: ' . implode(' y ', $mensajesConflicto);
                
                \Log::warning('CU-12: Conflictos detectados', [
                    'conflicto_profesor' => $validacion['conflicto_profesor'],
                    'conflicto_aula' => $validacion['conflicto_aula']
                ]);
            }

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('CU-12: Error en validación de cambios: ' . $e->getMessage());
            
            return response()->json([
                'disponible' => false,
                'puede_guardar' => false,
                'mensaje_cu12' => 'Error en validación: ' . $e->getMessage(),
                'error' => true
            ], 500);
        }
    }

    /**
     * Detectar qué componentes han cambiado en el horario
     */
    private function detectarCambiosComponentes(Horario $horario, Request $request)
    {
        $cambios = [];
        
        if ($horario->aula_id != $request->aula_id) {
            $aulaAnterior = $horario->aula->codigo_aula ?? 'N/A';
            $aulaNueva = $this->obtenerInfoAula($request->aula_id)['codigo'];
            $cambios['aula'] = [
                'anterior' => $aulaAnterior,
                'nueva' => $aulaNueva,
                'tipo' => 'aula'
            ];
        }
        
        if ($horario->dia_semana != $request->dia_semana) {
            $cambios['dia'] = [
                'anterior' => $this->getNombreDia($horario->dia_semana),
                'nuevo' => $this->getNombreDia($request->dia_semana),
                'tipo' => 'dia'
            ];
        }
        
        if ($horario->hora_inicio != $request->hora_inicio || $horario->hora_fin != $request->hora_fin) {
            $cambios['horario'] = [
                'anterior' => $horario->hora_inicio . '-' . $horario->hora_fin,
                'nuevo' => $request->hora_inicio . '-' . $request->hora_fin,
                'tipo' => 'horario'
            ];
        }
        
        if ($horario->tipo_clase != $request->tipo_clase) {
            $cambios['tipo_clase'] = [
                'anterior' => ucfirst($horario->tipo_clase),
                'nuevo' => ucfirst($request->tipo_clase),
                'tipo' => 'tipo_clase'
            ];
        }
        
        return $cambios;
    }

    /**
     * Obtener información completa de un aula
     */
    private function obtenerInfoAula($aulaId)
    {
        $aula = \App\Models\Aula::find($aulaId);
        
        if (!$aula) {
            return ['codigo' => 'N/A', 'nombre' => 'N/A', 'tipo' => 'N/A'];
        }
        
        return [
            'codigo' => $aula->codigo_aula ?? 'N/A',
            'nombre' => $aula->nombre ?? 'N/A',
            'tipo' => $aula->tipo_aula ?? 'general',
            'capacidad' => $aula->capacidad ?? 0
        ];
    }

    /**
     * Formatear hora a formato HH:MM
     */
    private function formatearHora($hora)
    {
        if ($hora instanceof \Carbon\Carbon) {
            return $hora->format('H:i');
        }
        
        if (is_string($hora) && strlen($hora) > 5) {
            return substr($hora, 0, 5);
        }
        
        return $hora;
    }

    /**
     * Calcular compatibilidad completa de aula considerando el contexto de la materia
     */
    private function calcularCompatibilidadAulaCompleta($aula, $horario, $horariosMateria)
    {
        $puntuacion = 50; // Base

        // Bonus por consistencia: si ya se usa esta aula para la materia
        $aulasUtilizadas = $horariosMateria->pluck('aula_id')->unique()->toArray();
        if (in_array($aula->id, $aulasUtilizadas)) {
            $puntuacion += 25;
        }

        // Compatibilidad por tipo de clase
        $tipoClase = $horario->tipo_clase;
        $tipoAula = $aula->tipo_aula ?? 'general';

        if (($tipoClase === 'laboratorio' && $tipoAula === 'laboratorio') ||
            ($tipoClase === 'practica' && in_array($tipoAula, ['laboratorio', 'taller'])) ||
            ($tipoClase === 'teorica' && in_array($tipoAula, ['aula', 'auditorio', 'general']))) {
            $puntuacion += 30;
        }

        // Compatibilidad por capacidad
        $capacidadRequerida = 30; // Estimado
        if ($aula->capacidad >= $capacidadRequerida && $aula->capacidad <= $capacidadRequerida * 1.5) {
            $puntuacion += 20;
        } elseif ($aula->capacidad >= $capacidadRequerida) {
            $puntuacion += 10;
        }

        // Bonus por proximidad de código (aulas cercanas)
        $aulaActual = $horario->aula->codigo_aula ?? '';
        $aulaNumero = (int)filter_var($aulaActual, FILTER_SANITIZE_NUMBER_INT);
        $nuevaAulaNumero = (int)filter_var($aula->codigo_aula, FILTER_SANITIZE_NUMBER_INT);
        
        if (abs($aulaNumero - $nuevaAulaNumero) <= 5) {
            $puntuacion += 10; // Aulas cercanas
        }

        return min(100, $puntuacion);
    }

    /**
     * Generar descripción completa del cambio de aula
     */
    private function generarDescripcionCambioAula($aula, $horario, $horariosMateria)
    {
        $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        $diaNombre = $dias[$horario->dia_semana] ?? 'N/A';
        
        $aulaTexto = $aula->codigo_aula;
        if ($aula->tipo_aula === 'laboratorio' || stripos($aula->codigo_aula, 'lab') !== false) {
            $aulaTexto = 'Lab ' . str_replace(['Lab', 'LAB', 'Laboratorio'], '', $aulaTexto);
        }
        
        return "Cambiar {$diaNombre} a {$aulaTexto}";
    }

    /**
     * Obtener razón de compatibilidad completa
     */
    private function obtenerRazonCompatibilidadCompleta($aula, $horario, $horariosMateria)
    {
        $razones = [];
        
        // Verificar si ya se usa en la materia
        $aulasUtilizadas = $horariosMateria->pluck('aula_id')->unique()->toArray();
        if (in_array($aula->id, $aulasUtilizadas)) {
            $razones[] = 'Ya utilizada por la materia';
        }
        
        // Compatibilidad de tipo
        $tipoClase = $horario->tipo_clase;
        $tipoAula = $aula->tipo_aula ?? 'general';
        
        if (($tipoClase === 'laboratorio' && $tipoAula === 'laboratorio') ||
            ($tipoClase === 'practica' && in_array($tipoAula, ['laboratorio', 'taller'])) ||
            ($tipoClase === 'teorica' && in_array($tipoAula, ['aula', 'auditorio', 'general']))) {
            $razones[] = 'Tipo compatible';
        }
        
        // Capacidad adecuada
        if ($aula->capacidad >= 20 && $aula->capacidad <= 50) {
            $razones[] = 'Capacidad adecuada';
        }
        
        return implode(', ', $razones) ?: 'Aula disponible';
    }

    /**
     * Calcular impacto del cambio
     */
    private function calcularImpactoCambio($aula, $horario, $horariosMateria)
    {
        $aulasUtilizadas = $horariosMateria->pluck('aula_id')->unique()->count();
        
        if (in_array($aula->id, $horariosMateria->pluck('aula_id')->toArray())) {
            return 'Mínimo - Aula ya utilizada por la materia';
        } elseif ($aulasUtilizadas === 1) {
            return 'Medio - Introduce nueva aula a la materia';
        } else {
            return 'Bajo - Agrega variedad de aulas';
        }
    }

    /**
     * Generar patrón resultante después del cambio
     */
    private function generarPatronResultante($aula, $horario, $horariosMateria)
    {
        $dias = [1 => 'Lunes', 2 => 'Martes', 3 => 'Miércoles', 4 => 'Jueves', 5 => 'Viernes', 6 => 'Sábado', 7 => 'Domingo'];
        $diasCortos = [1 => 'Lun', 2 => 'Mar', 3 => 'Mié', 4 => 'Jue', 5 => 'Vie', 6 => 'Sáb', 7 => 'Dom'];
        
        $patron = [];
        
        foreach ($horariosMateria as $h) {
            $diaTexto = $diasCortos[$h->dia_semana] ?? 'N/A';
            
            if ($h->id === $horario->id) {
                // Este es el horario que estamos cambiando
                $aulaTexto = $aula->codigo_aula;
                if ($aula->tipo_aula === 'laboratorio' || stripos($aula->codigo_aula, 'lab') !== false) {
                    $aulaTexto = 'Lab ' . str_replace(['Lab', 'LAB', 'Laboratorio'], '', $aulaTexto);
                }
            } else {
                // Otros horarios mantienen su aula
                $aulaTexto = $h->aula->codigo_aula ?? 'N/A';
                if ($h->aula->tipo_aula === 'laboratorio' || stripos($h->aula->codigo_aula, 'lab') !== false) {
                    $aulaTexto = 'Lab ' . str_replace(['Lab', 'LAB', 'Laboratorio'], '', $aulaTexto);
                }
            }
            
            $patron[] = $diaTexto . ' ' . $aulaTexto;
        }
        
        return implode(' - ', $patron);
    }


}