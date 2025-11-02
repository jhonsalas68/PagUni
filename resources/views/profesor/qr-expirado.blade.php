@extends('layouts.app')

@section('title', 'Código QR Expirado')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-clock"></i> Código QR Expirado
                    </h4>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-hourglass-end fa-4x text-warning mb-4"></i>
                    <h5>Código QR Expirado</h5>
                    <p class="text-muted mb-4">
                        Este código QR ha expirado. Los códigos QR tienen una validez de 30 minutos desde su generación.
                    </p>
                    <div class="alert alert-info">
                        <i class="fas fa-lightbulb"></i>
                        <strong>¿Qué puedes hacer?</strong>
                        <p class="mt-2 mb-0">
                            Solicita al profesor que genere un nuevo código QR desde su dashboard para poder registrar tu asistencia.
                        </p>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="window.close()">
                        <i class="fas fa-times"></i> Cerrar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection