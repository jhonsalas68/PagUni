<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Facultad;
use Illuminate\Http\Request;

class CarreraController extends Controller
{
    public function index()
    {
$carreras = Carrera::with('facultad')->orderBy('codigo')->get();
        return view('admin.carreras.index', compact('carreras'));
    }

    public function create()
    {
$facultades = Facultad::orderBy('nombre')->get();
        return view('admin.carreras.create', compact('facultades'));
    }

    public function store(Request $request)
    {
$request->validate([
            'codigo' => 'required|string|max:10|unique:carreras,codigo',
            'nombre' => 'required|string|max:255',
            'facultad_id' => 'required|exists:facultades,id',
            'duracion_semestres' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
        ]);

        Carrera::create($request->all());

        return redirect()->route('admin.carreras.index')
            ->with('success', 'Carrera registrada exitosamente.');
    }

    public function show(Carrera $carrera)
    {
$carrera->load('facultad', 'materias');
        return view('admin.carreras.show', compact('carrera'));
    }

    public function edit(Carrera $carrera)
    {
$facultades = Facultad::orderBy('nombre')->get();
        return view('admin.carreras.edit', compact('carrera', 'facultades'));
    }

    public function update(Request $request, Carrera $carrera)
    {
$request->validate([
            'codigo' => 'required|string|max:10|unique:carreras,codigo,' . $carrera->id,
            'nombre' => 'required|string|max:255',
            'facultad_id' => 'required|exists:facultades,id',
            'duracion_semestres' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
        ]);

        $carrera->update($request->all());

        return redirect()->route('admin.carreras.index')
            ->with('success', 'Carrera actualizada exitosamente.');
    }

    public function destroy(Carrera $carrera)
    {
$carrera->delete();

        return redirect()->route('admin.carreras.index')
            ->with('success', 'Carrera eliminada exitosamente.');
    }
}