<?php

namespace App\Http\Controllers;

use App\Services\GeneradorHorarioService;
use App\Models\CargaAcademica;
use App\Models\PeriodoAcademico;
use InvalidArgumentException;
use Illuminate\Http\Request;

class HorarioGeneradorController extends Controller
{
    private $generador;

    public function __construct(GeneradorHorarioService $generador)
    {
        $this->generador = $generador;
    }

    public function index()
    {
        // Obtener periodos disponibles desde el modelo PeriodoAcademico
        // Priorizar activos, pero mostrar todos por si acaso se quiere planificar uno futuro o pasado
        $periodos = PeriodoAcademico::orderBy('anio', 'desc')
            ->orderBy('semestre', 'desc')
            ->pluck('codigo');

        return view('admin.horarios.generar', compact('periodos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'periodo' => 'required|string',
        ]);

        try {
            $resultado = $this->generador->generar($request->periodo);

            $mensaje = "Proceso finalizado. Cargas procesadas: {$resultado['procesados']}. Horarios asignados: {$resultado['asignados']}.";
            
            if (!empty($resultado['conflictos'])) {
                return redirect()->route('admin.horarios.generador.index')
                    ->with('warning', $mensaje)
                    ->with('conflictos', $resultado['conflictos']);
            }

            return redirect()->route('admin.horarios.generador.index')
                ->with('success', $mensaje);
                
        } catch (\Exception $e) {
            return back()->with('error', 'Error inesperado: ' . $e->getMessage());
        }
    }

    public function stats(Request $request)
    {
        $periodo = $request->query('periodo');
        
        if (!$periodo) {
            return response()->json(['total' => 0, 'horarios' => []]);
        }
        
        $total = \App\Models\Horario::where('periodo_academico', $periodo)->count();
        
        $horarios = \App\Models\Horario::with(['cargaAcademica.materia', 'cargaAcademica.grupo', 'aula'])
            ->where('periodo_academico', $periodo)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(function($h) {
                return [
                    'materia' => $h->cargaAcademica->materia->nombre ?? 'N/A',
                    'grupo' => $h->cargaAcademica->grupo->identificador ?? 'N/A',
                    'dia' => $h->dias_semana, // Probable JSON string
                    'hora' => substr($h->hora_inicio, 0, 5) . ' - ' . substr($h->hora_fin, 0, 5),
                    'aula' => $h->aula->nombre ?? 'N/A'
                ];
            });

        return response()->json([
            'total' => $total,
            'horarios' => $horarios
        ]);
    }
}
