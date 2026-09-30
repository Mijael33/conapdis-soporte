@extends('layouts.admin')
@section('title', 'Detalle Movimiento')
@section('page-title', 'Detalle del Movimiento')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">
                    {{ $registro->numero_comprobante }}
                </h5>
                <small class="text-muted">{{ $registro->bien_codigo }} - {{ $registro->bien_descripcion }}</small>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                @can('entrada-salida.ver')
                <a href="{{ route('admin.entrada-salida.pdf-salida', $registro) }}" 
                   class="btn-outline-conapdis btn-sm" target="_blank">
                    <i class="bi bi-file-pdf"></i> PDF Salida
                </a>
                @if($registro->estatus === 'Completado')
                <a href="{{ route('admin.entrada-salida.pdf-entrada', $registro) }}" 
                   class="btn-outline-conapdis btn-sm" target="_blank">
                    <i class="bi bi-file-pdf"></i> PDF Entrada
                </a>
                @endif
                @endcan
            </div>
        </div>

        <div class="card-body p-4">

            {{-- ESTATUS --}}
            <div class="text-center mb-4">
                @if($registro->estatus === 'Pendiente')
                    <span class="badge rounded-pill" style="background: #fef3c7; color: #a16207; font-size: 0.9rem; padding: 8px 20px;">
                        PENDIENTE DE ENTRADA
                    </span>
                    <div class="mt-2 d-flex justify-content-center gap-2 flex-wrap">
                        @can('entrada-salida.crear')
                        <a href="{{ route('admin.entrada-salida.registrar-entrada', $registro) }}" class="btn-conapdis">
                            <i class="bi bi-box-arrow-in-down"></i> Registrar Entrada
                        </a>
                        @endcan
                        @can('entrada-salida.editar')
                        <a href="{{ route('admin.entrada-salida.edit', $registro) }}" class="btn-outline-conapdis">
                            <i class="bi bi-pencil"></i> Editar Salida
                        </a>
                        @endcan
                    </div>
                @else
                    <span class="badge rounded-pill" style="background: #dcfce7; color: #166534; font-size: 0.9rem; padding: 8px 20px;">
                        MOVIMIENTO COMPLETADO
                    </span>
                    <div class="mt-2 small text-muted">
                        Duración total: <strong>{{ $registro->duracion }}</strong>
                    </div>
                @endif
            </div>

            {{-- INFO DEL BIEN --}}
            <div class="rounded-3 mb-4 p-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <h6 class="fw-bold mb-3" style="color: #003097;">
                    <i class="bi bi-box-seam me-2"></i>Datos del Bien
                </h6>
                <div class="row g-3 small">
                    <div class="col-md-4">
                        <strong>Código:</strong><br>{{ $registro->bien_codigo }}
                    </div>
                    <div class="col-md-4">
                        <strong>Descripción:</strong><br>{{ $registro->bien_descripcion }}
                    </div>
                    <div class="col-md-4">
                        <strong>Tipo:</strong><br>{{ $registro->bien_tipo }}
                    </div>
                    <div class="col-md-12 mt-3">
                        <strong>Sede:</strong><br>
                        {{ $registro->sede->nombre_sede ?? 'N/A' }} 
                        ({{ $registro->sede->estado->nombre ?? 'N/A' }})
                    </div>
                </div>
            </div>

            {{-- SALIDA --}}
            <div class="card border-0 shadow-sm rounded-3 mb-4" style="border-left: 4px solid #003097 !important;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3" style="color: #003097;">
                        <i class="bi bi-box-arrow-up-right me-2"></i>Registro de Salida
                    </h6>

                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <strong>Fecha y hora:</strong><br>
                            {{ \App\Helpers\FechaHelper::formatear($registro->fecha_hora_salida) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Estado del bien al salir:</strong><br>
                            {{ $registro->salida_estado_bien }}
                        </div>

                        <div class="col-md-12 mt-3">
                            <strong>Autorizado por:</strong><br>
                            {{ $registro->salida_autoriza_nombre }} 
                            - C.I. {{ $registro->salida_autoriza_cedula }}
                            ({{ $registro->salida_autoriza_cargo }})
                        </div>

                        <div class="col-md-12 mt-3">
                            <strong>Retirado por:</strong><br>
                            {{ $registro->salida_retira_nombre }} 
                            - C.I. {{ $registro->salida_retira_cedula }}
                            ({{ $registro->salida_retira_cargo }})
                        </div>

                        <div class="col-md-6 mt-3">
                            <strong>Motivo:</strong><br>{{ $registro->salida_motivo }}
                        </div>
                        <div class="col-md-6 mt-3">
                            <strong>Destino:</strong><br>{{ $registro->salida_destino }}
                        </div>

                        <div class="col-md-12 mt-3">
                            <strong>Seguridad que autorizó la salida:</strong><br>
                            {{ $registro->salida_seguridad_nombre }} 
                            - C.I. {{ $registro->salida_seguridad_cedula }}
                        </div>

                        @if($registro->salida_observaciones)
                        <div class="col-md-12 mt-3">
                            <strong>Observaciones:</strong><br>{{ $registro->salida_observaciones }}
                        </div>
                        @endif

                        <div class="col-md-12 mt-3 pt-3 border-top">
                            <small class="text-muted">
                                Registrado por: <strong>{{ $registro->salidaUsuario->name ?? 'N/A' }}</strong>
                                | IP: {{ $registro->salida_ip ?? 'N/A' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ENTRADA --}}
            @if($registro->estatus === 'Completado')
            <div class="card border-0 shadow-sm rounded-3 mb-4" style="border-left: 4px solid #16a34a !important;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3" style="color: #16a34a;">
                        <i class="bi bi-box-arrow-in-down-left me-2"></i>Registro de Entrada
                    </h6>

                    <div class="row g-3 small">
                        <div class="col-md-6">
                            <strong>Fecha y hora:</strong><br>
                            {{ \App\Helpers\FechaHelper::formatear($registro->fecha_hora_entrada) }}
                        </div>
                        <div class="col-md-6">
                            <strong>Estado del bien al regresar:</strong><br>
                            {{ $registro->entrada_estado_bien }}
                        </div>

                        <div class="col-md-12 mt-3">
                            <strong>Entregado por:</strong><br>
                            {{ $registro->entrada_recibe_nombre }} 
                            - C.I. {{ $registro->entrada_recibe_cedula }}
                        </div>

                        <div class="col-md-12 mt-3">
                            <strong>Seguridad que recibió:</strong><br>
                            {{ $registro->entrada_seguridad_nombre }} 
                            - C.I. {{ $registro->entrada_seguridad_cedula }}
                        </div>

                        @if($registro->entrada_observaciones)
                        <div class="col-md-12 mt-3">
                            <strong>Observaciones:</strong><br>{{ $registro->entrada_observaciones }}
                        </div>
                        @endif

                        <div class="col-md-12 mt-3 pt-3 border-top">
                            <small class="text-muted">
                                Registrado por: <strong>{{ $registro->entradaUsuario->name ?? 'N/A' }}</strong>
                                | IP: {{ $registro->entrada_ip ?? 'N/A' }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </div>

    <div class="mt-3 d-flex gap-2">
        <a href="{{ route('admin.entrada-salida.index') }}" class="btn-outline-conapdis">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
        @if($registro->estatus === 'Pendiente')
            @can('entrada-salida.eliminar')
            <form action="{{ route('admin.entrada-salida.destroy', $registro) }}" method="POST" 
                  onsubmit="return confirm('¿Eliminar este movimiento?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn-outline-danger">
                    <i class="bi bi-trash"></i> Eliminar
                </button>
            </form>
            @endcan
        @endif
    </div>
</div>
@endsection