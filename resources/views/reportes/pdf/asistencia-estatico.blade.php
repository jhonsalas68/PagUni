<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Asistencia</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
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
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .estadisticas {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        .estadistica {
            display: table-cell;
            text-align: center;
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        .estadistica .numero {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        .estadistica .label {
            font-size: 10px;
            color: #666;
            text-transform: uppercase;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .estado-presente { color: #28a745; font-weight: bold; }
        .estado-tardanza { color: #ffc107; font-weight: bold; }
        .estado-falta { color: #dc3545; font-weight: bold; }
        .estado-justificada { color: #6c757d; font-weight: bold; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Asistencia Docente</h1>
        <p>Período: {{ \Carbon\Carbon::parse($fechaInicio)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($fechaFin)->format('d/m/Y') }}</p>
        <p>Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <div class="estadisticas">
        <div class="estadistica">
            <div class="numero">{{ $estadisticas['total_registros'] }}</div>
            <div class="label">Total Registros</div>
        </div>
        <div class="estadistica">
            <div class="numero">{{ $estadisticas['presentes'] }}</div>
            <div class="label">Presentes</div>
        </div>
        <div class="estadistica">
            <div class="numero">{{ $estadisticas['tardanzas'] }}</div>
            <div class="label">Tardanzas</div>
        </div>
        <div class="estadistica">
            <div class="numero">{{ $estadisticas['faltas'] }}</div>
            <div class="label">Faltas</div>
        </div>
        <div class="estadistica">
            <div class="numero">{{ $estadisticas['justificadas'] }}</div>
            <div class="label">Justificadas</div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Docente</th>
                <th>Materia</th>
                <th>Aula</th>
                <th>Horario</th>
                <th>Estado</th>
                <th>Modalidad</th>
                <th>Duración</th>
            </tr>
        </thead>
        <tbody>
            @forelse($asistencias as $asistencia)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($asistencia->fecha)->format('d/m/Y') }}</td>
                    <td>{{ $asistencia->horario->cargaAcademica->profesor->nombre_completo ?? 'N/A' }}</td>
                    <td>{{ $asistencia->horario->cargaAcademica->grupo->materia->nombre ?? 'N/A' }}</td>
                    <td>{{ $asistencia->horario->aula->codigo_aula ?? 'N/A' }}</td>
                    <td>{{ $asistencia->horario->hora_inicio }} - {{ $asistencia->horario->hora_fin }}</td>
                    <td class="estado-{{ $asistencia->estado }}">
                        @switch($asistencia->estado)
                            @case('presente') Presente @break
                            @case('tardanza') Tardanza @break
                            @case('falta') Falta @break
                            @case('justificada') Justificada @break
                            @default {{ $asistencia->estado }}
                        @endswitch
                    </td>
                    <td>{{ ucfirst($asistencia->modalidad ?? 'N/A') }}</td>
                    <td>{{ $asistencia->duracion_clase ?? 0 }} min</td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #666;">
                        No se encontraron registros de asistencia en el período seleccionado
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Sistema de Gestión Universitaria - Reporte generado automáticamente</p>
        <p>Total de registros: {{ $asistencias->count() }}</p>
    </div>
</body>
</html>