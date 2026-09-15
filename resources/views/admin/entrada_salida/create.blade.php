@extends('layouts.admin')
@section('title', 'Nuevo Registro E/S')
@section('page-title', 'Nuevo Registro de Entrada/Salida')
@section('content')
<div class="card">
    <div class="card-header bg-white py-3"><h5 class="mb-0 fw-bold">Registrar Movimiento de Bien</h5></div>
    <div class="card-body">
        <form action="{{ route('admin.entrada-salida.store') }}" method="POST">
            @csrf
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tipo de Movimiento</label>
                    <select name="tipo" id="tipo" class="form-select" required>
                        <option value="">Seleccione</option>
                        <option value="Salida" {{ old('tipo')=='Salida' ? 'selected' : '' }}>Salida</option>
                        <option value="Entrada" {{ old('tipo')=='Entrada' ? 'selected' : '' }}>Entrada</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Tipo de Bien</label>
                    <select name="bien_tipo" id="bien_tipo" class="form-select" required>
                        <option value="">Seleccione</option>
                        <option value="Tecnologia" {{ old('bien_tipo')=='Tecnologia' ? 'selected' : '' }}>Tecnología</option>
                        <option value="BienNacional" {{ old('bien_tipo')=='BienNacional' ? 'selected' : '' }}>Bien Nacional</option>
                        <option value="Vehiculo" {{ old('bien_tipo')=='Vehiculo' ? 'selected' : '' }}>Vehículo</option>
                        <option value="Sonido" {{ old('bien_tipo')=='Sonido' ? 'selected' : '' }}>Sonido</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Bien</label>
                    <select name="bien_id" id="bien_id" class="form-select" required>
                        <option value="">Seleccione tipo de bien primero</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Sede</label>
                    <select name="sede_id" class="form-select" required>
                        <option value="">Seleccione</option>
                        @foreach($sedes as $sede)
                            <option value="{{ $sede->id }}" {{ old('sede_id')==$sede->id ? 'selected' : '' }}>{{ $sede->nombre_sede }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="campos-salida" style="display:none;">
                <h6 class="fw-bold mt-2">Datos de Salida</h6>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Fecha Hora Salida</label>
                        <input type="datetime-local" name="fecha_hora_salida" class="form-control" value="{{ old('fecha_hora_salida') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Autorizado por</label>
                        <input type="text" name="autorizado_por_nombre" class="form-control" value="{{ old('autorizado_por_nombre') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">C.I. Autorizado</label>
                        <input type="text" name="autorizado_por_cedula" class="form-control" value="{{ old('autorizado_por_cedula') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Cargo Autorizado</label>
                        <input type="text" name="autorizado_por_cargo" class="form-control" value="{{ old('autorizado_por_cargo') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Retirado por</label>
                        <input type="text" name="persona_retira_nombre" class="form-control" value="{{ old('persona_retira_nombre') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">C.I. Retira</label>
                        <input type="text" name="persona_retira_cedula" class="form-control" value="{{ old('persona_retira_cedula') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Cargo Retira</label>
                        <input type="text" name="persona_retira_cargo" class="form-control" value="{{ old('persona_retira_cargo') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Seguridad Salida</label>
                        <input type="text" name="seguridad_salida_nombre" class="form-control" value="{{ old('seguridad_salida_nombre') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">C.I. Seguridad</label>
                        <input type="text" name="seguridad_salida_cedula" class="form-control" value="{{ old('seguridad_salida_cedula') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Estado Salida</label>
                        <select name="estado_salida" class="form-select">
                            <option value="">Seleccione</option>
                            <option value="Operativo">Operativo</option>
                            <option value="Con Daños">Con Daños</option>
                            <option value="Incompleto">Incompleto</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Motivo</label>
                        <input type="text" name="motivo" class="form-control" value="{{ old('motivo') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Destino</label>
                    <input type="text" name="destino" class="form-control" value="{{ old('destino') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">Observaciones Salida</label>
                    <textarea name="observaciones_salida" class="form-control" rows="2">{{ old('observaciones_salida') }}</textarea>
                </div>
            </div>

            <div id="campos-entrada" style="display:none;">
                <h6 class="fw-bold mt-2">Datos de Entrada</h6>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Fecha Hora Entrada</label>
                        <input type="datetime-local" name="fecha_hora_entrada" class="form-control" value="{{ old('fecha_hora_entrada') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Seguridad Entrada</label>
                        <input type="text" name="seguridad_entrada_nombre" class="form-control" value="{{ old('seguridad_entrada_nombre') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">C.I. Seguridad</label>
                        <input type="text" name="seguridad_entrada_cedula" class="form-control" value="{{ old('seguridad_entrada_cedula') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">Estado Entrada</label>
                        <select name="estado_entrada" class="form-select">
                            <option value="">Seleccione</option>
                            <option value="Operativo">Operativo</option>
                            <option value="Con Daños">Con Daños</option>
                            <option value="Incompleto">Incompleto</option>
                            <option value="No Retornó">No Retornó</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Observaciones Entrada</label>
                    <textarea name="observaciones_entrada" class="form-control" rows="2">{{ old('observaciones_entrada') }}</textarea>
                </div>
            </div>

            <button type="submit" class="btn-conapdis">Guardar</button>
            <a href="{{ route('admin.entrada-salida.index') }}" class="btn-outline-conapdis">Cancelar</a>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('tipo').addEventListener('change', function() {
    const salida = document.getElementById('campos-salida');
    const entrada = document.getElementById('campos-entrada');
    if (this.value === 'Salida') {
        salida.style.display = 'block';
        entrada.style.display = 'none';
    } else if (this.value === 'Entrada') {
        salida.style.display = 'none';
        entrada.style.display = 'block';
    } else {
        salida.style.display = 'none';
        entrada.style.display = 'none';
    }
});

document.getElementById('bien_tipo').addEventListener('change', function() {
    const tipo = this.value;
    const selectBien = document.getElementById('bien_id');
    selectBien.innerHTML = '<option value="">Cargando...</option>';

    if (tipo === 'Tecnologia') {
        selectBien.innerHTML = '<option value="">Seleccione un equipo</option>';
        @foreach($equipos as $eq)
        selectBien.innerHTML += '<option value="{{ $eq->id }}">{{ $eq->codigo_inventario_institucional }} - {{ $eq->marca }} {{ $eq->modelo }}</option>';
        @endforeach
    } else if (tipo === 'BienNacional') {
        selectBien.innerHTML = '<option value="">Seleccione un bien</option>';
        @foreach($bienes as $b)
        selectBien.innerHTML += '<option value="{{ $b->id }}">{{ $b->codigo_inventario }} - {{ Str::limit($b->descripcion, 40) }}</option>';
        @endforeach
    } else if (tipo === 'Vehiculo') {
        selectBien.innerHTML = '<option value="">Seleccione un vehículo</option>';
        @foreach($vehiculos as $v)
        selectBien.innerHTML += '<option value="{{ $v->id }}">{{ $v->placa }} - {{ $v->marca }} {{ $v->modelo }}</option>';
        @endforeach
    } else if (tipo === 'Sonido') {
        selectBien.innerHTML = '<option value="">Seleccione un equipo de sonido</option>';
        @foreach($sonido as $s)
        selectBien.innerHTML += '<option value="{{ $s->id }}">{{ $s->serial }} - {{ $s->marca }} {{ $s->modelo }}</option>';
        @endforeach
    }
});
</script>
@endpush
@endsection