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
        Route::get('/dashboard', function () {
            if (session('user_type') !== 'administrador') {
                return redirect()->route('login');
            }
            return view('admin.dashboard');
        })->name('dashboard');
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