<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Facultad;
use Illuminate\Http\Request;

class FacultadController extends Controller
{
    public function index()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        $facultades = Facultad::with('carreras')->orderBy('codigo')->get();
        return view('admin.facultades.index', compact('facultades'));
    }

    public function create()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        return view('admin.facultades.create');
    }

    public function store(Request $request)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

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
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }
        
        return view('admin.facultades.edit', compact('facultad'));
    }

    public function update(Request $request, Facultad $facultad)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

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
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        $facultad->delete();

        return redirect()->route('admin.facultades.index')
            ->with('success', 'Facultad eliminada exitosamente.');
    }
}