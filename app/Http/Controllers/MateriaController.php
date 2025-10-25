<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index()
    {
        $materias = Materia::with('carrera')->get();
        return response()->json($materias);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:10|unique:materias',
            'creditos' => 'required|integer|min:1',
            'semestre' => 'required|integer|min:1',
            'carrera_id' => 'required|exists:carreras,id',
            'descripcion' => 'nullable|string',
        ]);

        $materia = Materia::create($request->all());
        return response()->json($materia->load('carrera'), 201);
    }

    public function show(Materia $materia)
    {
        return response()->json($materia->load(['carrera', 'inscripciones']));
    }

    public function update(Request $request, Materia $materia)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:10|unique:materias,codigo,' . $materia->id,
            'creditos' => 'required|integer|min:1',
            'semestre' => 'required|integer|min:1',
            'carrera_id' => 'required|exists:carreras,id',
            'descripcion' => 'nullable|string',
        ]);

        $materia->update($request->all());
        return response()->json($materia->load('carrera'));
    }

    public function destroy(Materia $materia)
    {
        $materia->delete();
        return response()->json(null, 204);
    }
}