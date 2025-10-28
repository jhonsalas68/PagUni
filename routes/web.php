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

// Middleware para verificar autenticación
Route::middleware(['web'])->group(function () {
    
    // Rutas del Administrador
    Route::prefix('admin')->name('admin.')->group(function () {
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
        Route::resource('horarios', \App\Http\Controllers\Admin\HorarioController::class);
        Route::post('horarios/validar-disponibilidad', [\App\Http\Controllers\Admin\HorarioController::class, 'validarDisponibilidad'])->name('horarios.validar-disponibilidad');
        Route::post('horarios/{horario}/validar-cambios-cu12', [\App\Http\Controllers\Admin\HorarioController::class, 'validarCambiosCU12'])->name('horarios.validar-cambios-cu12');
        Route::post('horarios/{horario}/sugerir-alternativas', [\App\Http\Controllers\Admin\HorarioController::class, 'sugerirAlternativas'])->name('horarios.sugerir-alternativas');
        Route::get('horarios/{horario}/horarios-relacionados', [\App\Http\Controllers\Admin\HorarioController::class, 'obtenerHorariosRelacionados'])->name('horarios.horarios-relacionados');
        Route::get('horarios/{horario}/test-sugerencias', [\App\Http\Controllers\Admin\HorarioController::class, 'testSugerencias'])->name('horarios.test-sugerencias');
        Route::get('horarios/{horario}/sugerencias-get', [\App\Http\Controllers\Admin\HorarioController::class, 'sugerenciasGet'])->name('horarios.sugerencias-get');
        Route::get('horarios/{horario}/debug-simple', [\App\Http\Controllers\Admin\HorarioController::class, 'debugSimple'])->name('horarios.debug-simple');
        Route::get('horarios/logs-validacion', [\App\Http\Controllers\Admin\HorarioController::class, 'obtenerLogsValidacion'])->name('horarios.logs-validacion');
        
        // CU-13: Gestión de Días No Laborables/Feriados
        Route::resource('feriados', \App\Http\Controllers\Admin\FeriadoController::class);
        Route::post('feriados/verificar-fecha', [\App\Http\Controllers\Admin\FeriadoController::class, 'verificarFecha'])->name('feriados.verificar-fecha');
        Route::get('feriados/dias-lectivos', [\App\Http\Controllers\Admin\FeriadoController::class, 'diasLectivos'])->name('feriados.dias-lectivos');
    });

    // Rutas del Profesor
    Route::prefix('profesor')->name('profesor.')->group(function () {
        Route::get('/dashboard', function () {
            if (session('user_type') !== 'profesor') {
                return redirect()->route('login');
            }
            return view('profesor.dashboard');
        })->name('dashboard');
    });

    // Rutas del Estudiante
    Route::prefix('estudiante')->name('estudiante.')->group(function () {
        Route::get('/dashboard', function () {
            if (session('user_type') !== 'estudiante') {
                return redirect()->route('login');
            }
            return view('estudiante.dashboard');
        })->name('dashboard');
    });
});