<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use Illuminate\Http\Request;

class AulaController extends Controller
{
    public function index(Request $request)
    {
        $query = Aula::query();

        // Filtro por búsqueda (código o nombre)
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('codigo_aula', 'like', "%{$buscar}%")
                  ->orWhere('nombre', 'like', "%{$buscar}%")
                  ->orWhere('edificio', 'like', "%{$buscar}%");
            });
        }

        // Filtro por tipo de aula
        if ($request->filled('tipo_aula')) {
            $query->where('tipo_aula', $request->tipo_aula);
        }

        // Filtro por estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $aulas = $query->orderBy('codigo_aula')->paginate(10)->withQueryString();
        
        return view('admin.aulas.index', compact('aulas'));
    }

    public function create()
    {
return view('admin.aulas.create');
    }

    public function store(Request $request)
    {
$request->validate([
            'codigo_aula' => 'required|string|max:20|unique:aulas,codigo_aula',
            'nombre' => 'required|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'tipo_aula' => 'required|in:aula,laboratorio,auditorio,sala_conferencias,biblioteca',
            'edificio' => 'required|string|max:255',
            'piso' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
            'equipamiento' => 'nullable|array',
            'estado' => 'required|in:disponible,ocupada,mantenimiento,fuera_servicio',
            'tiene_aire_acondicionado' => 'boolean',
            'tiene_proyector' => 'boolean',
            'tiene_computadoras' => 'boolean',
            'acceso_discapacitados' => 'boolean',
        ]);

        $data = $request->all();
        $data['tiene_aire_acondicionado'] = $request->has('tiene_aire_acondicionado');
        $data['tiene_proyector'] = $request->has('tiene_proyector');
        $data['tiene_computadoras'] = $request->has('tiene_computadoras');
        $data['acceso_discapacitados'] = $request->has('acceso_discapacitados');

        Aula::create($data);

        return redirect()->route('admin.aulas.index')
            ->with('success', 'Aula registrada exitosamente.');
    }

    public function show(Aula $aula)
    {
return view('admin.aulas.show', compact('aula'));
    }

    public function edit(Aula $aula)
    {
return view('admin.aulas.edit', compact('aula'));
    }

    public function update(Request $request, Aula $aula)
    {
$request->validate([
            'codigo_aula' => 'required|string|max:20|unique:aulas,codigo_aula,' . $aula->id,
            'nombre' => 'required|string|max:255',
            'capacidad' => 'required|integer|min:1',
            'tipo_aula' => 'required|in:aula,laboratorio,auditorio,sala_conferencias,biblioteca',
            'edificio' => 'required|string|max:255',
            'piso' => 'required|integer|min:1',
            'descripcion' => 'nullable|string',
            'equipamiento' => 'nullable|array',
            'estado' => 'required|in:disponible,ocupada,mantenimiento,fuera_servicio',
            'tiene_aire_acondicionado' => 'boolean',
            'tiene_proyector' => 'boolean',
            'tiene_computadoras' => 'boolean',
            'acceso_discapacitados' => 'boolean',
        ]);

        $data = $request->all();
        $data['tiene_aire_acondicionado'] = $request->has('tiene_aire_acondicionado');
        $data['tiene_proyector'] = $request->has('tiene_proyector');
        $data['tiene_computadoras'] = $request->has('tiene_computadoras');
        $data['acceso_discapacitados'] = $request->has('acceso_discapacitados');

        $aula->update($data);

        return redirect()->route('admin.aulas.index')
            ->with('success', 'Aula actualizada exitosamente.');
    }

    public function destroy(Aula $aula)
    {
$aula->delete();

        return redirect()->route('admin.aulas.index')
            ->with('success', 'Aula eliminada exitosamente.');
    }
}