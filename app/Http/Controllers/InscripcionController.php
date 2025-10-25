<?php

namespace App\Http\Controllers;

use App\Models\Inscripcion;
use Illuminate\Http\Request;

class InscripcionController extends Controller
{
    public function index()
    {
        $inscripciones = Inscripcion::with(['estudiante', 'materia', 'profesor'])->get();
        return response()->json($inscripciones);
    }

    public function store(Request $request)
    {
        $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'materia_id' => 'required|exists:materias,id',
            'profesor_id' => 'required|exists:profesores,id',
            'periodo' => 'required|string|max:10',
            'nota_final' => 'nullable|numeric|min:0|max:5',
            'estado' => 'in:inscrito,aprobado,reprobado,retirado',
        ]);

        $inscripcion = Inscripcion::create($request->all());
        return response()->json($inscripcion->load(['estudiante', 'materia', 'profesor']), 201);
    }

    public function show(Inscripcion $inscripcion)
    {
        return response()->json($inscripcion->load(['estudiante', 'materia', 'profesor']));
    }

    public function update(Request $request, Inscripcion $inscripcion)
    {
        $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,id',
            'materia_id' => 'required|exists:materias,id',
            'profesor_id' => 'required|exists:profesores,id',
            'periodo' => 'required|string|max:10',
            'nota_final' => 'nullable|numeric|min:0|max:5',
            'estado' => 'in:inscrito,aprobado,reprobado,retirado',
        ]);

        $inscripcion->update($request->all());
        return response()->json($inscripcion->load(['estudiante', 'materia', 'profesor']));
    }

    public function destroy(Inscripcion $inscripcion)
    {
        $inscripcion->delete();
        return response()->json(null, 204);
    }

    public function calificar(Request $request, Inscripcion $inscripcion)
    {
        $request->validate([
            'nota_final' => 'required|numeric|min:0|max:5',
        ]);

        $estado = $request->nota_final >= 3 ? 'aprobado' : 'reprobado';
        
        $inscripcion->update([
            'nota_final' => $request->nota_final,
            'estado' => $estado,
        ]);

        return response()->json($inscripcion->load(['estudiante', 'materia', 'profesor']));
    }
}