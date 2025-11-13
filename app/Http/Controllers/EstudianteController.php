<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Inscripcion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class EstudianteController extends Controller
{
    public function dashboard()
    {
        $estudianteId = Session::get('user_id');
        $estudiante = Estudiante::findOrFail($estudianteId);

        // Obtener inscripciones activas
        $inscripciones = Inscripcion::with([
            'grupo.materia',
            'grupo.cargaAcademica.profesor',
            'asistencias'
        ])
        ->where('estudiante_id', $estudianteId)
        ->where('estado', 'activo')
        ->get()
        ->map(function($inscripcion) {
            $inscripcion->porcentaje_asistencia = $inscripcion->calcularPorcentajeAsistencia();
            return $inscripcion;
        });

        // Calcular estadísticas
        $totalMaterias = $inscripciones->count();
        $promedioAsistencia = $estudiante->calcularPromedioAsistencia();
        $alertas = $inscripciones->filter(function($i) {
            return $i->tieneAsistenciaBaja();
        })->count();

        // Clases de hoy (simplificado)
        $clasesHoy = 0; // Puedes implementar lógica más compleja aquí

        return view('estudiante.dashboard', compact(
            'inscripciones',
            'totalMaterias',
            'promedioAsistencia',
            'alertas',
            'clasesHoy'
        ));
    }

    public function index()
    {
        $estudiantes = Estudiante::with('carrera')->get();
        return response()->json($estudiantes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:estudiantes',
            'cedula' => 'required|string|max:20|unique:estudiantes',
            'codigo_estudiante' => 'required|string|max:15|unique:estudiantes',
            'fecha_nacimiento' => 'required|date',
            'carrera_id' => 'required|exists:carreras,id',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'semestre_actual' => 'integer|min:1',
            'estado' => 'in:activo,inactivo,graduado,retirado',
        ]);

        $estudiante = Estudiante::create($request->all());
        return response()->json($estudiante->load('carrera'), 201);
    }

    public function show(Estudiante $estudiante)
    {
        return response()->json($estudiante->load(['carrera', 'inscripciones.materia']));
    }

    public function update(Request $request, Estudiante $estudiante)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:estudiantes,email,' . $estudiante->id,
            'cedula' => 'required|string|max:20|unique:estudiantes,cedula,' . $estudiante->id,
            'codigo_estudiante' => 'required|string|max:15|unique:estudiantes,codigo_estudiante,' . $estudiante->id,
            'fecha_nacimiento' => 'required|date',
            'carrera_id' => 'required|exists:carreras,id',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string',
            'semestre_actual' => 'integer|min:1',
            'estado' => 'in:activo,inactivo,graduado,retirado',
        ]);

        $estudiante->update($request->all());
        return response()->json($estudiante->load('carrera'));
    }

    public function destroy(Estudiante $estudiante)
    {
        $estudiante->delete();
        return response()->json(null, 204);
    }
}