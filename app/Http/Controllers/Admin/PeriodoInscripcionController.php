<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodoInscripcion;
use Illuminate\Http\Request;

class PeriodoInscripcionController extends Controller
{
    public function index()
    {
        $periodos = PeriodoInscripcion::orderBy('fecha_inicio', 'desc')->paginate(10);
        return view('admin.periodos-inscripcion.index', compact('periodos'));
    }

    public function create()
    {
        return view('admin.periodos-inscripcion.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'periodo_academico' => 'required|string|max:10',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'descripcion' => 'nullable|string',
        ]);

        PeriodoInscripcion::create($request->all());

        return redirect()->route('admin.periodos-inscripcion.index')
            ->with('success', 'Periodo de inscripción creado exitosamente.');
    }

    public function edit($id)
    {
        $periodo = PeriodoInscripcion::findOrFail($id);
        return view('admin.periodos-inscripcion.edit', compact('periodo'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'periodo_academico' => 'required|string|max:10',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'descripcion' => 'nullable|string',
        ]);

        $periodo = PeriodoInscripcion::findOrFail($id);
        $periodo->update($request->all());

        return redirect()->route('admin.periodos-inscripcion.index')
            ->with('success', 'Periodo de inscripción actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $periodo = PeriodoInscripcion::findOrFail($id);
        $periodo->delete();

        return redirect()->route('admin.periodos-inscripcion.index')
            ->with('success', 'Periodo de inscripción eliminado exitosamente.');
    }

    public function activar($id)
    {
        $periodo = PeriodoInscripcion::findOrFail($id);
        $periodo->activar();

        return back()->with('success', 'Periodo de inscripción activado exitosamente.');
    }

    public function desactivar($id)
    {
        $periodo = PeriodoInscripcion::findOrFail($id);
        $periodo->desactivar();

        return back()->with('success', 'Periodo de inscripción desactivado exitosamente.');
    }
}
