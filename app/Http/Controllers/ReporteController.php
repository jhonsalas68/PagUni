<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AsistenciaDocente;
use App\Models\Horario;
use App\Models\CargaAcademica;
use App\Models\Profesor;
use App\Models\Materia;
use App\Models\Aula;
use Carbon\Carbon;
use Dompdf\Dompdf;
use Dompdf\Options;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AsistenciaExport;
use App\Exports\CargaHorariaExport;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    /**
     * Vista principal de reportes
     */
    public function index()
    {
        $docentes = Profesor::orderBy('nombre')->orderBy('apellido')->get();
        $materias = Materia::orderBy('nombre')->get();
        $aulas = Aula::orderBy('codigo_aula')->get();

        return view('reportes.index', compact('docentes', 'materias', 'aulas'));
    }

    /**
     * Generar reporte estático de asistencia en PDF
     */
    public function reporteEstaticoPDF(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $asistencias = AsistenciaDocente::with(['horario.cargaAcademica.grupo.materia', 'horario.cargaAcademica.profesor', 'horario.aula'])
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_entrada', 'desc')
            ->get();

        $estadisticas = [
            'total_registros' => $asistencias->count(),
            'presentes' => $asistencias->where('estado', 'presente')->count(),
            'tardanzas' => $asistencias->where('estado', 'tardanza')->count(),
            'faltas' => $asistencias->where('estado', 'falta')->count(),
            'justificadas' => $asistencias->where('estado', 'justificado')->count(),
        ];

        $html = view('reportes.pdf.asistencia-estatico', compact('asistencias', 'estadisticas', 'fechaInicio', 'fechaFin'))->render();

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $filename = 'reporte_asistencia_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generar reporte dinámico/personalizado en Excel
     */
    public function reporteDinamicoExcel(Request $request)
    {
        $filtros = [
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin' => $request->input('fecha_fin'),
            'docente_id' => $request->input('docente_id'),
            'materia_id' => $request->input('materia_id'),
            'aula_id' => $request->input('aula_id'),
            'estado' => $request->input('estado'),
            'modalidad' => $request->input('modalidad'),
        ];

        $columnas = $request->input('columnas', [
            'fecha', 'docente', 'materia', 'aula', 'horario', 'estado', 'modalidad'
        ]);

        $filename = 'reporte_personalizado_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new AsistenciaExport($filtros, $columnas), $filename);
    }

    /**
     * Generar reporte de carga horaria por docente
     */
    public function reporteCargaHoraria(Request $request)
    {
        $docenteId = $request->input('docente_id');
        $periodo = $request->input('periodo', Carbon::now()->format('Y') . '-' . (Carbon::now()->month <= 6 ? '1' : '2'));

        // Obtener cargas académicas con el período correcto
        $query = CargaAcademica::with(['profesor', 'grupo.materia', 'horarios'])
            ->where('periodo', $periodo);

        if ($docenteId) {
            $query->where('profesor_id', $docenteId);
        }

        $cargas = $query->get();

        $reporte = [];
        $semanasSemestre = 16; // Duración típica de un semestre
        
        foreach ($cargas as $carga) {
            // Cada materia tiene 4.5 horas semanales, independiente de cuántos días tenga
            // (3 días x 1.5 hrs = 4.5 hrs, o 2 días x 2.25 hrs = 4.5 hrs)
            $horasSemanales = $carga->horarios->count() > 0 ? 4.5 : 0;
            
            // Calcular proyecciones
            $horasMensuales = $horasSemanales * 4; // 4 semanas por mes
            $horasSemestrales = $horasSemanales * $semanasSemestre;

            // Calcular horas impartidas basado en las asistencias
            // Cada asistencia cuenta según la distribución de horas de la materia
            $asistencias = AsistenciaDocente::whereIn('horario_id', $carga->horarios->pluck('id'))
                ->whereIn('estado', ['presente', 'tardanza'])
                ->get();
            
            // Determinar cuántos días tiene la materia para calcular horas por clase
            $diasPorSemana = 0;
            if ($carga->horarios->count() > 0) {
                $primerHorario = $carga->horarios->first();
                $diasPorSemana = count($primerHorario->dias_semana ?? []);
            }
            
            // Calcular horas por clase según días: 3 días = 1.5 hrs/clase, 2 días = 2.25 hrs/clase
            $horasPorClase = $diasPorSemana == 3 ? 1.5 : ($diasPorSemana == 2 ? 2.25 : 1.5);
            
            // Contar cada asistencia como las horas correspondientes
            $horasImpartidasTotal = $asistencias->count() * $horasPorClase;

            // Calcular cumplimiento real (puede exceder 100%)
            $cumplimientoReal = $horasSemestrales > 0 ? round(($horasImpartidasTotal / $horasSemestrales) * 100, 2) : 0;
            
            // Cumplimiento para pago (limitado al 100%)
            $cumplimientoPago = min($cumplimientoReal, 100);

            $reporte[] = [
                'docente' => $carga->profesor->nombre_completo ?? 'N/A',
                'materia' => $carga->grupo->materia->nombre ?? 'N/A',
                'grupo' => $carga->grupo->identificador ?? 'N/A',
                'horas_semanales' => round($horasSemanales, 2),
                'horas_mensuales' => round($horasMensuales, 2),
                'horas_semestrales' => round($horasSemestrales, 2),
                'horas_impartidas' => round($horasImpartidasTotal, 2),
                'porcentaje_cumplimiento' => $cumplimientoReal,
                'porcentaje_pago' => $cumplimientoPago,
            ];
        }

        if ($request->input('formato') === 'excel') {
            $filename = 'carga_horaria_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';
            return Excel::download(new CargaHorariaExport($reporte, $periodo), $filename);
        }

        // PDF por defecto
        $html = view('reportes.pdf.carga-horaria', compact('reporte', 'periodo'))->render();

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'carga_horaria_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Consultar bitácora de acceso y uso
     */
    public function bitacora(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        $tipoActividad = $request->input('tipo_actividad', 'todas');
        $profesorId = $request->input('profesor_id');
        $materiaId = $request->input('materia_id');
        $aulaId = $request->input('aula_id');

        // Query base para actividades
        $query = AsistenciaDocente::with([
            'profesor',
            'horario.cargaAcademica.profesor', 
            'horario.cargaAcademica.grupo.materia', 
            'horario.aula'
        ])->whereBetween('fecha', [$fechaInicio, $fechaFin]);

        // Filtrar por tipo de actividad
        if ($tipoActividad !== 'todas') {
            switch ($tipoActividad) {
                case 'qr_generados':
                    $query->whereNotNull('qr_token');
                    break;
                case 'asistencias':
                    $query->whereIn('estado', ['presente', 'tardanza']);
                    break;
                case 'faltas':
                    $query->where('estado', 'falta');
                    break;
                case 'justificaciones':
                    $query->where('estado', 'justificado');
                    break;
            }
        }

        // Filtrar por profesor
        if ($profesorId) {
            $query->whereHas('horario.cargaAcademica', function($q) use ($profesorId) {
                $q->where('profesor_id', $profesorId);
            });
        }

        // Filtrar por materia
        if ($materiaId) {
            $query->whereHas('horario.cargaAcademica.grupo', function($q) use ($materiaId) {
                $q->where('materia_id', $materiaId);
            });
        }

        // Filtrar por aula
        if ($aulaId) {
            $query->whereHas('horario', function($q) use ($aulaId) {
                $q->where('aula_id', $aulaId);
            });
        }

        $actividades = $query->orderBy('fecha', 'desc')
            ->orderBy('hora_entrada', 'desc')
            ->paginate(20);

        // Estadísticas detalladas
        $estadisticas = [
            'total_actividades' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
            'qr_generados' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->whereNotNull('qr_token')->count(),
            'asistencias_registradas' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->whereIn('estado', ['presente', 'tardanza'])->count(),
            'faltas_registradas' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->where('estado', 'falta')->count(),
            'justificaciones' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->where('estado', 'justificado')->count(),
            'docentes_activos' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->join('horarios', 'asistencia_docente.horario_id', '=', 'horarios.id')
                ->join('carga_academica', 'horarios.carga_academica_id', '=', 'carga_academica.id')
                ->distinct('carga_academica.profesor_id')->count('carga_academica.profesor_id'),
            'materias_activas' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->join('horarios', 'asistencia_docente.horario_id', '=', 'horarios.id')
                ->join('carga_academica', 'horarios.carga_academica_id', '=', 'carga_academica.id')
                ->join('grupos', 'carga_academica.grupo_id', '=', 'grupos.id')
                ->distinct('grupos.materia_id')->count('grupos.materia_id'),
            'aulas_utilizadas' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->join('horarios', 'asistencia_docente.horario_id', '=', 'horarios.id')
                ->distinct('horarios.aula_id')->count('horarios.aula_id'),
        ];

        // Obtener listas para filtros
        $profesores = Profesor::orderBy('nombre')->orderBy('apellido')->get();
        $materias = Materia::orderBy('nombre')->get();
        $aulas = Aula::orderBy('codigo_aula')->get();

        // Actividades por día (para gráfico)
        $actividadesPorDia = DB::select("
            SELECT DATE(created_at) as fecha, COUNT(*) as total 
            FROM asistencia_docente 
            WHERE created_at BETWEEN ? AND ? 
            GROUP BY DATE(created_at) 
            ORDER BY DATE(created_at)
        ", [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);

        // Top profesores más activos
        $profesoresActivos = AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->join('horarios', 'asistencia_docente.horario_id', '=', 'horarios.id')
            ->join('carga_academica', 'horarios.carga_academica_id', '=', 'carga_academica.id')
            ->join('profesores', 'carga_academica.profesor_id', '=', 'profesores.id')
            ->selectRaw('profesores.id, profesores.nombre, profesores.apellido, COUNT(*) as total_actividades')
            ->groupBy('profesores.id', 'profesores.nombre', 'profesores.apellido')
            ->orderBy('total_actividades', 'desc')
            ->limit(10)
            ->get();

        // Materias más activas
        $materiasActivas = AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->join('horarios', 'asistencia_docente.horario_id', '=', 'horarios.id')
            ->join('carga_academica', 'horarios.carga_academica_id', '=', 'carga_academica.id')
            ->join('grupos', 'carga_academica.grupo_id', '=', 'grupos.id')
            ->join('materias', 'grupos.materia_id', '=', 'materias.id')
            ->selectRaw('materias.id, materias.nombre, materias.codigo, COUNT(*) as total_actividades')
            ->groupBy('materias.id', 'materias.nombre', 'materias.codigo')
            ->orderBy('total_actividades', 'desc')
            ->limit(10)
            ->get();

        // Aulas más utilizadas
        $aulasUtilizadas = AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
            ->join('horarios', 'asistencia_docente.horario_id', '=', 'horarios.id')
            ->join('aulas', 'horarios.aula_id', '=', 'aulas.id')
            ->selectRaw('aulas.id, aulas.codigo_aula, aulas.nombre, COUNT(*) as total_actividades')
            ->groupBy('aulas.id', 'aulas.codigo_aula', 'aulas.nombre')
            ->orderBy('total_actividades', 'desc')
            ->limit(10)
            ->get();

        return response()
            ->view('reportes.bitacora', compact(
                'actividades', 'estadisticas', 'fechaInicio', 'fechaFin', 
                'tipoActividad', 'profesorId', 'materiaId', 'aulaId',
                'profesores', 'materias', 'aulas', 'actividadesPorDia', 
                'profesoresActivos', 'materiasActivas', 'aulasUtilizadas'
            ))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Generar reporte de bitácora en PDF
     */
    public function bitacoraPDF(Request $request)
    {
        $fechaInicio = $request->input('fecha_inicio', Carbon::now()->subDays(30)->format('Y-m-d'));
        $fechaFin = $request->input('fecha_fin', Carbon::now()->format('Y-m-d'));
        $tipoActividad = $request->input('tipo_actividad', 'todas');
        $profesorId = $request->input('profesor_id');
        $materiaId = $request->input('materia_id');
        $aulaId = $request->input('aula_id');

        // Query base para actividades
        $query = AsistenciaDocente::with([
            'horario.cargaAcademica.profesor', 
            'horario.cargaAcademica.grupo.materia', 
            'horario.aula'
        ])->whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59']);

        // Aplicar filtros
        if ($tipoActividad !== 'todas') {
            switch ($tipoActividad) {
                case 'qr_generados':
                    $query->whereNotNull('qr_token');
                    break;
                case 'asistencias':
                    $query->whereIn('estado', ['presente', 'tardanza']);
                    break;
                case 'faltas':
                    $query->where('estado', 'falta');
                    break;
                case 'justificaciones':
                    $query->where('estado', 'justificado');
                    break;
            }
        }

        if ($profesorId) {
            $query->whereHas('horario.cargaAcademica', function($q) use ($profesorId) {
                $q->where('profesor_id', $profesorId);
            });
        }

        if ($materiaId) {
            $query->whereHas('horario.cargaAcademica.grupo', function($q) use ($materiaId) {
                $q->where('materia_id', $materiaId);
            });
        }

        if ($aulaId) {
            $query->whereHas('horario', function($q) use ($aulaId) {
                $q->where('aula_id', $aulaId);
            });
        }

        $actividades = $query->orderBy('asistencia_docente.created_at', 'desc')->limit(500)->get();

        // Estadísticas
        $estadisticas = [
            'total_actividades' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
            'qr_generados' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->whereNotNull('qr_token')->count(),
            'asistencias_registradas' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->whereIn('estado', ['presente', 'tardanza'])->count(),
            'faltas_registradas' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->where('estado', 'falta')->count(),
            'justificaciones' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->where('estado', 'justificado')->count(),
            'docentes_activos' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->join('horarios', 'asistencia_docente.horario_id', '=', 'horarios.id')
                ->join('carga_academica', 'horarios.carga_academica_id', '=', 'carga_academica.id')
                ->distinct('carga_academica.profesor_id')->count('carga_academica.profesor_id'),
            'materias_activas' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->join('horarios', 'asistencia_docente.horario_id', '=', 'horarios.id')
                ->join('carga_academica', 'horarios.carga_academica_id', '=', 'carga_academica.id')
                ->join('grupos', 'carga_academica.grupo_id', '=', 'grupos.id')
                ->distinct('grupos.materia_id')->count('grupos.materia_id'),
            'aulas_utilizadas' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->join('horarios', 'asistencia_docente.horario_id', '=', 'horarios.id')
                ->distinct('horarios.aula_id')->count('horarios.aula_id'),
        ];

        // Obtener nombres para filtros
        $profesorFiltro = $profesorId ? Profesor::find($profesorId)->nombre_completo ?? null : null;
        $materiaFiltro = $materiaId ? Materia::find($materiaId)->nombre ?? null : null;
        $aulaFiltro = $aulaId ? Aula::find($aulaId)->codigo_aula ?? null : null;

        $html = view('reportes.pdf.bitacora', compact(
            'actividades', 'estadisticas', 'fechaInicio', 'fechaFin', 
            'tipoActividad', 'profesorFiltro', 'materiaFiltro', 'aulaFiltro'
        ))->render();

        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $options->set('isRemoteEnabled', true);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $filename = 'bitacora_' . Carbon::now()->format('Y-m-d_H-i-s') . '.pdf';
        
        return response($dompdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }

    /**
     * Generar reporte de bitácora en Excel
     */
    public function bitacoraExcel(Request $request)
    {
        $filtros = [
            'fecha_inicio' => $request->input('fecha_inicio'),
            'fecha_fin' => $request->input('fecha_fin'),
            'tipo_actividad' => $request->input('tipo_actividad'),
            'profesor_id' => $request->input('profesor_id'),
            'materia_id' => $request->input('materia_id'),
            'aula_id' => $request->input('aula_id'),
        ];

        $fechaInicio = $filtros['fecha_inicio'] ?? Carbon::now()->subDays(30)->format('Y-m-d');
        $fechaFin = $filtros['fecha_fin'] ?? Carbon::now()->format('Y-m-d');

        // Estadísticas para incluir en el Excel
        $estadisticas = [
            'total_actividades' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])->count(),
            'qr_generados' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->whereNotNull('qr_token')->count(),
            'asistencias_registradas' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->whereIn('estado', ['presente', 'tardanza'])->count(),
            'faltas_registradas' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->where('estado', 'falta')->count(),
            'justificaciones' => AsistenciaDocente::whereBetween('asistencia_docente.created_at', [$fechaInicio . ' 00:00:00', $fechaFin . ' 23:59:59'])
                ->where('estado', 'justificado')->count(),
        ];

        $filename = 'bitacora_' . Carbon::now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new \App\Exports\BitacoraExport($filtros, $estadisticas), $filename);
    }
}
