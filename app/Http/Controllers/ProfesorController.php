<?php

namespace App\Http\Controllers;

use App\Models\Profesor;
use Illuminate\Http\Request;

class ProfesorController extends Controller
{
    public function index()
    {
        $profesores = Profesor::all();
        return response()->json($profesores);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:profesores',
            'cedula' => 'required|string|max:20|unique:profesores',
            'tipo_contrato' => 'required|in:tiempo_completo,medio_tiempo,catedra',
            'telefono' => 'nullable|string|max:20',
            'especialidad' => 'nullable|string',
        ]);

        $profesor = Profesor::create($request->all());
        return response()->json($profesor, 201);
    }

    public function show(Profesor $profesor)
    {
        return response()->json($profesor->load('inscripciones.materia'));
    }

    public function update(Request $request, Profesor $profesor)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email' => 'required|email|unique:profesores,email,' . $profesor->id,
            'cedula' => 'required|string|max:20|unique:profesores,cedula,' . $profesor->id,
            'tipo_contrato' => 'required|in:tiempo_completo,medio_tiempo,catedra',
            'telefono' => 'nullable|string|max:20',
            'especialidad' => 'nullable|string',
        ]);

        $profesor->update($request->all());
        return response()->json($profesor);
    }

    public function destroy(Profesor $profesor)
    {
        $profesor->delete();
        return response()->json(null, 204);
    }
}