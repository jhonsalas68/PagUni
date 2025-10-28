<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Feriado;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FeriadoController extends Controller
{
    /**
     * CU-13: Mostrar lista de días no laborables/feriados
     */
    public function index(Request $request)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        $query = Feriado::query();

        // Filtros de búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function($q) use ($buscar) {
                $q->where('descripcion', 'ILIKE', "%{$buscar}%")
                  ->orWhere('tipo', 'ILIKE', "%{$buscar}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('año')) {
            $año = $request->año;
            $query->whereYear('fecha_inicio', $año);
        }

        // Ordenar por fecha
        $feriados = $query->orderBy('fecha_inicio', 'desc')
                         ->paginate(15)
                         ->withQueryString();

        // Estadísticas
        $estadisticas = [
            'total' => Feriado::where('activo', true)->count(),
            'este_año' => Feriado::where('activo', true)
                                ->whereYear('fecha_inicio', date('Y'))
                                ->count(),
            'proximos' => Feriado::where('activo', true)
                                ->where('fecha_inicio', '>=', now())
                                ->count(),
            'por_tipo' => Feriado::where('activo', true)
                                ->selectRaw('tipo, COUNT(*) as total')
                                ->groupBy('tipo')
                                ->pluck('total', 'tipo')
                                ->toArray()
        ];

        return view('admin.feriados.index', compact('feriados', 'estadisticas'));
    }

    /**
     * CU-13: Mostrar formulario para crear nuevo feriado
     */
    public function create()
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        return view('admin.feriados.create');
    }

    /**
     * CU-13: Almacenar nuevo feriado con validación de superposición
     */
    public function store(Request $request)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        $validationRules = [
            'fecha_inicio' => 'required|date|after_or_equal:today',
            'descripcion' => 'required|string|max:255',
            'tipo' => 'required|in:feriado,receso,asueto',
            'fecha_fin' => 'nullable|date|after:fecha_inicio'
        ];

        $request->validate($validationRules);

        // Validar superposición
        $validacion = Feriado::checkOverlap($request->fecha_inicio, $request->fecha_fin);

        if ($validacion['tiene_conflicto']) {
            return back()
                ->withErrors(['error' => 'Error: La fecha se superpone con un feriado ya registrado. ' . $validacion['mensaje']])
                ->withInput();
        }

        try {
            Feriado::create([
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
                'activo' => true
            ]);

            return redirect()->route('admin.feriados.index')
                ->with('success', 'Gestión de Feriados Exitosa.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error: Los datos son incorrectos. ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * CU-13: Mostrar detalles de un feriado específico
     */
    public function show(Feriado $feriado)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        // Calcular días afectados
        $diasAfectados = [];
        if ($feriado->fecha_fin) {
            $inicio = Carbon::parse($feriado->fecha_inicio);
            $fin = Carbon::parse($feriado->fecha_fin);
            
            while ($inicio->lte($fin)) {
                $diasAfectados[] = [
                    'fecha' => $inicio->copy(),
                    'dia_semana' => $inicio->locale('es')->dayName,
                    'es_laborable' => $inicio->isWeekday()
                ];
                $inicio->addDay();
            }
        } else {
            $fecha = Carbon::parse($feriado->fecha_inicio);
            $diasAfectados[] = [
                'fecha' => $fecha,
                'dia_semana' => $fecha->locale('es')->dayName,
                'es_laborable' => $fecha->isWeekday()
            ];
        }

        return view('admin.feriados.show', compact('feriado', 'diasAfectados'));
    }

    /**
     * CU-13: Mostrar formulario para editar feriado
     */
    public function edit(Feriado $feriado)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        return view('admin.feriados.edit', compact('feriado'));
    }

    /**
     * CU-13: Actualizar feriado con validación de superposición
     */
    public function update(Request $request, Feriado $feriado)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        $validationRules = [
            'fecha_inicio' => 'required|date',
            'descripcion' => 'required|string|max:255',
            'tipo' => 'required|in:feriado,receso,asueto',
            'fecha_fin' => 'nullable|date|after:fecha_inicio',
            'activo' => 'boolean'
        ];

        $request->validate($validationRules);

        // Validar superposición (excluyendo el feriado actual)
        $validacion = Feriado::checkOverlap($request->fecha_inicio, $request->fecha_fin, $feriado->id);

        if ($validacion['tiene_conflicto']) {
            return back()
                ->withErrors(['error' => 'Error: La fecha se superpone con un feriado ya registrado. ' . $validacion['mensaje']])
                ->withInput();
        }

        try {
            $feriado->update([
                'fecha_inicio' => $request->fecha_inicio,
                'fecha_fin' => $request->fecha_fin,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
                'activo' => $request->has('activo')
            ]);

            return redirect()->route('admin.feriados.index')
                ->with('success', 'Gestión de Feriados Exitosa.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error: Los datos son incorrectos. ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * CU-13: Eliminar feriado (desactivar)
     */
    public function destroy(Feriado $feriado)
    {
        if (session('user_type') !== 'administrador') {
            return redirect()->route('login')->with('error', 'Acceso denegado');
        }

        try {
            // En lugar de eliminar, desactivamos para mantener historial
            $feriado->update(['activo' => false]);

            return redirect()->route('admin.feriados.index')
                ->with('success', 'Gestión de Feriados Exitosa. Feriado desactivado correctamente.');

        } catch (\Exception $e) {
            return back()
                ->withErrors(['error' => 'Error: No se pudo eliminar el feriado. ' . $e->getMessage()]);
        }
    }

    /**
     * API: Verificar si una fecha es feriado
     */
    public function verificarFecha(Request $request)
    {
        $request->validate([
            'fecha' => 'required|date'
        ]);

        $esFeriado = Feriado::esFeriado($request->fecha);
        $feriados = [];

        if ($esFeriado) {
            $feriados = Feriado::getActiveFeriados($request->fecha, $request->fecha);
        }

        return response()->json([
            'es_feriado' => $esFeriado,
            'feriados' => $feriados->map(function($f) {
                return [
                    'id' => $f->id,
                    'descripcion' => $f->descripcion,
                    'tipo' => $f->tipo_formateado,
                    'rango_fechas' => $f->rango_fechas
                ];
            })
        ]);
    }

    /**
     * API: Obtener días lectivos en un rango
     */
    public function diasLectivos(Request $request)
    {
        $request->validate([
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after_or_equal:fecha_inicio'
        ]);

        $diasLectivos = Feriado::getDiasLectivos($request->fecha_inicio, $request->fecha_fin);

        return response()->json([
            'dias_lectivos' => $diasLectivos->map(function($fecha) {
                return [
                    'fecha' => $fecha->format('Y-m-d'),
                    'fecha_formateada' => $fecha->format('d/m/Y'),
                    'dia_semana' => $fecha->locale('es')->dayName
                ];
            }),
            'total_dias' => count($diasLectivos)
        ]);
    }
}
