@extends('layouts.dashboard')

@section('title', 'CU-13: Gestión de Días No Laborables')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <h3 class="card-title mb-2 mb-md-0">
                        <i class="fas fa-calendar-times me-2"></i>
                        <span class="d-none d-sm-inline">Gestión de Días No Laborables/Feriados</span>
                        <span class="d-sm-none">Feriados</span>
                    </h3>
                    <a href="{{ route('admin.feriados.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        <span class="d-none d-sm-inline">Registrar Nuevo Feriado</span>
                        <span class="d-sm-none">Nuevo</span>
                    </a>
                </div>
            </div>

                <div class="card-body">
                    <!-- Filtros de búsqueda -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-12 col-sm-6 col-lg-4">
                                <label for="buscar" class="form-label">Buscar:</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="buscar" 
                                       name="buscar" 
                                       value="{{ request('buscar') }}" 
                                       placeholder="Descripción o tipo...">
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="tipo" class="form-label">Tipo:</label>
                                <select class="form-select" id="tipo" name="tipo">
                                    <option value="">Todos los tipos</option>
                                    <option value="feriado" {{ request('tipo') == 'feriado' ? 'selected' : '' }}>Feriado</option>
                                    <option value="receso" {{ request('tipo') == 'receso' ? 'selected' : '' }}>Receso</option>
                                    <option value="asueto" {{ request('tipo') == 'asueto' ? 'selected' : '' }}>Asueto</option>
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-3">
                                <label for="año" class="form-label">Año:</label>
                                <select class="form-select" id="año" name="año">
                                    <option value="">Todos los años</option>
                                    @for($i = date('Y') - 2; $i <= date('Y') + 2; $i++)
                                        <option value="{{ $i }}" {{ request('año') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </div>
                            <div class="col-12 col-sm-6 col-lg-2">
                                <label class="form-label d-none d-lg-block">&nbsp;</label>
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="fas fa-search me-1"></i>
                                    Filtrar
                                </button>
                            </div>
                        </div>
                    </form>

                    <!-- Estadísticas -->
                    @if(isset($estadisticas))
                    <div class="row g-3 mb-4">
                        <div class="col-6 col-lg-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info"><i class="fas fa-calendar-check"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Activos</span>
                                    <span class="info-box-number">{{ $estadisticas['total'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-calendar-alt"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Este Año</span>
                                    <span class="info-box-number">{{ $estadisticas['este_año'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-calendar-plus"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Próximos</span>
                                    <span class="info-box-number">{{ $estadisticas['proximos'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-lg-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger"><i class="fas fa-chart-pie"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Por Tipo</span>
                                    <span class="info-box-number small">
                                        @if(isset($estadisticas['por_tipo']))
                                            @foreach($estadisticas['por_tipo'] as $tipo => $count)
                                                {{ ucfirst($tipo) }}: {{ $count }}
                                                @if(!$loop->last)<br>@endif
                                            @endforeach
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Tabla de feriados -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th class="d-none d-md-table-cell">Fecha(s)</th>
                                    <th>Feriado</th>
                                    <th class="d-none d-lg-table-cell">Tipo</th>
                                    <th class="d-none d-sm-table-cell">Estado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($feriados as $feriado)
                                <tr>
                                    <td class="d-none d-md-table-cell">
                                        <strong>{{ $feriado->rango_fechas }}</strong>
                                        <br>
                                        <small class="text-muted">
                                            @if($feriado->fecha_fin)
                                                Rango de fechas
                                            @else
                                                Día específico
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <div class="d-md-none">
                                            <strong>{{ $feriado->rango_fechas }}</strong>
                                            <br>
                                        </div>
                                        <strong>{{ $feriado->descripcion }}</strong>
                                        <div class="d-lg-none mt-1">
                                            <span class="badge bg-{{ $feriado->tipo == 'feriado' ? 'primary' : ($feriado->tipo == 'receso' ? 'success' : 'warning') }}">
                                                {{ $feriado->tipo_formateado }}
                                            </span>
                                        </div>
                                        <div class="d-sm-none mt-1">
                                            @if($feriado->activo)
                                                <span class="badge bg-success">Activo</span>
                                            @else
                                                <span class="badge bg-secondary">Inactivo</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="d-none d-lg-table-cell">
                                        <span class="badge bg-{{ $feriado->tipo == 'feriado' ? 'primary' : ($feriado->tipo == 'receso' ? 'success' : 'warning') }}">
                                            {{ $feriado->tipo_formateado }}
                                        </span>
                                    </td>
                                    <td class="d-none d-sm-table-cell">
                                        @if($feriado->activo)
                                            <span class="badge bg-success">Activo</span>
                                        @else
                                            <span class="badge bg-secondary">Inactivo</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group-responsive">
                                            <a href="{{ route('admin.feriados.show', $feriado) }}" 
                                               class="btn btn-info btn-sm" 
                                               title="Ver detalles">
                                                <i class="fas fa-eye"></i>
                                                <span class="d-none d-xl-inline ms-1">Ver</span>
                                            </a>
                                            <a href="{{ route('admin.feriados.edit', $feriado) }}" 
                                               class="btn btn-warning btn-sm" 
                                               title="Editar">
                                                <i class="fas fa-edit"></i>
                                                <span class="d-none d-xl-inline ms-1">Editar</span>
                                            </a>
                                            @if($feriado->activo)
                                            <form method="POST" 
                                                  action="{{ route('admin.feriados.destroy', $feriado) }}" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('¿Está seguro de desactivar este feriado?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-danger btn-sm" 
                                                        title="Desactivar">
                                                    <i class="fas fa-trash"></i>
                                                    <span class="d-none d-xl-inline ms-1">Eliminar</span>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">No hay feriados registrados</h5>
                                            <p class="text-muted">Comience registrando el primer día no laborable.</p>
                                            <a href="{{ route('admin.feriados.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus mr-1"></i>
                                                Registrar Primer Feriado
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginación -->
                    @if($feriados->hasPages())
                    <div class="d-flex justify-content-center">
                        {{ $feriados->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const tipoSelect = document.getElementById('tipo');
    const añoSelect = document.getElementById('año');
    
    if (tipoSelect) {
        tipoSelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }
    
    if (añoSelect) {
        añoSelect.addEventListener('change', function() {
            this.closest('form').submit();
        });
    }
    
    // Responsive table enhancements
    const table = document.querySelector('.table-responsive table');
    if (table && window.innerWidth < 768) {
        // Add mobile-friendly styling
        table.classList.add('table-sm');
    }
});
</script>
@endsection