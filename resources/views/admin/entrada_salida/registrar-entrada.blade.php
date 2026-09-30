@extends('layouts.admin')
@section('title', 'Registrar Entrada')
@section('page-title', 'Registrar Entrada de Bien')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex align-items-center">
                <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: #dcfce7;">
                    <i class="bi bi-box-arrow-in-down-left" style="font-size: 1.2rem; color: #16a34a;"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">Registrar Entrada de Bien</h5>
                    <small class="text-muted">Comprobante: {{ $registro->numero_comprobante }}</small>
                </div>
            </div>
        </div>

        <div class="card-body p-4">

            {{-- ============================================
                 DATOS DE LA SALIDA (SOLO LECTURA)
            ============================================ --}}
            <div class="rounded-3 mb-4 p-4" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <h6 class="fw-bold mb-3" style="color: #003097;">
                    <i class="bi bi-info-circle me-2"></i>Información de la Salida Original
                </h6>

                <div class="row g-3 small">
                    <div class="col-md-6">
                        <strong>Bien:</strong><br>
                        {{ $registro->bien_codigo }} - {{ $registro->bien_descripcion }}
                    </div>
                    <div class="col-md-6">
                        <strong>Sede:</strong><br>
                        {{ $registro->sede->nombre_sede ?? 'N/A' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha de Salida:</strong><br>
                        {{ \App\Helpers\FechaHelper::formatear($registro->fecha_hora_salida) }}
                    </div>
                    <div class="col-md-6">
                        <strong>Retirado por:</strong><br>
                        {{ $registro->salida_retira_nombre }} - C.I. {{ $registro->salida_retira_cedula }}
                    </div>
                    <div class="col-md-6">
                        <strong>Motivo:</strong><br>
                        {{ $registro->salida_motivo }}
                    </div>
                    <div class="col-md-6">
                        <strong>Destino:</strong><br>
                        {{ $registro->salida_destino }}
                    </div>
                    <div class="col-md-6">
                        <strong>Estado al Salir:</strong><br>
                        {{ $registro->salida_estado_bien }}
                    </div>
                    <div class="col-md-6">
                        <strong>Seguridad que autorizó salida:</strong><br>
                        {{ $registro->salida_seguridad_nombre }} - C.I. {{ $registro->salida_seguridad_cedula }}
                    </div>
                </div>
            </div>

            {{-- ============================================
                 FORMULARIO DE ENTRADA
            ============================================ --}}
            <form action="{{ route('admin.entrada-salida.store-entrada', $registro) }}" method="POST">
                @csrf

                <div class="d-flex align-items-center mb-3">
                    <span class="badge rounded-pill me-2" style="background: #16a34a;">NUEVOS DATOS</span>
                    <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">Datos de la Entrada</h6>
                </div>

                {{-- Persona que entrega --}}
                <div class="mb-4">
                    <h6 class="fw-semibold" style="color: #003097;">
                        <i class="bi bi-person-check me-2"></i>Persona que Entrega el Bien
                    </h6>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre completo *</label>
                            <input type="text" name="entrada_recibe_nombre" class="form-control rounded-3" 
                                   value="{{ old('entrada_recibe_nombre', $registro->salida_retira_nombre) }}" required>
                            <small class="text-muted">Por defecto aparece quien retiró, pero puede cambiarlo</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cédula *</label>
                            <input type="text" name="entrada_recibe_cedula" class="form-control rounded-3" 
                                   value="{{ old('entrada_recibe_cedula', $registro->salida_retira_cedula) }}" required>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Seguridad de ENTRADA --}}
                <div class="mb-4">
                    <h6 class="fw-semibold" style="color: #003097;">
                        <i class="bi bi-shield-fill-check me-2"></i>Funcionario de Seguridad que Recibe
                    </h6>
                    <div class="alert rounded-3 mt-2" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                        <i class="bi bi-info-circle"></i>
                        <small>
                            Este es el funcionario de seguridad que está en la puerta cuando el bien 
                            <strong>REGRESA</strong>. Puede ser una persona diferente a quien autorizó la salida
                            ({{ $registro->salida_seguridad_nombre }}).
                        </small>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre del funcionario *</label>
                            <input type="text" name="entrada_seguridad_nombre" class="form-control rounded-3" 
                                   value="{{ old('entrada_seguridad_nombre') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cédula *</label>
                            <input type="text" name="entrada_seguridad_cedula" class="form-control rounded-3" 
                                   value="{{ old('entrada_seguridad_cedula') }}" required>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Estado del bien al regresar --}}
                <div class="mb-4">
                    <h6 class="fw-semibold" style="color: #003097;">
                        <i class="bi bi-clipboard-check me-2"></i>Estado del Bien al Regresar
                    </h6>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado al ingresar *</label>
                            <select name="entrada_estado_bien" class="form-select rounded-3" required>
                                <option value="">Seleccione el estado</option>
                                <option value="Operativo" {{ old('entrada_estado_bien') == 'Operativo' ? 'selected' : '' }}>Operativo</option>
                                <option value="Con Daños" {{ old('entrada_estado_bien') == 'Con Daños' ? 'selected' : '' }}>Con Daños</option>
                                <option value="Incompleto" {{ old('entrada_estado_bien') == 'Incompleto' ? 'selected' : '' }}>Incompleto</option>
                                <option value="No Retornó" {{ old('entrada_estado_bien') == 'No Retornó' ? 'selected' : '' }}>No Retornó</option>
                            </select>
                            <small class="text-muted">Compare con el estado al salir: <strong>{{ $registro->salida_estado_bien }}</strong></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Observaciones de la entrada</label>
                            <textarea name="entrada_observaciones" class="form-control rounded-3" rows="3"
                                      placeholder="Observaciones sobre el estado en que regresa el bien">{{ old('entrada_observaciones') }}</textarea>
                        </div>
                    </div>
                </div>

                {{-- Fecha/hora automática --}}
                <div class="alert rounded-3" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock-history me-2 fs-5"></i>
                        <div>
                            <strong>Fecha y hora se registran automáticamente del servidor</strong>
                            <div class="small">Se registrará: {{ now()->format('d/m/Y h:i A') }}</div>
                        </div>
                    </div>
                </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn-conapdis px-4">
                        <i class="bi bi-check-lg"></i> Registrar Entrada
                    </button>
                    <a href="{{ route('admin.entrada-salida.show', $registro) }}" class="btn-outline-conapdis px-4">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection