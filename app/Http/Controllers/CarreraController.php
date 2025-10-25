<?php

namespace App\Http\Controllers;

use App\Models\Carrera;
use Illuminate\Http\Request;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras = Carrera::with(['facultad', 'materias'])->get();
        return response()->json($carreras);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:10|unique:carreras',
            'duracion_semestres' => 'required|integer|min:1',
            'facultad_id' => 'required|exists:facultades,id',
            'descripcion' => 'nullable|string',
        ]);

        $carrera = Carrera::create($request->all());
        return response()->json($carrera->load('facultad'), 201);
    }

    public function show(Carrera $carrera)
    {
        return response()->json($carrera->load(['facultad', 'materias', 'estudiantes']));
    }

    public function update(Request $request, Carrera $carrera)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:10|unique:carreras,codigo,' . $carrera->id,
            'duracion_semestres' => 'required|integer|min:1',
            'facultad_id' => 'required|exists:facultades,id',
            'descripcion' => 'nullable|string',
        ]);

        $carrera->update($request->all());
        return response()->json($carrera->load('facultad'));
    }

    public function destroy(Carrera $carrera)
    {
        $carrera->delete();
        return response()->json(null, 204);
    }
}