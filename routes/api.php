<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FacultadController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\ProfesorController;
use App\Http\Controllers\EstudianteController;
use App\Http\Controllers\InscripcionController;
use App\Http\Controllers\AdministradorController;
use App\Http\Controllers\AulaController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rutas para Facultades
Route::apiResource('facultades', FacultadController::class);

// Rutas para Carreras
Route::apiResource('carreras', CarreraController::class);

// Rutas para Materias
Route::apiResource('materias', MateriaController::class);

// Rutas para Profesores
Route::apiResource('profesores', ProfesorController::class);

// Rutas para Estudiantes
Route::apiResource('estudiantes', EstudianteController::class);

// Rutas para Inscripciones
Route::apiResource('inscripciones', InscripcionController::class);
Route::put('inscripciones/{inscripcion}/calificar', [InscripcionController::class, 'calificar']);

// Rutas para Administradores
Route::apiResource('administradores', AdministradorController::class);

// Rutas para Aulas
Route::apiResource('aulas', AulaController::class);
Route::get('aulas/tipo/{tipo}', [AulaController::class, 'porTipo']);
Route::get('aulas/edificio/{edificio}', [AulaController::class, 'porEdificio']);
Route::get('aulas-disponibles', [AulaController::class, 'disponibles']);
Route::get('laboratorios', [AulaController::class, 'laboratorios']);
Route::get('auditorios', [AulaController::class, 'auditorios']);