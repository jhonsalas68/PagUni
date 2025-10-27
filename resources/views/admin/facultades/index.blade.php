@extends('layouts.dashboard')

@section('title', 'Gestión de Facultades')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión de Facultades</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="{{ route('admin.facultades.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Nueva Facultad
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card shadow">
        <div class="card-header">
            <h6 class="m-0 font-weight-bold text-primary">Lista de Facultades</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Carreras</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($facultades as $facultad)
                        <tr>
                            <td><span class="badge bg-primary">{{ $facultad->codigo }}</span></td>
                            <td>{{ $facultad->nombre }}</td>
                            <td>{{ Str::limit($facultad->descripcion, 50) }}</td>
                            <td><span class="badge bg-info">{{ $facultad->carreras->count() }} carreras</span></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.facultades.edit', $facultad) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.facultades.destroy', $facultad) }}" 
                                          style="display: inline;" 
                                          onsubmit="return confirm('¿Eliminar esta facultad?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay facultades registradas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection