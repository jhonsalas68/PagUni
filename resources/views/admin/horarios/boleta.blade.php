@extends('layouts.dashboard')

@section('title', 'Boleta de Horarios')

@section('content')
<div class="container-fluid">
    <div class="row mb-3 no-print">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h4>Boleta de Inscripción</h4>
                <div>
                    <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <button class="btn btn-primary" onclick="window.print()">
                        <i class="fas fa-print"></i> Imprimir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <!-- Encabezado -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <h3 class="mb-3 fw-bold">PERIODO NORMAL 2-2025</h3>
                    <h5 class="mb-3">187-3 INGENIERÍA INFORMÁTICA</h5>
                    <p class="mb-1"><strong>MODALIDAD:</strong> PRESENCIAL</p>
                    <p class="mb-0"><strong>LOCALIDAD:</strong> SANTA CRUZ</p>
                </div>
                <div class="col-md-4 text-end">
                    <h4 class="text-primary mb-2 fw-bold">218160232</h4>
                    <p class="small text-muted mb-0">SALAS ROJAS, JHONNY STIVEN</p>
                    <p class="small text-muted">12890737-SCZ</p>
                    <h5 class="mt-3 text-danger fw-bold">ORIGEN</h5>
                </div>
            </div>

            <!-- Tabla de Horarios -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>SIGLA</th>
                            <th>GRUPO</th>
                            <th>MATERIA</th>
                            <th>MODALIDAD</th>
                            <th>NIVEL</th>
                            <th>HORARIO</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $diasMap = [
                                'lunes' => 'Lu',
                                'martes' => 'Ma',
                                'miercoles' => 'Mi',
                                'jueves' => 'Ju',
                                'viernes' => 'Vi',
                                'sabado' => 'Sa'
                            ];
                            
                            // Agrupar horarios por materia
                            $horariosPorMateria = [];
                            foreach($horarios as $horario) {
                                $materiaId = $horario->cargaAcademica->grupo->materia->id;
                                if (!isset($horariosPorMateria[$materiaId])) {
                                    $horariosPorMateria[$materiaId] = [
                                        'materia' => $horario->cargaAcademica->grupo->materia,
                                        'grupo' => $horario->cargaAcademica->grupo,
                                        'horarios' => []
                                    ];
                                }
                                $horariosPorMateria[$materiaId]['horarios'][] = $horario;
                            }
                        @endphp

                        @foreach($horariosPorMateria as $data)
                            @php
                                $materia = $data['materia'];
                                $grupo = $data['grupo'];
                                $horariosMateria = $data['horarios'];
                                
                                // Construir string de horario
                                $horarioTexto = '';
                                foreach($horariosMateria as $h) {
                                    $diasTexto = [];
                                    foreach($h->dias_semana as $dia) {
                                        $diasTexto[] = $diasMap[$dia] ?? ucfirst(substr($dia, 0, 2));
                                    }
                                    $diasStr = implode(' ', $diasTexto);
                                    $horaInicio = substr($h->hora_inicio, 0, 5);
                                    $horaFin = substr($h->hora_fin, 0, 5);
                                    $aula = $h->aula->codigo_aula;
                                    
                                    $horarioTexto .= $diasStr . ' ' . $horaInicio . '-' . $horaFin . ' ' . $aula . ' |';
                                }
                                $horarioTexto = rtrim($horarioTexto, ' |');
                            @endphp
                            <tr>
                                <td><strong>{{ $materia->codigo }}</strong></td>
                                <td>{{ $grupo->identificador }}</td>
                                <td>{{ strtoupper($materia->nombre) }}</td>
                                <td>PRESENCIAL</td>
                                <td class="text-center">{{ $materia->semestre }}</td>
                                <td><small>{{ $horarioTexto }}</small></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        .sidebar, .mobile-navbar, .no-print {
            display: none !important;
        }
        
        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
        
        .card {
            border: none !important;
            box-shadow: none !important;
        }
        
        body {
            font-size: 11px !important;
        }
        
        table {
            font-size: 10px !important;
        }
        
        th, td {
            padding: 6px 4px !important;
        }
        
        h3 {
            font-size: 16px !important;
        }
        
        h4, h5 {
            font-size: 14px !important;
        }
    }
    
    .table-bordered {
        border: 1px solid #000 !important;
    }
    
    .table-bordered th,
    .table-bordered td {
        border: 1px solid #000 !important;
        vertical-align: middle;
        padding: 10px 8px;
    }
    
    .table-light {
        background-color: #e9ecef !important;
    }
    
    .table-light th {
        font-weight: 600;
        text-transform: uppercase;
        font-size: 12px;
    }
    
    tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    tbody tr {
        background-color: #fff;
    }
    
    tbody tr:nth-child(even) {
        background-color: #f9f9f9;
    }
</style>
@endsection
