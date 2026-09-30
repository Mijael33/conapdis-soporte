@extends('layouts.admin')
@section('title', 'Registrar Salida')
@section('page-title', 'Registrar Salida de Bien')

@section('content')
<div class="container-fluid">
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex align-items-center">
                <div class="rounded-3 d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; background: #dbeafe;">
                    <i class="bi bi-box-arrow-up-right" style="font-size: 1.2rem; color: #003097;"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold" style="color: #1a3b5d;">Registrar Salida de Bien</h5>
                    <small class="text-muted">Complete los datos del retiro del bien</small>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="{{ route('admin.entrada-salida.store') }}" method="POST" id="formSalida">
                @csrf

                {{-- ============================================
                     PASO 1: SELECCIÓN DEL BIEN
                ============================================ --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge rounded-pill me-2" style="background: #003097;">1</span>
                        <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">Selección del Bien</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tipo de Bien *</label>
                            <select name="bien_tipo" id="bien_tipo" class="form-select rounded-3" required>
                                <option value="">Seleccione el tipo</option>
                                <option value="Tecnologia" {{ old('bien_tipo') == 'Tecnologia' ? 'selected' : '' }}>Tecnología (Equipo)</option>
                                <option value="BienNacional" {{ old('bien_tipo') == 'BienNacional' ? 'selected' : '' }}>Bien Nacional</option>
                                <option value="Vehiculo" {{ old('bien_tipo') == 'Vehiculo' ? 'selected' : '' }}>Vehículo</option>
                                <option value="Sonido" {{ old('bien_tipo') == 'Sonido' ? 'selected' : '' }}>Equipo de Sonido</option>
                            </select>
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Bien a Retirar *</label>
                            <select name="bien_id" id="bien_id" class="form-select rounded-3" required>
                                <option value="">Primero seleccione el tipo</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Sede *</label>
                            @if(auth()->user()->hasRole('Administrador') || auth()->user()->hasRole('Auditor'))
                                <select name="sede_id" class="form-select rounded-3" required>
                                    <option value="">Seleccione la sede</option>
                                    @foreach($sedes as $sede)
                                        <option value="{{ $sede->id }}" {{ old('sede_id', auth()->user()->sede_id) == $sede->id ? 'selected' : '' }}>
                                            {{ $sede->nombre_sede }}
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

                    {{-- Info del bien seleccionado --}}
                    <div id="info-bien" class="alert rounded-3 mt-3 d-none" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                        <div class="d-flex">
                            <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                            <div>
                                <strong id="info-bien-titulo">Información del Bien</strong>
                                <div id="info-bien-detalle" class="small mt-1"></div>
                            </div>
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
                                   value="{{ old('salida_autoriza_nombre') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Cédula *</label>
                            <input type="text" name="salida_autoriza_cedula" class="form-control rounded-3" 
                                   value="{{ old('salida_autoriza_cedula') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cargo *</label>
                            <input type="text" name="salida_autoriza_cargo" class="form-control rounded-3" 
                                   value="{{ old('salida_autoriza_cargo') }}" required>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- ============================================
                     PASO 3: QUIEN RETIRA EL BIEN
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
                                   value="{{ old('salida_retira_nombre') }}" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Cédula *</label>
                            <input type="text" name="salida_retira_cedula" class="form-control rounded-3" 
                                   value="{{ old('salida_retira_cedula') }}" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Cargo *</label>
                            <input type="text" name="salida_retira_cargo" class="form-control rounded-3" 
                                   value="{{ old('salida_retira_cargo') }}" required>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- ============================================
                     PASO 4: DATOS DEL MOVIMIENTO
                ============================================ --}}
                <div class="mb-4">
                    <div class="d-flex align-items-center mb-3">
                        <span class="badge rounded-pill me-2" style="background: #003097;">4</span>
                        <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">Detalles del Movimiento</h6>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Motivo del retiro *</label>
                            <textarea name="salida_motivo" class="form-control rounded-3" rows="3" required
                                      placeholder="Ej: Traslado a evento institucional, reparación externa, comisión de servicio...">{{ old('salida_motivo') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Destino *</label>
                            <textarea name="salida_destino" class="form-control rounded-3" rows="3" required
                                      placeholder="Ej: Hotel Alba Caracas, Sede Regional Zulia, Taller autorizado...">{{ old('salida_destino') }}</textarea>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Estado del bien al salir *</label>
                            <select name="salida_estado_bien" class="form-select rounded-3" required>
                                <option value="">Seleccione el estado</option>
                                <option value="Operativo" {{ old('salida_estado_bien') == 'Operativo' ? 'selected' : '' }}>Operativo</option>
                                <option value="Con Daños" {{ old('salida_estado_bien') == 'Con Daños' ? 'selected' : '' }}>Con Daños</option>
                                <option value="Incompleto" {{ old('salida_estado_bien') == 'Incompleto' ? 'selected' : '' }}>Incompleto</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Observaciones adicionales</label>
                            <textarea name="salida_observaciones" class="form-control rounded-3" rows="3"
                                      placeholder="Notas adicionales (opcional)">{{ old('salida_observaciones') }}</textarea>
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
                        <h6 class="mb-0 fw-bold" style="color: #1a3b5d;">Funcionario de Seguridad que Autoriza la Salida</h6>
                    </div>

                    <div class="alert rounded-3" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                        <i class="bi bi-shield-fill-check"></i>
                        <small>Este es el funcionario de seguridad que está en la puerta cuando el bien SALE.</small>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nombre del funcionario *</label>
                            <input type="text" name="salida_seguridad_nombre" class="form-control rounded-3" 
                                   value="{{ old('salida_seguridad_nombre') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Cédula *</label>
                            <input type="text" name="salida_seguridad_cedula" class="form-control rounded-3" 
                                   value="{{ old('salida_seguridad_cedula') }}" required>
                        </div>
                    </div>
                </div>

                {{-- Fecha/hora automática --}}
                <div class="alert rounded-3" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock-history me-2 fs-5"></i>
                        <div>
                            <strong>Fecha y hora se registran automáticamente del servidor</strong>
                            <div class="small">Se registrará: {{ now()->format('d/m/Y h:i A') }} (hora del servidor)</div>
                        </div>
                    </div>
                </div>

                {{-- BOTONES --}}
                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn-conapdis px-4">
                        <i class="bi bi-check-lg"></i> Registrar Salida
                    </button>
                    <a href="{{ route('admin.entrada-salida.index') }}" class="btn-outline-conapdis px-4">
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// ============================================
// CARGAR BIENES SEGÚN TIPO
// ============================================
const bienes = {
    Tecnologia: [
        @foreach($equipos as $e)
        { id: {{ $e->id }}, codigo: '{{ $e->codigo_inventario_institucional }}', descripcion: '{{$e->marca }} {{ $e->modelo }}', extra: 'Serial: {{$e->serial_chasis ?? "N/A" }}' },
        @endforeach
    ],
    BienNacional: [
        @foreach($bienes as $b)
        { id: {{ $b->id }}, codigo: '{{$b->codigo_inventario }}', descripcion: '{{ Str::limit($b->descripcion, 50) }}', extra: 'Serial: {{$b->serial ?? "N/A" }}' },
        @endforeach
    ],
    Vehiculo: [
        @foreach($vehiculos as $v)
        { id: {{ $v->id }}, codigo: '{{ $v->placa }}', descripcion: '{{$v->marca }} {{ $v->modelo }}', extra: 'Año: {{ $v->anio ?? "N/A" }}' },
        @endforeach
    ],
    Sonido: [
        @foreach($sonido as $s)
        { id: {{ $s->id }}, codigo: '{{ $s->serial }}', descripcion: '{{$s->marca }} {{ $s->modelo }}', extra: 'Código: {{ $s->codigo_inventario }}' },
        @endforeach
    ]
};

document.getElementById('bien_tipo').addEventListener('change', function() {
    const tipo = this.value;
    const selectBien = document.getElementById('bien_id');
    const infoBien = document.getElementById('info-bien');

    infoBien.classList.add('d-none');

    if (!tipo || !bienes[tipo]) {
        selectBien.innerHTML = '<option value="">Primero seleccione el tipo</option>';
        return;
    }

    selectBien.innerHTML = '<option value="">Seleccione el bien</option>';
    bienes[tipo].forEach(b => {
        selectBien.innerHTML += `<option value="${b.id}">${b.codigo} - ${b.descripcion}</option>`;
    });
});

document.getElementById('bien_id').addEventListener('change', function() {
    const tipo = document.getElementById('bien_tipo').value;
    const id = parseInt(this.value);
    const infoBien = document.getElementById('info-bien');

    if (!id || !bienes[tipo]) {
        infoBien.classList.add('d-none');
        return;
    }

    const bien = bienes[tipo].find(b => b.id === id);
    if (bien) {
        document.getElementById('info-bien-titulo').textContent = bien.codigo;
        document.getElementById('info-bien-detalle').innerHTML = `${bien.descripcion}<br>${bien.extra}`;
        infoBien.classList.remove('d-none');
    }
});
</script>
@endpush
@endsection
