<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Ruta principal - redirige al login
Route::get('/', function () {
    if (session()->has('user_id')) {
        $userType = session('user_type');
        switch ($userType) {
            case 'administrador':
                return redirect()->route('admin.dashboard');
            case 'profesor':
                return redirect()->route('profesor.dashboard');
            case 'estudiante':
                return redirect()->route('estudiante.dashboard');
        }
    }
    return redirect()->route('login');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', function() {
    return redirect()->route('login')->with('error', 'Debe cerrar sesión usando el botón correspondiente.');
});

// Middleware para verificar autenticación
Route::middleware(['web'])->group(function () {
    
    // Rutas de Reportes (Admin y Profesor)
    Route::prefix('reportes')->name('reportes.')->middleware('auth.session:administrador,profesor')->group(function () {
        Route::get('/', [\App\Http\Controllers\ReporteController::class, 'index'])->name('index');
        Route::post('/estatico-pdf', [\App\Http\Controllers\ReporteController::class, 'reporteEstaticoPDF'])->name('estatico-pdf');
        Route::post('/dinamico-excel', [\App\Http\Controllers\ReporteController::class, 'reporteDinamicoExcel'])->name('dinamico-excel');
        Route::post('/carga-horaria', [\App\Http\Controllers\ReporteController::class, 'reporteCargaHoraria'])->name('carga-horaria');
        Route::get('/bitacora', [\App\Http\Controllers\ReporteController::class, 'bitacora'])->name('bitacora');
        Route::post('/bitacora-pdf', [\App\Http\Controllers\ReporteController::class, 'bitacoraPDF'])->name('bitacora-pdf');
        Route::post('/bitacora-excel', [\App\Http\Controllers\ReporteController::class, 'bitacoraExcel'])->name('bitacora-excel');
    });

    // Rutas del Administrador
    Route::prefix('admin')->name('admin.')->middleware('auth.session:administrador')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
        
        // Gestión de Docentes
        Route::resource('docentes', \App\Http\Controllers\Admin\DocenteController::class);
        Route::get('docentes-search', [\App\Http\Controllers\Admin\DocenteController::class, 'search'])->name('docentes.search');
        Route::patch('docentes/{docente}/activate', [\App\Http\Controllers\Admin\DocenteController::class, 'activate'])->name('docentes.activate');
        
        // Gestión de Facultades
        Route::resource('facultades', \App\Http\Controllers\Admin\FacultadController::class)->parameters(['facultades' => 'facultad']);
        
        // Gestión de Carreras
        Route::resource('carreras', \App\Http\Controllers\Admin\CarreraController::class);
        
        // Gestión de Materias
        Route::resource('materias', \App\Http\Controllers\Admin\MateriaController::class);
        
        // Gestión de Grupos
        Route::resource('grupos', \App\Http\Controllers\Admin\GrupoController::class);
        
        // Gestión de Aulas
        Route::resource('aulas', \App\Http\Controllers\Admin\AulaController::class);
        
        // Gestión de Estudiantes
        Route::resource('estudiantes', \App\Http\Controllers\Admin\EstudianteController::class);
        
        // Gestión de Cargas Académicas
        Route::resource('cargas-academicas', \App\Http\Controllers\Admin\CargaAcademicaController::class)->parameters(['cargas-academicas' => 'cargaAcademica']);
        
        // Gestión de Horarios
        Route::get('horarios/boleta', [\App\Http\Controllers\Admin\HorarioController::class, 'boleta'])->name('horarios.boleta');
        Route::resource('horarios', \App\Http\Controllers\Admin\HorarioController::class);
        Route::post('horarios/validar-disponibilidad', [\App\Http\Controllers\Admin\HorarioController::class, 'validarDisponibilidad'])->name('horarios.validar-disponibilidad');
        Route::post('horarios/{horario}/validar-cambios-cu12', [\App\Http\Controllers\Admin\HorarioController::class, 'validarCambiosCU12'])->name('horarios.validar-cambios-cu12');
        Route::post('horarios/{horario}/sugerir-alternativas', [\App\Http\Controllers\Admin\HorarioController::class, 'sugerirAlternativas'])->name('horarios.sugerir-alternativas');
        Route::get('horarios/{horario}/horarios-relacionados', [\App\Http\Controllers\Admin\HorarioController::class, 'obtenerHorariosRelacionados'])->name('horarios.horarios-relacionados');
        Route::get('horarios/{horario}/test-sugerencias', [\App\Http\Controllers\Admin\HorarioController::class, 'testSugerencias'])->name('horarios.test-sugerencias');
        Route::get('horarios/{horario}/sugerencias-get', [\App\Http\Controllers\Admin\HorarioController::class, 'sugerenciasGet'])->name('horarios.sugerencias-get');
        Route::get('horarios/{horario}/debug-simple', [\App\Http\Controllers\Admin\HorarioController::class, 'debugSimple'])->name('horarios.debug-simple');
        Route::get('horarios/logs-validacion', [\App\Http\Controllers\Admin\HorarioController::class, 'obtenerLogsValidacion'])->name('horarios.logs-validacion');
        
        // Sistema de Asistencia Docente (CU-14 a CU-20)
        Route::prefix('asistencia')->name('asistencia.')->group(function () {
            Route::post('entrada', [\App\Http\Controllers\AsistenciaDocenteController::class, 'registrarEntrada'])->name('entrada');
            Route::post('salida', [\App\Http\Controllers\AsistenciaDocenteController::class, 'registrarSalida'])->name('salida');
            Route::post('validar-horario', [\App\Http\Controllers\AsistenciaDocenteController::class, 'validarRegistroDentroDeHorario'])->name('validar-horario');
            Route::post('justificar', [\App\Http\Controllers\AsistenciaDocenteController::class, 'justificarAsistencia'])->name('justificar');
            Route::get('horario-propio', [\App\Http\Controllers\AsistenciaDocenteController::class, 'verHorarioPropio'])->name('horario-propio');
            Route::get('aula/{aula}', [\App\Http\Controllers\AsistenciaDocenteController::class, 'consultarHorarioAula'])->name('aula');
            Route::get('panel-control', [\App\Http\Controllers\AsistenciaDocenteController::class, 'panelControlAsistencia'])->name('panel-control');
        });
        
        // CU-13: Gestión de Días No Laborables/Feriados
        Route::resource('feriados', \App\Http\Controllers\Admin\FeriadoController::class);
        Route::post('feriados/verificar-fecha', [\App\Http\Controllers\Admin\FeriadoController::class, 'verificarFecha'])->name('feriados.verificar-fecha');
        Route::get('feriados/dias-lectivos', [\App\Http\Controllers\Admin\FeriadoController::class, 'diasLectivos'])->name('feriados.dias-lectivos');
        
        // Gestión de Periodos Académicos
        Route::resource('periodos-academicos', \App\Http\Controllers\Admin\PeriodoAcademicoController::class);
        Route::patch('periodos-academicos/{periodoAcademico}/marcar-actual', [\App\Http\Controllers\Admin\PeriodoAcademicoController::class, 'marcarActual'])->name('periodos-academicos.marcar-actual');
        
        // Gestión de Justificaciones
        Route::get('justificaciones', [\App\Http\Controllers\Admin\JustificacionController::class, 'index'])->name('justificaciones.index');
        
        // Panel de Control de Asistencias
        Route::get('panel-asistencia', [\App\Http\Controllers\PanelAsistenciaController::class, 'panelControlDia'])->name('panel-asistencia');
        Route::get('panel-asistencia/api/tiempo-real', [\App\Http\Controllers\PanelAsistenciaController::class, 'apiEstadoTiempoReal'])->name('panel-asistencia.tiempo-real');
        Route::post('panel-asistencia/justificar', [\App\Http\Controllers\PanelAsistenciaController::class, 'justificarDesdePanel'])->name('panel-asistencia.justificar');
    });

    // Rutas del Profesor
    Route::prefix('profesor')->name('profesor.')->middleware('auth.session:profesor')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\ProfesorController::class, 'dashboard'])->name('dashboard');
        Route::get('/dashboard-simple', [\App\Http\Controllers\ProfesorController::class, 'dashboardSimple'])->name('dashboard-simple');
        Route::get('/dashboard-funcional', [\App\Http\Controllers\ProfesorController::class, 'dashboardFuncional'])->name('dashboard-funcional');
        Route::post('/generar-qr', [\App\Http\Controllers\ProfesorController::class, 'generarQR'])->name('generar-qr');
        Route::get('/qr-vista/{token}', [\App\Http\Controllers\ProfesorController::class, 'vistaQR'])->name('qr-vista');
        Route::get('/qr-image/{token}', [\App\Http\Controllers\ProfesorController::class, 'mostrarQR'])->name('qr-image');
        Route::get('/qr/{token}', [\App\Http\Controllers\ProfesorController::class, 'vistaEscanearQR'])->name('escanear-qr');
        Route::post('/qr/{token}/confirmar', [\App\Http\Controllers\ProfesorController::class, 'escanearQR'])->name('confirmar-qr');
        Route::post('/marcar-entrada', [\App\Http\Controllers\ProfesorController::class, 'marcarEntrada'])->name('marcar-entrada');
        Route::post('/marcar-salida', [\App\Http\Controllers\ProfesorController::class, 'marcarSalida'])->name('marcar-salida');
        Route::get('/historial-asistencias', [\App\Http\Controllers\ProfesorController::class, 'historialAsistencias'])->name('historial-asistencias');
        Route::get('/mi-horario', [\App\Http\Controllers\ProfesorController::class, 'miHorario'])->name('mi-horario');
        Route::post('/justificar-asistencia', [\App\Http\Controllers\ProfesorController::class, 'justificarAsistencia'])->name('justificar-asistencia');
    });

    // Rutas del Estudiante
    Route::prefix('estudiante')->name('estudiante.')->middleware('auth.session:estudiante')->group(function () {
        Route::get('/dashboard', [\App\Http\Controllers\EstudianteController::class, 'dashboard'])->name('dashboard');
        
        // Inscripciones
        Route::get('/inscripciones', [\App\Http\Controllers\InscripcionController::class, 'index'])->name('inscripciones.index');
        Route::post('/inscripciones', [\App\Http\Controllers\InscripcionController::class, 'store'])->name('inscripciones.store');
        Route::delete('/inscripciones/{inscripcion}', [\App\Http\Controllers\InscripcionController::class, 'destroy'])->name('inscripciones.destroy');
        
        // Mis Materias
        Route::get('/mis-materias', [\App\Http\Controllers\InscripcionController::class, 'misInscripciones'])->name('mis-materias');
        
        // Asistencias
        Route::get('/asistencia/marcar', [\App\Http\Controllers\AsistenciaEstudianteController::class, 'mostrarClasesHoy'])->name('asistencia.escaner');
        Route::post('/asistencia/marcar', [\App\Http\Controllers\AsistenciaEstudianteController::class, 'marcarAsistencia'])->name('asistencia.marcar');
        Route::get('/asistencia/historial', [\App\Http\Controllers\AsistenciaEstudianteController::class, 'historial'])->name('asistencia.historial');
    });

    // Rutas de Admin para Periodos de Inscripción
    Route::prefix('admin')->name('admin.')->middleware('auth.session:administrador')->group(function () {
        Route::resource('periodos-inscripcion', \App\Http\Controllers\Admin\PeriodoInscripcionController::class);
        Route::post('periodos-inscripcion/{periodo}/activar', [\App\Http\Controllers\Admin\PeriodoInscripcionController::class, 'activar'])->name('periodos-inscripcion.activar');
        Route::post('periodos-inscripcion/{periodo}/desactivar', [\App\Http\Controllers\Admin\PeriodoInscripcionController::class, 'desactivar'])->name('periodos-inscripcion.desactivar');
    });

    // Rutas de Consulta de Aulas (Públicas)
    Route::prefix('consulta')->name('consulta.')->group(function () {
        Route::get('/aulas', [\App\Http\Controllers\AulaConsultaController::class, 'index'])->name('aulas.index');
        Route::get('/aulas/{aula}/horario', [\App\Http\Controllers\AulaConsultaController::class, 'consultarHorario'])->name('aulas.horario');
        Route::get('/aulas/{aula}/api/ocupacion', [\App\Http\Controllers\AulaConsultaController::class, 'apiConsultarOcupacion'])->name('aulas.api-ocupacion');
    });
});