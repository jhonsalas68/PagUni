<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Grupo;
use App\Models\Materia;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function index()
    {
$grupos = Grupo::with('materia.carrera')->orderBy('identificador')->get();
        return view('admin.grupos.index', compact('grupos'));
    }

    public function create(Request $request)
    {
        $materias = Materia::with('carrera')->orderBy('nombre')->get();
        $materiaSeleccionada = $request->input('materia_id');
        
        return view('admin.grupos.create', compact('materias', 'materiaSeleccionada'));
    }

    public function store(Request $request)
    {
$request->validate([
            'identificador' => 'required|string|max:10',
            'materia_id' => 'required|exists:materias,id',
            'capacidad_maxima' => 'required|integer|min:1',
            'estado' => 'required|in:activo,inactivo',
        ]);

        // Verificar que no exista ya un grupo con el mismo identificador para la misma materia
        $existeGrupo = Grupo::where('identificador', $request->identificador)
                           ->where('materia_id', $request->materia_id)
                           ->exists();

        if ($existeGrupo) {
            return back()->withErrors(['error' => 'Ya existe un grupo con este identificador para esta materia.'])
                        ->withInput();
        }

        Grupo::create($request->all());

        return redirect()->route('admin.grupos.index')
            ->with('success', 'Grupo registrado exitosamente.');
    }

    public function show(Grupo $grupo)
    {
$grupo->load(['materia.carrera', 'cargaAcademica.profesor']);
        return view('admin.grupos.show', compact('grupo'));
    }

    public function edit(Grupo $grupo)
    {
$materias = Materia::with('carrera')->orderBy('nombre')->get();
        return view('admin.grupos.edit', compact('grupo', 'materias'));
    }

    public function update(Request $request, Grupo $grupo)
    {
$request->validate([
            'identificador' => 'required|string|max:10',
            'materia_id' => 'required|exists:materias,id',
            'capacidad_maxima' => 'required|integer|min:1',
            'estado' => 'required|in:activo,inactivo',
        ]);

        // Verificar que no exista ya un grupo con el mismo identificador para la misma materia (excluyendo el actual)
        $existeGrupo = Grupo::where('identificador', $request->identificador)
                           ->where('materia_id', $request->materia_id)
                           ->where('id', '!=', $grupo->id)
                           ->exists();

        if ($existeGrupo) {
            return back()->withErrors(['error' => 'Ya existe un grupo con este identificador para esta materia.'])
                        ->withInput();
        }

        $grupo->update($request->all());

        return redirect()->route('admin.grupos.index')
            ->with('success', 'Grupo actualizado exitosamente.');
    }

    public function destroy(Grupo $grupo)
    {
$grupo->delete();

        return redirect()->route('admin.grupos.index')
            ->with('success', 'Grupo eliminado exitosamente.');
    }
}
