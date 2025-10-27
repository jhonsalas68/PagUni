<?php

namespace App\Http\Controllers;

use App\Models\Aula;
use Illuminate\Http\Request;

class AulaController extends Controller
{
    public function index()
    {
        $aulas = Aula::orderBy('edificio')->orderBy('piso')->orderBy('codigo_aula')->get();
        return response()->json($aulas);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigo_aula' => 'required|string|max:20|unique:aulas',
            'nombre' => 'required|string|max:255',
            'tipo_aula' => 'required|in:aula,laboratorio,auditorio,sala_conferencias,biblioteca',
            'edificio' => 'required|string|max:255',
            'piso' => 'required|integer|min:0',
            'capacidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
            'equipamiento' => 'nullable|array',
            'estado' => 'required|in:disponible,ocupada,mantenimiento,fuera_servicio',
            'tiene_aire_acondicionado' => 'boolean',
            'tiene_proyector' => 'boolean',
            'tiene_computadoras' => 'boolean',
            'acceso_discapacitados' => 'boolean',
        ]);

        $aula = Aula::create($request->all());
        return response()->json($aula, 201);
    }

    public function show(Aula $aula)
    {
        return response()->json($aula);
    }

    public function update(Request $request, Aula $aula)
    {
        $request->validate([
            'codigo_aula' => 'required|string|max:20|unique:aulas,codigo_aula,' . $aula->id,
            'nombre' => 'required|string|max:255',
            'tipo_aula' => 'required|in:aula,laboratorio,auditorio,sala_conferencias,biblioteca',
            'edificio' => 'required|string|max:255',
            'piso' => 'required|integer|min:0',
            'capacidad' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
            'equipamiento' => 'nullable|array',
            'estado' => 'required|in:disponible,ocupada,mantenimiento,fuera_servicio',
            'tiene_aire_acondicionado' => 'boolean',
            'tiene_proyector' => 'boolean',
            'tiene_computadoras' => 'boolean',
            'acceso_discapacitados' => 'boolean',
        ]);

        $aula->update($request->all());
        return response()->json($aula);
    }

    public function destroy(Aula $aula)
    {
        $aula->delete();
        return response()->json(null, 204);
    }

    // Métodos adicionales para filtros específicos
    public function porTipo($tipo)
    {
        $aulas = Aula::tipo($tipo)->get();
        return response()->json($aulas);
    }

    public function porEdificio($edificio)
    {
        $aulas = Aula::edificio($edificio)->get();
        return response()->json($aulas);
    }

    public function disponibles()
    {
        $aulas = Aula::disponibles()->get();
        return response()->json($aulas);
    }

    public function laboratorios()
    {
        $aulas = Aula::tipo('laboratorio')->get();
        return response()->json($aulas);
    }

    public function auditorios()
    {
        $aulas = Aula::tipo('auditorio')->get();
        return response()->json($aulas);
    }
}