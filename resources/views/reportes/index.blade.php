@extends('layouts.dashboard')

@section('title', 'Reportes del Sistema')

@section('head')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">
                    <i class="fas fa-chart-bar"></i> Reportes del Sistema
                </h1>
            </div>
        </div>
    </div>

    <!-- Reportes Cards -->
    <div class="row">
        <!-- Reporte Estático PDF -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Reporte Estático
                            </div>
                            <div class="h6 mb-2 font-weight-bold text-gray-800">Asistencia PDF</div>
                            <div class="mb-3">
                                <small class="text-muted">Genera un informe predefinido de asistencia en formato PDF</small>
                            </div>
                            <button class="btn btn-primary btn-sm" onclick="mostrarModalEstatico()">
                                <i class="fas fa-file-pdf"></i> Generar PDF
                            </button>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-pdf fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte Dinámico Excel -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Reporte Personalizado
                            </div>
                            <div class="h6 mb-2 font-weight-bold text-gray-800">Excel Dinámico</div>
                            <div class="mb-3">
                                <small class="text-muted">Selecciona atributos para generar reportes a medida</small>
                            </div>
                            <button class="btn btn-success btn-sm" onclick="mostrarModalDinamico()">
                                <i class="fas fa-file-excel"></i> Generar Excel
                            </button>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-excel fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reporte Carga Horaria -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Carga Horaria
                            </div>
                            <div class="h6 mb-2 font-weight-bold text-gray-800">Por Docente</div>
                            <div class="mb-3">
                                <small class="text-muted">Horas asignadas vs. horas efectivas impartidas</small>
                            </div>
                            <button class="btn btn-info btn-sm" onclick="mostrarModalCargaHoraria()">
                                <i class="fas fa-clock"></i> Generar Reporte
                            </button>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('user_type') === 'administrador')
        <!-- Bitácora de Acceso -->
        <div class="col-lg-6 col-xl-4 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Bitácora
                            </div>
                            <div class="h6 mb-2 font-weight-bold text-gray-800">Acceso y Uso</div>
                            <div class="mb-3">
                                <small class="text-muted">Auditoría de actividades y modificaciones</small>
                            </div>
                            <a href="{{ route('reportes.bitacora') }}" class="btn btn-warning btn-sm">
                                <i class="fas fa-list-alt"></i> Ver Bitácora
                            </a>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Modal Reporte Estático -->
