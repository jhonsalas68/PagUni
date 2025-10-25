<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
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