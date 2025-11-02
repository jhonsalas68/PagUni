<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Bitácora - {{ date('d/m/Y') }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            color: #333;
            font-size: 18px;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .filtros {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        .filtros h3 {
            margin-top: 0;
            color: #495057;
            font-size: 14px;
        }
        .filtros p {
            margin: 5px 0;
            font-size: 11px;
        }
        .estadisticas {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .stat-card {
            flex: 1;
            min-width: 120px;
            margin: 5px;
            padding: 10px;
            background-color: #e9ecef;
            border-radius: 5px;
            text-align: center;
        }
        .stat-card h4 {
            margin: 0 0 5px 0;
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
        }
        .stat-card .number {
            font-size: 16px;
            font-weight: bold;
            color: #495057;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        .table th,
        .table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: left;
            font-size: 10px;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        .table tr:nth-child(even) {
            background-color: #f8f9fa;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }
        .badge-success { background-color: #28a745; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-danger { background-color: #dc3545; }
        .badge-secondary { background-color: #6c757d; }
        .badge-primary { background-color: #007bff; }
        .badge-info { background-color: #17a2b8; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>📋 Reporte de Bitácora de Actividades</h1>
        <p>Sistema Universitario - Generado el {{ date('d/m/Y H:i:s') }}</p>
        <p>Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
    </div>

    <div class="filtros">
        <h3>🔍 Filtros Aplicados</h3>
        <p><strong>Período:</strong> {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
        <p><strong>Tipo de Actividad:</strong> {{ ucfirst(str_replace('_', ' ', $tipoActividad)) }}</p>
        @if($profesorFiltro)
            <p><strong>Profesor:</strong> {{ $profesorFiltro }}</p>
        @endif
        @if($materiaFiltro)
            <p><strong>Materia:</strong> {{ $materiaFiltro }}</p>
        @endif
        @if($aulaFiltro)
            <p><strong>Aula:</strong> {{ $aulaFiltro }}</p>
        @endif
    </div>

    <div class="estadisticas">
        <div class="stat-card">
            <h4>Total Actividades</h4>
            <div class="number">{{ $estadisticas['total_actividades'] }}</div>
        </div>
        <div class="stat-card">
            <h4>QR Generados</h4>
            <div class="number">{{ $estadisticas['qr_generados'] }}</div>
        </div>
        <div class="stat-card">
            <h4>Asistencias</h4>
            <div class="number">{{ $estadisticas['asistencias_registradas'] }}</div>
        </div>
        <div class="stat-card">
            <h4>Faltas</h4>
            <div class="number">{{ $estadisticas['faltas_registradas'] }}</div>
        </div>
        <div class="stat-card">
            <h4>Justificaciones</h4>
            <div class="number">{{ $estadisticas['justificaciones'] }}</div>
        </div>
        <div class="stat-card">
            <h4>Docentes Activos</h4>
            <div class="number">{{ $estadisticas['docentes_activos'] }}</div>
        </div>
        <div class="stat-card">
            <h4>Materias Activas</h4>
            <div class="number">{{ $estadisticas['materias_activas'] }}</div>
        </div>
        <div class="stat-card">
            <h4>Aulas Utilizadas</h4>
            <div class="number">{{ $estadisticas['aulas_utilizadas'] }}</div>
        </div>
    </div>

    @if($actividades->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Docente</th>
                    <th>Materia</th>
                    <th>Grupo</th>
                    <th>Aula</th>
                    <th>Actividad</th>
                    <th>Estado</th>
                    <th>Modalidad</th>
                    <th>Duración</th>
                </tr>
            </thead>
            <tbody>
                @foreach($actividades as $actividad)
                    <tr>
                        <td>
                            {{ $actividad->created_at->format('d/m/Y') }}<br>
                            <small>{{ $actividad->created_at->format('H:i:s') }}</small>
                        </td>
                        <td>{{ $actividad->horario->cargaAcademica->profesor->nombre_completo ?? 'N/A' }}</td>
                        <td>
                            <strong>{{ $actividad->horario->cargaAcademica->grupo->materia->codigo ?? 'N/A' }}</strong><br>
                            <small>{{ $actividad->horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</small>
                        </td>
                        <td>{{ $actividad->horario->cargaAcademica->grupo->identificador ?? 'N/A' }}</td>
                        <td>
                            {{ $actividad->horario->aula->codigo_aula ?? 'N/A' }}<br>
                            <small>{{ $actividad->horario->aula->nombre ?? '' }}</small>
                        </td>
                        <td>
                            @if($actividad->qr_token)
                                <span class="badge badge-primary">QR Generado</span>
                            @else
                                <span class="badge badge-info">Registro</span>
                            @endif
                        </td>
                        <td>
                            @switch($actividad->estado)
                                @case('presente')
                                    <span class="badge badge-success">Presente</span>
                                    @break
                                @case('tardanza')
                                    <span class="badge badge-warning">Tardanza</span>
                                    @break
                                @case('falta')
                                    <span class="badge badge-danger">Falta</span>
                                    @break
                                @case('justificada')
                                    <span class="badge badge-secondary">Justificada</span>
                                    @break
                                @default
                                    <span class="badge badge-secondary">{{ ucfirst($actividad->estado) }}</span>
                            @endswitch
                        </td>
                        <td>{{ ucfirst($actividad->modalidad ?? 'N/A') }}</td>
                        <td>
                            @if($actividad->duracion_clase)
                                {{ $actividad->duracion_clase }} min
                            @else
                                N/A
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>📭 No se encontraron actividades en el período y filtros seleccionados.</p>
        </div>
    @endif

    <div class="footer">
        <p>Sistema Universitario - Reporte generado automáticamente</p>
        <p>Total de registros: {{ $actividades->count() }} | Página generada el {{ date('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>