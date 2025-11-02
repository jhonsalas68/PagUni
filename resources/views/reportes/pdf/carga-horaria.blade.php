<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reporte de Carga Horaria</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 15px;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            font-size: 10px;
        }
        .numero {
            text-align: right;
        }
        .porcentaje {
            text-align: right;
            font-weight: bold;
        }
        .porcentaje.alto { color: #28a745; }
        .porcentaje.medio { color: #ffc107; }
        .porcentaje.bajo { color: #dc3545; }
        .resumen {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .resumen h3 {
            margin: 0 0 10px 0;
            color: #333;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Reporte de Carga Horaria por Docente</h1>
        <p>Período Académico: {{ $periodo }}</p>
        <p>Generado el: {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Docente</th>
                <th>Materia</th>
                <th>Grupo</th>
                <th>Horas Asignadas</th>
                <th>Horas Impartidas</th>
                <th>Cumplimiento (%)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAsignadas = 0;
                $totalImpartidas = 0;
            @endphp
            @forelse($reporte as $item)
                @php
                    $totalAsignadas += $item['horas_asignadas'];
                    $totalImpartidas += $item['horas_impartidas'];
                    $porcentaje = $item['porcentaje_cumplimiento'];
                    $clasePortentaje = $porcentaje >= 90 ? 'alto' : ($porcentaje >= 70 ? 'medio' : 'bajo');
                @endphp
                <tr>
                    <td>{{ $item['docente'] }}</td>
                    <td>{{ $item['materia'] }}</td>
                    <td>{{ $item['grupo'] }}</td>
                    <td class="numero">{{ $item['horas_asignadas'] }}</td>
                    <td class="numero">{{ $item['horas_impartidas'] }}</td>
                    <td class="porcentaje {{ $clasePortentaje }}">{{ $porcentaje }}%</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" style="text-align: center; color: #666;">
                        No se encontraron datos de carga horaria para el período seleccionado
                    </td>
                </tr>
            @endforelse
        </tbody>
        @if(count($reporte) > 0)
        <tfoot>
            <tr style="background-color: #e9ecef; font-weight: bold;">
                <td colspan="3">TOTALES</td>
                <td class="numero">{{ round($totalAsignadas, 2) }}</td>
                <td class="numero">{{ round($totalImpartidas, 2) }}</td>
                <td class="porcentaje">
                    {{ $totalAsignadas > 0 ? round(($totalImpartidas / $totalAsignadas) * 100, 2) : 0 }}%
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

    @if(count($reporte) > 0)
    <div class="resumen">
        <h3>Resumen Ejecutivo</h3>
        <p><strong>Total de registros:</strong> {{ count($reporte) }}</p>
        <p><strong>Horas totales asignadas:</strong> {{ round($totalAsignadas, 2) }} horas</p>
        <p><strong>Horas totales impartidas:</strong> {{ round($totalImpartidas, 2) }} horas</p>
        <p><strong>Porcentaje general de cumplimiento:</strong> 
            {{ $totalAsignadas > 0 ? round(($totalImpartidas / $totalAsignadas) * 100, 2) : 0 }}%
        </p>
        <p><strong>Docentes con cumplimiento ≥90%:</strong> 
            {{ collect($reporte)->where('porcentaje_cumplimiento', '>=', 90)->count() }}
        </p>
        <p><strong>Docentes con cumplimiento <70%:</strong> 
            {{ collect($reporte)->where('porcentaje_cumplimiento', '<', 70)->count() }}
        </p>
    </div>
    @endif

    <div class="footer">
        <p>Sistema de Gestión Universitaria - Reporte de Carga Horaria</p>
        <p>Este reporte compara las horas académicas asignadas vs. las horas efectivamente impartidas</p>
    </div>
</body>
</html>