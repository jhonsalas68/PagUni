<?php

namespace App\Http\Controllers;

use App\Models\Facultad;
use Illuminate\Http\Request;

class FacultadController extends Controller
{
    public function index()
    {
        $facultades = Facultad::with('carreras')->get();
        return response()->json($facultades);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:10|unique:facultades',
            'descripcion' => 'nullable|string',
        ]);

        $facultad = Facultad::create($request->all());
        return response()->json($facultad, 201);
    }

    public function show(Facultad $facultad)
    {
        return response()->json($facultad->load('carreras'));
    }

    public function update(Request $request, Facultad $facultad)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'codigo' => 'required|string|max:10|unique:facultades,codigo,' . $facultad->id,
            'descripcion' => 'nullable|string',
        ]);

        $facultad->update($request->all());
        return response()->json($facultad);
    }

    public function destroy(Facultad $facultad)
    {
        $facultad->delete();
        return response()->json(null, 204);
    }
}