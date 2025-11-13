<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use App\Models\Carrera;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index(Request $request)
    {
        $query = Materia::with('carrera.facultad');

        // Filtro por búsqueda (nombre, código o sigla)
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                  ->orWhere('codigo', 'like', "%{$buscar}%");
            });
        }

        // Filtro por semestre
        if ($request->filled('semestre')) {
            $query->where('semestre', $request->semestre);
        }

        $materias = $query->orderBy('codigo')->paginate(10)->withQueryString();
        
        return view('admin.materias.index', compact('materias'));
    }

    public function create()
    {
$carreras = Carrera::with('facultad')->orderBy('nombre')->get();
        return view('admin.materias.create', compact('carreras'));
    }

    public function store(Request $request)
    {
$request->validate([
            'codigo' => 'required|string|max:10|unique:materias,codigo',
            'nombre' => 'required|string|max:255',
            'carrera_id' => 'required|exists:carreras,id',
            'semestre' => 'required|integer|min:1|max:12',
            'creditos' => 'required|integer|min:1',
            'horas_teoricas' => 'required|integer|min:0',
            'horas_practicas' => 'required|integer|min:0',
            'descripcion' => 'nullable|string',
        ]);

        Materia::create($request->all());

        return redirect()->route('admin.materias.index')
            ->with('success', 'Materia registrada exitosamente.');
    }

    public function show(Materia $materia)
    {
$materia->load('carrera.facultad');
        return view('admin.materias.show', compact('materia'));
    }

    public function edit(Materia $materia)
    {
$carreras = Carrera::with('facultad')->orderBy('nombre')->get();
        return view('admin.materias.edit', compact('materia', 'carreras'));
    }

    public function update(Request $request, Materia $materia)
    {
$request->validate([
            'codigo' => 'required|string|max:10|unique:materias,codigo,' . $materia->id,
            'nombre' => 'required|string|max:255',
            'carrera_id' => 'required|exists:carreras,id',
            'semestre' => 'required|integer|min:1|max:12',
            'creditos' => 'required|integer|min:1',
            'horas_teoricas' => 'required|integer|min:0',
            'horas_practicas' => 'required|integer|min:0',
            'descripcion' => 'nullable|string',
        ]);

        $materia->update($request->all());

        return redirect()->route('admin.materias.index')
            ->with('success', 'Materia actualizada exitosamente.');
    }

    public function destroy(Materia $materia)
    {
$materia->delete();

        return redirect()->route('admin.materias.index')
            ->with('success', 'Materia eliminada exitosamente.');
    }
}