<div class="modal fade" id="modalEstatico" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-pdf"></i> Reporte Estático de Asistencia
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('reportes.estatico-pdf') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inicio_estatico" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio_estatico" name="fecha_inicio" 
                                       value="{{ date('Y-m-01') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_fin_estatico" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin_estatico" name="fecha_fin" 
                                       value="{{ date('Y-m-t') }}" required>
                            </div>
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Este reporte incluye todas las asistencias registradas en el período seleccionado con estadísticas generales.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download"></i> Descargar PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Reporte Dinámico -->
<div class="modal fade" id="modalDinamico" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="fas fa-file-excel"></i> Reporte Personalizado Excel
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('reportes.dinamico-excel') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="text-success">Filtros de Datos</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_inicio_dinamico" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="fecha_inicio_dinamico" name="fecha_inicio">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="fecha_fin_dinamico" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="fecha_fin_dinamico" name="fecha_fin">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="docente_dinamico" class="form-label">Docente</label>
                                <select class="form-select" id="docente_dinamico" name="docente_id">
                                    <option value="">Todos los docentes</option>
                                    @foreach($docentes as $docente)
                                        <option value="{{ $docente->id }}">{{ $docente->nombre_completo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="materia_dinamico" class="form-label">Materia</label>
                                <select class="form-select" id="materia_dinamico" name="materia_id">
                                    <option value="">Todas las materias</option>
                                    @foreach($materias as $materia)
                                        <option value="{{ $materia->id }}">{{ $materia->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="aula_dinamico" class="form-label">Aula</label>
                                <select class="form-select" id="aula_dinamico" name="aula_id">
                                    <option value="">Todas las aulas</option>
                                    @foreach($aulas as $aula)
                                        <option value="{{ $aula->id }}">{{ $aula->codigo_aula }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="estado_dinamico" class="form-label">Estado</label>
                                <select class="form-select" id="estado_dinamico" name="estado">
                                    <option value="">Todos los estados</option>
                                    <option value="presente">Presente</option>
                                    <option value="tardanza">Tardanza</option>
                                    <option value="falta">Falta</option>
                                    <option value="justificada">Justificada</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Columnas -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="text-success">Columnas a Incluir</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columnas[]" value="fecha" id="col_fecha" checked>
                                        <label class="form-check-label" for="col_fecha">Fecha</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columnas[]" value="docente" id="col_docente" checked>
                                        <label class="form-check-label" for="col_docente">Docente</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columnas[]" value="materia" id="col_materia" checked>
                                        <label class="form-check-label" for="col_materia">Materia</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columnas[]" value="grupo" id="col_grupo">
                                        <label class="form-check-label" for="col_grupo">Grupo</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columnas[]" value="aula" id="col_aula" checked>
                                        <label class="form-check-label" for="col_aula">Aula</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columnas[]" value="horario" id="col_horario" checked>
                                        <label class="form-check-label" for="col_horario">Horario</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columnas[]" value="estado" id="col_estado" checked>
                                        <label class="form-check-label" for="col_estado">Estado</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columnas[]" value="modalidad" id="col_modalidad" checked>
                                        <label class="form-check-label" for="col_modalidad">Modalidad</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columnas[]" value="hora_entrada" id="col_entrada">
                                        <label class="form-check-label" for="col_entrada">Hora Entrada</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columnas[]" value="hora_salida" id="col_salida">
                                        <label class="form-check-label" for="col_salida">Hora Salida</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columnas[]" value="duracion" id="col_duracion">
                                        <label class="form-check-label" for="col_duracion">Duración</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="columnas[]" value="sesion" id="col_sesion">
                                        <label class="form-check-label" for="col_sesion">Sesión</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-download"></i> Descargar Excel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Carga Horaria -->
<div class="modal fade" id="modalCargaHoraria" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-clock"></i> Reporte de Carga Horaria
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('reportes.carga-horaria') }}" method="POST" target="_blank">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="docente_carga" class="form-label">Docente (Opcional)</label>
                        <select class="form-select" id="docente_carga" name="docente_id">
                            <option value="">Todos los docentes</option>
                            @foreach($docentes as $docente)
                                <option value="{{ $docente->id }}">{{ $docente->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="periodo_carga" class="form-label">Período Académico</label>
                        <select class="form-select" id="periodo_carga" name="periodo" required>
                            <option value="{{ date('Y') }}-1" {{ date('n') <= 6 ? 'selected' : '' }}>{{ date('Y') }}-1</option>
                            <option value="{{ date('Y') }}-2" {{ date('n') > 6 ? 'selected' : '' }}>{{ date('Y') }}-2</option>
                            <option value="{{ date('Y')-1 }}-1">{{ date('Y')-1 }}-1</option>
                            <option value="{{ date('Y')-1 }}-2">{{ date('Y')-1 }}-2</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="formato_carga" class="form-label">Formato</label>
                        <select class="form-select" id="formato_carga" name="formato" required>
                            <option value="pdf">PDF</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Este reporte compara las horas asignadas vs. las horas efectivamente impartidas por cada docente.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-download"></i> Generar Reporte
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function mostrarModalEstatico() {
    const modal = new bootstrap.Modal(document.getElementById('modalEstatico'));
    modal.show();
}

function mostrarModalDinamico() {
    const modal = new bootstrap.Modal(document.getElementById('modalDinamico'));
    modal.show();
}

function mostrarModalCargaHoraria() {
    const modal = new bootstrap.Modal(document.getElementById('modalCargaHoraria'));
    modal.show();
}
</script>
@endsection