@extends('layouts.dashboard')

@section('title', 'Generador Automático de Horarios')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-magic"></i> Generador Automático de Horarios
        </h1>
        <a href="{{ route('admin.horarios.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Volver a Horarios
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('conflictos'))
        <div class="card shadow mb-4 border-left-warning">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-warning">Conflictos Encontrados</h6>
            </div>
            <div class="card-body">
                <p>Las siguientes cargas académicas no pudieron ser asignadas automáticamente debido a falta de disponibilidad de aulas o profesores:</p>
                <ul>
                    @foreach(session('conflictos') as $conflicto)
                        <li class="text-danger">{{ $conflicto }}</li>
                    @endforeach
                </ul>
                <p class="text-muted small">Intente liberar horarios de profesores, habilitar más aulas, o asignar estas cargas manualmente.</p>
            </div>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">Configuración de Generación</h6>
                </div>
                <div class="card-body">
                    <p class="mb-4">
                        Este proceso asignará horarios automáticamente a todas las cargas académicas del periodo seleccionado que <strong>aún no tengan horario asignado</strong>.
                        Se respetarán los horarios ya existentes.
                    </p>
                    
                    <form action="{{ route('admin.horarios.generador.store') }}" method="POST" onsubmit="return confirm('¿Está seguro de iniciar la generación automática? Este proceso puede tardar unos segundos.');">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="periodo" class="form-label font-weight-bold">Periodo Académico:</label>
                            <select name="periodo" id="periodo" class="form-select" required>
                                <option value="">-- Seleccione un Periodo --</option>
                                @foreach($periodos as $periodo)
                                    <option value="{{ $periodo }}">{{ $periodo }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> <strong>Criterios del Algoritmo:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Prioriza grupos más grandes y materias con más horas.</li>
                                <li>Evita solapamiento de horarios para el mismo profesor.</li>
                                <li>Busca aulas con capacidad suficiente.</li>
                                <li>Asigna bloques de máximo 2 horas consecutivas.</li>
                            </ul>
                        </div>

                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-cogs"></i> Iniciar Generación Automática
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Estado Actual</h6>
                </div>
                <div class="card-body">
                    <div id="stats-container" class="text-center py-5">
                       <i class="fas fa-calendar-alt fa-4x text-gray-300 mb-3"></i>
                       <p class="text-muted">Seleccione un periodo para ver estadísticas y horarios generados.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const periodoSelect = document.getElementById('periodo');
    const statsContainer = document.getElementById('stats-container');
    
    // Función para cargar estadísticas
    function loadStats(periodo) {
        if (!periodo) {
            statsContainer.innerHTML = `
                <div class="text-center py-5">
                    <i class="fas fa-calendar-alt fa-4x text-gray-300 mb-3"></i>
                    <p class="text-muted">Seleccione un periodo para ver estadísticas.</p>
                </div>
            `;
            return;
        }

        statsContainer.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div><p>Cargando datos...</p></div>';

        fetch(`{{ route('admin.horarios.generador.stats') }}?periodo=${periodo}`)
            .then(response => response.json())
            .then(data => {
                if (data.total === 0) {
                     statsContainer.innerHTML = `
                        <div class="text-center py-5">
                            <i class="fas fa-info-circle fa-4x text-info mb-3"></i>
                            <p class="text-info">No hay horarios generados para el periodo ${periodo}.</p>
                            <p class="small text-muted">Use el botón de la izquierda para generar.</p>
                        </div>
                    `;
                } else {
                    let rows = '';
                    data.horarios.forEach(h => {
                        // Limpiar JSON de dias si viene sucio
                        let dias = h.dia;
                        if (typeof dias === 'string') {
                            try { dias = JSON.parse(dias).join(', '); } catch(e) { dias = h.dia; }
                        } else if (Array.isArray(dias)) {
                             dias = dias.join(', ');
                        }

                        rows += `
                            <tr>
                                <td><small class="font-weight-bold">${h.materia}</small><br><span class="badge bg-light text-dark text-xs">${h.grupo}</span></td>
                                <td class="small">${dias}<br>${h.hora}</td>
                                <td><span class="badge bg-primary">${h.aula}</span></td>
                            </tr>
                        `;
                    });

                    statsContainer.innerHTML = `
                        <div class="text-center mb-3">
                            <h4 class="font-weight-bold text-primary">${data.total}</h4>
                            <span class="text-uppercase text-xs font-weight-bold text-gray-600">Horarios Generados</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-striped" width="100%" cellspacing="0">
                                <thead class="thead-light">
                                    <tr>
                                        <th>Materia / Grupo</th>
                                        <th>Horario</th>
                                        <th>Aula</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${rows}
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-2">
                            <a href="{{ route('admin.horarios.index') }}?periodo=${periodo}" class="btn btn-sm btn-link">Ver todo</a>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statsContainer.innerHTML = '<p class="text-danger text-center">Error al cargar datos.</p>';
            });
    }

    periodoSelect.addEventListener('change', function() {
        loadStats(this.value);
    });
    
    // Si ya hay un valor seleccionado (ej: old input), cargar
    if(periodoSelect.value) {
        loadStats(periodoSelect.value);
    }
});
</script>
@endsection
