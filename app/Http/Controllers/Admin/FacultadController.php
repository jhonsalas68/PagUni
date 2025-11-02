<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facultad;
use Illuminate\Http\Request;

class FacultadController extends Controller
{
    public function index()
    {
$facultades = Facultad::with('carreras')->orderBy('codigo')->get();
        return view('admin.facultades.index', compact('facultades'));
    }

    public function create()
    {
return view('admin.facultades.create');
    }

    public function store(Request $request)
    {
$request->validate([
            'codigo' => 'required|string|max:10|unique:facultades,codigo',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        Facultad::create($request->all());

        return redirect()->route('admin.facultades.index')
            ->with('success', 'Facultad registrada exitosamente.');
    }

    public function edit(Facultad $facultad)
    {
return view('admin.facultades.edit', compact('facultad'));
    }

    public function update(Request $request, Facultad $facultad)
    {
$request->validate([
            'codigo' => 'required|string|max:10|unique:facultades,codigo,' . $facultad->id,
            'nombre' => 'required|string|max:255',
            'descripcion' => 'nullable|string',
        ]);

        $facultad->update($request->all());

        return redirect()->route('admin.facultades.index')
            ->with('success', 'Facultad actualizada exitosamente.');
    }

    public function destroy(Facultad $facultad)
    {
$facultad->delete();

        return redirect()->route('admin.facultades.index')
            ->with('success', 'Facultad eliminada exitosamente.');
    }
}