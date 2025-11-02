@extends('layouts.app')

@section('title', 'Error en Código QR')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-exclamation-circle"></i> Error
                    </h4>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-bug fa-4x text-danger mb-4"></i>
                    <h5>Error al Procesar QR</h5>
                    <p class="text-muted mb-4">
                        Ocurrió un error inesperado al procesar el código QR.
                    </p>
                    @if(isset($error))
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Detalles del error:</strong>
                            <p class="mt-2 mb-0">{{ $error }}</p>
                        </div>
                    @endif
                    <button type="button" class="btn btn-primary" onclick="window.close()">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection