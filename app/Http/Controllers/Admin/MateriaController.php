<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use App\Models\Carrera;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $materias = Materia::with('carrera.facultad')->orderBy('codigo')->get();
        return view('admin.materias.index', compact('materias'));
    }

    public function create()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $carreras = Carrera::with('facultad')->orderBy('nombre')->get();
        return view('admin.materias.create', compact('carreras'));
    }

    public function store(Request $request)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

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
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $materia->load('carrera.facultad');
        return view('admin.materias.show', compact('materia'));
    }

    public function edit(Materia $materia)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $carreras = Carrera::with('facultad')->orderBy('nombre')->get();
        return view('admin.materias.edit', compact('materia', 'carreras'));
    }

    public function update(Request $request, Materia $materia)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

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
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        $materia->delete();

        return redirect()->route('admin.materias.index')
            ->with('success', 'Materia eliminada exitosamente.');
    }
}