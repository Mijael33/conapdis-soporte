@extends('layouts.admin')
@section('title', 'Editar Salida')
@section('page-title', 'Editar Salida')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex align-items-center">
                <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: #dbeafe;">
                    <i class="bi bi-pencil-square" style="font-size: 1.2rem; color: #003097;"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">Editar Salida</h5>
                    <small class="text-muted">Comprobante: {{ $registro->numero_comprobante }}</small>
                </div>
            </div>
        </div>

        <div class="card-body p-4">

            {{-- ============================================
                 ALERTA INFORMATIVA
            ============================================ --}}
            <div class="alert rounded-3 mb-4" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                <div class="d-flex">
                    <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                    <div>
                        <strong>Información importante:</strong>
                        <div class="small">
                            Solo se puede editar la información de la <strong>SALIDA</strong>. Los datos del bien 
                            y la fecha/hora no se pueden modificar.
                            <br>
                            <strong>Esta edición quedará registrada en la bitácora del sistema.</strong>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ============================================
                 INFO DEL BIEN (SOLO LECTURA)
            ============================================ --}}
            <div class="rounded-3 mb-4 p-3" style="background: #f8fafc; border: 1px solid #e2e8f0;">
                <div class="row g-2 small">
                    <div class="col-md-6">
                        <strong>Bien:</strong> {{ $registro->bien_codigo }} - {{ $registro->bien_descripcion }}
                    </div>
                    <div class="col-md-6">
                        <strong>Tipo:</strong> {{ $registro->bien_tipo }}
                    </div>
                    <div class="col-md-6">
                        <strong>Fecha de Salida:</strong> 
                        {{ \App\Helpers\FechaHelper::formatear($registro->fecha_hora_salida) }}
                    </div>
                    <div class="col-md-6">
                        <strong>Registrado por:</strong> 
                        {{ $registro->salidaUsuario->name ?? 'N/A' }}
                    </div>
                </div>
            </div>

            {{-- ============================================
                 FORMULARIO DE EDICIÓN
            ============================================ --}}
            <form action="{{ route('admin.entrada-salida.update', $registro) }}" method="POST">
                @csrf @method('PUT')

                {{-- ============================================
                     PASO 1: SEDE
                ============================================ --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge rounded-pill me-2" style="background: #003097;">1</span>
                        <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">Sede del Movimiento</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Sede *</label>
                            @if(auth()->user()->hasRole('Administrador') || auth()->user()->hasRole('Auditor'))
                                <select name="sede_id" class="form-select rounded-3" required>
                                    @foreach($sedes as $sede)
                                        <option value="{{ $sede->id }}" 
                                            {{ old('sede_id', $registro->sede_id) == $sede->id ? 'selected' : '' }}>
                                            {{ $sede->nombre_sede }} ({{ $sede->estado->nombre }})
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input type="hidden" name="sede_id" value="{{ auth()->user()->sede_id }}">
                                <input type="text" class="form-control rounded-3" value="{{ auth()->user()->sede->nombre_sede ?? 'Sin sede asignada' }}" readonly>
                                <small class="text-muted">Sede asignada automáticamente</small>
                            @endif
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- ============================================
                     PASO 2: AUTORIZACIÓN
                ============================================ --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge rounded-pill me-2" style="background: #003097;">2</span>
                        <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">Datos de Autorización</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Nombre de quien autoriza *</label>
                            <input type="text" name="salida_autoriza_nombre" class="form-control rounded-3" 
                                   value="{{ old('salida_autoriza_nombre', $registro->salida_autoriza_nombre) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Cédula *</label>
                            <input type="text" name="salida_autoriza_cedula" class="form-control rounded-3" 
                                   value="{{ old('salida_autoriza_cedula', $registro->salida_autoriza_cedula) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cargo *</label>
                            <input type="text" name="salida_autoriza_cargo" class="form-control rounded-3" 
                                   value="{{ old('salida_autoriza_cargo', $registro->salida_autoriza_cargo) }}" required>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- ============================================
                     PASO 3: QUIEN RETIRA
                ============================================ --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge rounded-pill me-2" style="background: #003097;">3</span>
                        <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">Persona que Retira el Bien</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Nombre completo *</label>
                            <input type="text" name="salida_retira_nombre" class="form-control rounded-3" 
                                   value="{{ old('salida_retira_nombre', $registro->salida_retira_nombre) }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Cédula *</label>
                            <input type="text" name="salida_retira_cedula" class="form-control rounded-3" 
                                   value="{{ old('salida_retira_cedula', $registro->salida_retira_cedula) }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cargo *</label>
                            <input type="text" name="salida_retira_cargo" class="form-control rounded-3" 
                                   value="{{ old('salida_retira_cargo', $registro->salida_retira_cargo) }}" required>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- ============================================
                     PASO 4: DETALLES DEL MOVIMIENTO
                ============================================ --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge rounded-pill me-2" style="background: #003097;">4</span>
                        <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">Detalles del Movimiento</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Motivo del retiro *</label>
                            <textarea name="salida_motivo" class="form-control rounded-3" rows="3" required>{{ old('salida_motivo', $registro->salida_motivo) }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Destino *</label>
                            <textarea name="salida_destino" class="form-control rounded-3" rows="3" required>{{ old('salida_destino', $registro->salida_destino) }}</textarea>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado del bien al salir *</label>
                            <select name="salida_estado_bien" class="form-select rounded-3" required>
                                <option value="Operativo" {{ old('salida_estado_bien', $registro->salida_estado_bien) == 'Operativo' ? 'selected' : '' }}>Operativo</option>
                                <option value="Con Daños" {{ old('salida_estado_bien', $registro->salida_estado_bien) == 'Con Daños' ? 'selected' : '' }}>Con Daños</option>
                                <option value="Incompleto" {{ old('salida_estado_bien', $registro->salida_estado_bien) == 'Incompleto' ? 'selected' : '' }}>Incompleto</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Observaciones adicionales</label>
                            <textarea name="salida_observaciones" class="form-control rounded-3" rows="3">{{ old('salida_observaciones', $registro->salida_observaciones) }}</textarea>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- ============================================
                     PASO 5: SEGURIDAD (SALIDA)
                ============================================ --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge rounded-pill me-2" style="background: #003097;">5</span>
                        <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">Funcionario de Seguridad - Salida</h6>
                    </div>

                    <div class="alert rounded-3" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                        <i class="bi bi-shield-fill-check"></i>
                        <small>Funcionario de seguridad que autorizó la salida del bien.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre del funcionario *</label>
                            <input type="text" name="salida_seguridad_nombre" class="form-control rounded-3" 
                                   value="{{ old('salida_seguridad_nombre', $registro->salida_seguridad_nombre) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cédula *</label>
                            <input type="text" name="salida_seguridad_cedula" class="form-control rounded-3" 
                                   value="{{ old('salida_seguridad_cedula', $registro->salida_seguridad_cedula) }}" required>
                        </div>
                    </div>
                </div>

                {{-- Fecha/hora original --}}
                <div class="alert rounded-3" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock-history me-2 fs-5"></i>
                        <div>
                            <strong>Fecha y hora de la salida original</strong>
                            <div class="small">
                                {{ \App\Helpers\FechaHelper::formatear($registro->fecha_hora_salida) }}
                                (no se puede modificar por auditoría)
                            </div>
                        </div>
                    </div>
                </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn-conapdis px-4">
                        <i class="bi bi-check-lg"></i> Actualizar Salida
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