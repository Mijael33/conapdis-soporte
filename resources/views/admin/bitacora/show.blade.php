@extends('layouts.admin')
@section('title', 'Detalle Bitácora')
@section('page-title', 'Detalle del Registro')

@section('content')
<div class="container-fluid">

    <div class="row g-4">
        {{-- Información Principal --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">
                        <i class="bi bi-journal-text me-2"></i>Registro #{{ $bitacora->id }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    <table class="table">
                        <tr>
                            <th class="w-25">Fecha y Hora</th>
                            <td>
                                <strong>{{ \App\Helpers\FechaHelper::formatear($bitacora->fecha_registro) }}</strong>
                                <br><small class="text-muted">Zona horaria: {{ $bitacora->zona_horaria }}</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Módulo</th>
                            <td>
                                <span class="badge rounded-pill" 
                                      style="background: {{ $bitacora->color_modulo }}20; color: {{ $bitacora->color_modulo }}; border: 1px solid {{ $bitacora->color_modulo }}40;">
                                    {{ ucfirst($bitacora->modulo) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Acción</th>
                            <td>
                                <span class="badge rounded-pill bg-{{ $bitacora->color_accion }}">
                                    {{ ucfirst(str_replace('_', ' ', $bitacora->accion)) }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <th>Descripción</th>
                            <td>{{ $bitacora->descripcion }}</td>
                        </tr>
                        @if($bitacora->modelo_codigo)
                        <tr>
                            <th>Código Afectado</th>
                            <td>
                                <span class="badge rounded-pill" style="background: #e0e7ff; color: #4f46e5;">
                                    {{ $bitacora->modelo_codigo }}
                                </span>
                            </td>
                        </tr>
                        @endif
                        @if($bitacora->modelo_tipo)
                        <tr>
                            <th>Modelo</th>
                            <td>
                                <code>{{ $bitacora->modelo_tipo }}</code>
                                @if($bitacora->modelo_id)
                                    <br><small class="text-muted">ID: {{ $bitacora->modelo_id }}</small>
                                @endif
                            </td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Comparación de Datos --}}
            @if($bitacora->datos_anteriores || $bitacora->datos_nuevos)
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">
                        <i class="bi bi-arrow-left-right me-2"></i>Comparación de Datos
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        {{-- Datos Anteriores --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2" style="color: #dc2626;">
                                <i class="bi bi-dash-circle me-1"></i>Antes
                            </h6>
                            <div class="rounded-3 p-3" style="background: #fef2f2; border: 1px solid #fecaca; max-height: 400px; overflow-y: auto;">
                                @if($bitacora->datos_anteriores)
                                    <pre style="font-size: 0.75rem; margin: 0; white-space: pre-wrap; word-break: break-all;">{{ json_encode($bitacora->datos_anteriores, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    <p class="text-muted small mb-0">Sin datos anteriores</p>
                                @endif
                            </div>
                        </div>

                        {{-- Datos Nuevos --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-2" style="color: #16a34a;">
                                <i class="bi bi-plus-circle me-1"></i>Después
                            </h6>
                            <div class="rounded-3 p-3" style="background: #f0fdf4; border: 1px solid #bbf7d0; max-height: 400px; overflow-y: auto;">
                                @if($bitacora->datos_nuevos)
                                    <pre style="font-size: 0.75rem; margin: 0; white-space: pre-wrap; word-break: break-all;">{{ json_encode($bitacora->datos_nuevos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                                @else
                                    <p class="text-muted small mb-0">Sin datos nuevos</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($bitacora->datos_extra)
                    <div class="mt-3">
                        <h6 class="fw-bold mb-2" style="color: #003097;">
                            <i class="bi bi-info-circle me-1"></i>Datos Adicionales
                        </h6>
                        <div class="rounded-3 p-3" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                            <pre style="font-size: 0.75rem; margin: 0; white-space: pre-wrap; word-break: break-all;">{{ json_encode($bitacora->datos_extra, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        {{-- Panel Lateral --}}
        <div class="col-md-4">
            {{-- Info del Usuario --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">
                        <i class="bi bi-person-circle me-2"></i>Usuario
                    </h6>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-3">
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center" 
                             style="width: 60px; height: 60px; background: #003097; color: white; font-size: 1.5rem; font-weight: 700;">
                            {{ strtoupper(substr($bitacora->usuario_nombre_snapshot ?? 'S', 0, 1)) }}
                        </div>
                    </div>
                    <table class="table table-sm">
                        <tr>
                            <th>Nombre</th>
                            <td>{{ $bitacora->usuario_nombre_snapshot ?? 'Sistema' }}</td>
                        </tr>
                        @if($bitacora->usuario)
                        <tr>
                            <th>Email</th>
                            <td><small>{{ $bitacora->usuario->email }}</small></td>
                        </tr>
                        @endif
                        @if($bitacora->ip_address)
                        <tr>
                            <th>IP</th>
                            <td><code>{{ $bitacora->ip_address }}</code></td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            {{-- Info del User Agent --}}
            @if($bitacora->user_agent)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">
                        <i class="bi bi-browser-chrome me-2"></i>Navegador
                    </h6>
                </div>
                <div class="card-body p-3">
                    <small class="text-muted" style="word-break: break-all;">
                        {{ $bitacora->user_agent }}
                    </small>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="mt-3">
        <a href="{{ route('admin.bitacora.index') }}" class="btn-outline-conapdis">
            <i class="bi bi-arrow-left"></i> Volver
        </a>
    </div>
</div>
@endsection