<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facultad;
use App\Models\Carrera;
use App\Models\Materia;
use App\Models\Profesor;
use App\Models\Estudiante;
use App\Models\Administrador;
use App\Models\Aula;
use App\Models\Inscripcion;

class DashboardController extends Controller
{
    public function index()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        // Obtener estadísticas reales de la base de datos
        $stats = [
            'facultades' => Facultad::count(),
            'carreras' => Carrera::count(),
            'materias' => Materia::count(),
            'profesores' => Profesor::count(),
            'profesores_activos' => Profesor::where('estado', 'activo')->count(),
            'profesores_inactivos' => Profesor::where('estado', 'inactivo')->count(),
            'estudiantes' => Estudiante::count(),
            'estudiantes_activos' => Estudiante::where('estado', 'activo')->count(),
            'administradores' => Administrador::count(),
            'aulas' => Aula::count(),
            'aulas_disponibles' => Aula::where('estado', 'disponible')->count(),
            'inscripciones' => Inscripcion::count(),
            'inscripciones_activas' => Inscripcion::where('estado', 'inscrito')->count(),
        ];

        // Obtener últimos docentes registrados
        $ultimosDocentes = Profesor::orderBy('created_at', 'desc')->limit(5)->get();

        // Obtener últimos estudiantes registrados
        $ultimosEstudiantes = Estudiante::orderBy('created_at', 'desc')->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'ultimosDocentes', 'ultimosEstudiantes'));
    }
}