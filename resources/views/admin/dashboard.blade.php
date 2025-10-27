@extends('layouts.dashboard')

@section('title', 'Dashboard Administrador')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard Administrador</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary">Exportar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Facultades
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['facultades'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-building fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Carreras
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['carreras'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-graduation-cap fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Docentes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['profesores'] }}</div>
                            <div class="text-xs text-muted">
                                <i class="fas fa-check-circle text-success"></i> {{ $stats['profesores_activos'] }} Activos
                                <i class="fas fa-ban text-danger ms-2"></i> {{ $stats['profesores_inactivos'] }} Inactivos
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chalkboard-teacher fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Estudiantes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['estudiantes'] }}</div>
                            <div class="text-xs text-muted">
                                <i class="fas fa-user-check text-success"></i> {{ $stats['estudiantes_activos'] }} Activos
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-graduate fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Segunda fila de estadísticas -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Materias
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['materias'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-book fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                                Aulas
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['aulas'] }}</div>
                            <div class="text-xs text-muted">
                                <i class="fas fa-door-open text-success"></i> {{ $stats['aulas_disponibles'] }} Disponibles
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-door-closed fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Inscripciones
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['inscripciones'] }}</div>
                            <div class="text-xs text-muted">
                                <i class="fas fa-clipboard-check text-success"></i> {{ $stats['inscripciones_activas'] }} Activas
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Administradores
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['administradores'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-shield fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Últimos Docentes Registrados</h6>
                </div>
                <div class="card-body">
                    @if($ultimosDocentes->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($ultimosDocentes as $docente)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $docente->nombre_completo }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $docente->codigo_docente }} - {{ $docente->especialidad }}</small>
                                </div>
                                <span class="badge bg-{{ $docente->estado == 'activo' ? 'success' : 'danger' }}">
                                    {{ ucfirst($docente->estado) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.docentes.index') }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-eye"></i> Ver Todos los Docentes
                            </a>
                        </div>
                    @else
                        <p class="text-muted">No hay docentes registrados.</p>
                        <a href="{{ route('admin.docentes.create') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus"></i> Registrar Primer Docente
                        </a>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Últimos Estudiantes Registrados</h6>
                </div>
                <div class="card-body">
                    @if($ultimosEstudiantes->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($ultimosEstudiantes as $estudiante)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>{{ $estudiante->nombre_completo }}</strong>
                                    <br>
                                    <small class="text-muted">{{ $estudiante->codigo_estudiante }} - {{ $estudiante->carrera->nombre ?? 'Sin carrera' }}</small>
                                </div>
                                <span class="badge bg-{{ $estudiante->estado == 'activo' ? 'success' : 'secondary' }}">
                                    {{ ucfirst($estudiante->estado) }}
                                </span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted">No hay estudiantes registrados.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Acciones Rápidas</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.docentes.create') }}" class="btn btn-success btn-block w-100">
                                <i class="fas fa-user-plus"></i> Registrar Docente
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.docentes.index') }}" class="btn btn-primary btn-block w-100">
                                <i class="fas fa-chalkboard-teacher"></i> Gestionar Docentes
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.facultades.index') }}" class="btn btn-info btn-block w-100">
                                <i class="fas fa-building"></i> Gestionar Facultades
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.aulas.index') }}" class="btn btn-warning btn-block w-100">
                                <i class="fas fa-door-closed"></i> Gestionar Aulas
                            </a>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.carreras.index') }}" class="btn btn-success btn-block w-100">
                                <i class="fas fa-graduation-cap"></i> Gestionar Carreras
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.materias.index') }}" class="btn btn-primary btn-block w-100">
                                <i class="fas fa-book"></i> Gestionar Materias
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.estudiantes.index') }}" class="btn btn-info btn-block w-100">
                                <i class="fas fa-user-graduate"></i> Gestionar Estudiantes
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="{{ route('admin.horarios.index') }}" class="btn btn-warning btn-block w-100">
                                <i class="fas fa-calendar-alt"></i> Gestionar Horarios
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection