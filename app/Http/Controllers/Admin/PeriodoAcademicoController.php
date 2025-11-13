<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeriodoAcademico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodoAcademicoController extends Controller
{
    public function index(Request $request)
    {
        $query = PeriodoAcademico::query();

        // Filtros
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->filled('anio')) {
            $query->where('anio', $request->anio);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('codigo', 'like', "%{$buscar}%")
                  ->orWhere('nombre', 'like', "%{$buscar}%");
            });
        }

        $periodos = $query->orderBy('anio', 'desc')
                          ->orderBy('semestre', 'desc')
                          ->paginate(15);

        $anios = PeriodoAcademico::select('anio')
                                 ->distinct()
                                 ->orderBy('anio', 'desc')
                                 ->pluck('anio');

        return view('admin.periodos-academicos.index', compact('periodos', 'anios'));
    }

    public function create()
    {
        return view('admin.periodos-academicos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'anio' => 'required|integer|min:2020|max:2050',
            'semestre' => 'required|integer|in:1,2',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'observaciones' => 'nullable|string|max:500'
        ], [
            'anio.required' => 'El año es obligatorio',
            'anio.min' => 'El año debe ser mayor a 2020',
            'anio.max' => 'El año debe ser menor a 2050',
            'semestre.required' => 'El semestre es obligatorio',
            'semestre.in' => 'El semestre debe ser 1 o 2',
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_fin.required' => 'La fecha de fin es obligatoria',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio'
        ]);

        // Generar código automático
        $codigo = $validated['anio'] . '-' . $validated['semestre'];
        
        // Verificar si ya existe
        if (PeriodoAcademico::where('codigo', $codigo)->exists()) {
            return back()->withErrors(['error' => 'Ya existe un periodo académico con el código ' . $codigo])
                        ->withInput();
        }

        // Generar nombre automático
        $nombreSemestre = $validated['semestre'] == 1 ? 'Primer' : 'Segundo';
        $nombre = $nombreSemestre . ' Semestre ' . $validated['anio'];

        $periodo = PeriodoAcademico::create([
            'codigo' => $codigo,
            'nombre' => $nombre,
            'anio' => $validated['anio'],
            'semestre' => $validated['semestre'],
            'fecha_inicio' => $validated['fecha_inicio'],
            'fecha_fin' => $validated['fecha_fin'],
            'estado' => 'activo',
            'es_actual' => false,
            'observaciones' => $validated['observaciones']
        ]);

        return redirect()->route('admin.periodos-academicos.index')
                        ->with('success', 'Periodo académico creado exitosamente: ' . $codigo);
    }

    public function edit(PeriodoAcademico $periodoAcademico)
    {
        return view('admin.periodos-academicos.edit', compact('periodoAcademico'));
    }

    public function update(Request $request, PeriodoAcademico $periodoAcademico)
    {
        $validated = $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'estado' => 'required|in:activo,inactivo,finalizado',
            'observaciones' => 'nullable|string|max:500'
        ], [
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria',
            'fecha_fin.required' => 'La fecha de fin es obligatoria',
            'fecha_fin.after' => 'La fecha de fin debe ser posterior a la fecha de inicio',
            'estado.required' => 'El estado es obligatorio'
        ]);

        $periodoAcademico->update($validated);

        return redirect()->route('admin.periodos-academicos.index')
                        ->with('success', 'Periodo académico actualizado exitosamente');
    }

    public function destroy(PeriodoAcademico $periodoAcademico)
    {
        // Verificar si tiene horarios asociados
        $horariosCount = DB::table('horarios')
                          ->where('periodo', $periodoAcademico->codigo)
                          ->count();

        if ($horariosCount > 0) {
            return back()->withErrors(['error' => 'No se puede eliminar el periodo porque tiene ' . $horariosCount . ' horarios asociados']);
        }

        $codigo = $periodoAcademico->codigo;
        $periodoAcademico->delete();

        return redirect()->route('admin.periodos-academicos.index')
                        ->with('success', 'Periodo académico ' . $codigo . ' eliminado exitosamente');
    }

    public function marcarActual(PeriodoAcademico $periodoAcademico)
    {
        PeriodoAcademico::marcarComoActual($periodoAcademico->id);

        return back()->with('success', 'Periodo ' . $periodoAcademico->codigo . ' marcado como actual');
    }
}
