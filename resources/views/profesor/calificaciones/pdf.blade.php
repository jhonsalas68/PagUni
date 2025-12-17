<!DOCTYPE html>
<html>
<head>
    <title>Resumen de Calificaciones</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }
        .header h2 {
            margin: 5px 0 0;
            font-size: 14px;
            color: #666;
        }
        .info {
            margin-bottom: 15px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 5px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-left {
            text-align: left;
        }
        .aprobado {
            color: green;
            font-weight: bold;
        }
        .reprobado {
            color: red;
            font-weight: bold;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #999;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Universidad Autónoma Gabriel René Moreno</h1>
        <h2>Sábana de Calificaciones</h2>
    </div>

    <div class="info">
        <strong>Materia:</strong> {{ $grupo->materia->nombre }} ({{ $grupo->materia->codigo }}) <br>
        <strong>Grupo:</strong> {{ $grupo->identificador }} <br>
        <strong>Docente:</strong> {{ $grupo->cargaAcademica->profesor->nombre }} {{ $grupo->cargaAcademica->profesor->apellido }} <br>
        <strong>Fecha de Emisión:</strong> {{ date('d/m/Y H:i') }}
    </div>

    <table>
        <thead>
            <tr>
                <th rowspan="2">N°</th>
                <th rowspan="2" class="text-left">Estudiante</th>
                <th rowspan="2">Registro</th>
                <th colspan="{{ $tiposEvaluacion->count() }}">Evaluaciones</th>
                <th rowspan="2">Promedio</th>
                <th rowspan="2">Estado</th>
            </tr>
            <tr>
                @foreach($tiposEvaluacion as $tipo)
                    <th>
                        {{ $tipo->nombre }} <br>
                        <small>({{ $tipo->ponderacion }}%)</small>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($datos as $index => $dato)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">
                        {{ $dato['estudiante']->apellido }}, {{ $dato['estudiante']->nombre }}
                    </td>
                    <td>{{ $dato['codigo'] }}</td>
                    
                    @foreach($tiposEvaluacion as $tipo)
                        <td>
                            @if($dato['notas'][$tipo->id] !== '-')
                                {{ $dato['notas'][$tipo->id] }}
                            @else
                                -
                            @endif
                        </td>
                    @endforeach
                    
                    <td>{{ $dato['promedio'] }}</td>
                    <td>
                        @if($dato['estado'] == 'Aprobado')
                            <span class="aprobado">Aprobado</span>
                        @else
                            <span class="reprobado">Reprobado</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Documento generado automáticamente por el Sistema de Gestión Universitaria (SGU).
    </div>
</body>
</html>
