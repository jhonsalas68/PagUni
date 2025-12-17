<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Rendimiento Académico</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
        }
        
        .header h1 {
            color: #007bff;
            margin: 0;
            font-size: 24px;
        }
        
        .header h2 {
            color: #6c757d;
            margin: 5px 0;
            font-size: 16px;
            font-weight: normal;
        }
        
        .info-section {
            margin-bottom: 25px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
        }
        
        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        
        .info-label {
            font-weight: bold;
            color: #495057;
        }
        
        .estadisticas {
            display: flex;
            justify-content: space-around;
            margin-bottom: 25px;
            text-align: center;
        }
        
        .estadistica {
            background-color: #fff;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            min-width: 120px;
        }
        
        .estadistica-numero {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .estadistica-label {
            font-size: 11px;
            color: #6c757d;
            text-transform: uppercase;
        }
        
        .aprobados { color: #28a745; }
        .reprobados { color: #dc3545; }
        .total { color: #007bff; }
        .promedio { color: #ffc107; }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            border: 1px solid #dee2e6;
            padding: 10px;
            text-align: left;
        }
        
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
            text-align: center;
        }
        
        td {
            vertical-align: middle;
        }
        
        .text-center {
            text-align: center;
        }
        
        .estado-aprobado {
            background-color: #d4edda;
            color: #155724;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .estado-reprobado {
            background-color: #f8d7da;
            color: #721c24;
            padding: 5px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 11px;
        }
        
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #6c757d;
            border-top: 1px solid #dee2e6;
            padding-top: 15px;
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
        <h1>REPORTE DE RENDIMIENTO ACADÉMICO</h1>
        <h2>Universidad Autónoma Gabriel René Moreno</h2>
        <h2>Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones</h2>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Materia:</span>
            <span>{{ $materia ? $materia->nombre . ' (' . $materia->codigo . ')' : 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Grupo:</span>
            <span>{{ $grupo ? 'Grupo ' . $grupo->identificador : 'N/A' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Generación:</span>
            <span>{{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Periodo Académico:</span>
            <span>2024-2</span>
        </div>
    </div>

    @if(!empty($estudiantes))
        <div class="estadisticas">
            <div class="estadistica">
                <div class="estadistica-numero total">{{ $estadisticas['total'] }}</div>
                <div class="estadistica-label">Total Estudiantes</div>
            </div>
            <div class="estadistica">
                <div class="estadistica-numero aprobados">{{ $estadisticas['aprobados'] }}</div>
                <div class="estadistica-label">Aprobados</div>
            </div>
            <div class="estadistica">
                <div class="estadistica-numero reprobados">{{ $estadisticas['reprobados'] }}</div>
                <div class="estadistica-label">Reprobados</div>
            </div>
            <div class="estadistica">
                <div class="estadistica-numero promedio">{{ $estadisticas['promedio_general'] }}</div>
                <div class="estadistica-label">Promedio General</div>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 15%;">Código</th>
                    <th style="width: 45%;">Estudiante</th>
                    <th style="width: 20%;">Nota Final</th>
                    <th style="width: 20%;">Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($estudiantes as $estudiante)
                    <tr>
                        <td class="text-center">{{ $estudiante['codigo'] }}</td>
                        <td>{{ $estudiante['nombres'] }}</td>
                        <td class="text-center" style="font-weight: bold;">{{ $estudiante['nota_final'] }}</td>
                        <td class="text-center">
                            @if($estudiante['estado'] == 'Aprobado')
                                <span class="estado-aprobado">✓ APROBADO</span>
                            @else
                                <span class="estado-reprobado">✗ REPROBADO</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="no-data">
            <p>No se encontraron estudiantes para mostrar en este reporte.</p>
        </div>
    @endif

    <div class="footer">
        <p>
            <strong>Sistema de Gestión Académica - UAGRM FICCT</strong><br>
            Generado el {{ \Carbon\Carbon::now()->format('d/m/Y') }} a las {{ \Carbon\Carbon::now()->format('H:i:s') }}
        </p>
    </div>
</body>
</html>