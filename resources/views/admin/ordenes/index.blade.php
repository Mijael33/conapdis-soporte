@extends('layouts.admin')
@section('title', 'Órdenes de Servicio')
@section('page-title', 'Órdenes de Servicio')
@section('content')
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
        <h5 class="mb-0 fw-bold">Tickets de Soporte</h5>
        <a href="{{ route('admin.ordenes.create') }}" class="btn-conapdis"><i class="bi bi-plus-lg"></i> Nueva Orden</a>
    </div>
    <div class="card-body">
        <form method="GET" class="row g-2 mb-3">
            <div class="col-md-3">
                <select name="estatus" class="form-select">
                    <option value="">Todos</option>
                    <option value="abiertas" {{ request('estatus')=='abiertas' ? 'selected' : '' }}>Abiertas</option>
                    <option value="Reparado" {{ request('estatus')=='Reparado' ? 'selected' : '' }}>Reparado</option>
                    <option value="En Espera de Repuesto" {{ request('estatus')=='En Espera de Repuesto' ? 'selected' : '' }}>En Espera de Repuesto</option>
                    <option value="Irrecuperable" {{ request('estatus')=='Irrecuperable' ? 'selected' : '' }}>Irrecuperable</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn-conapdis"><i class="bi bi-search"></i> Filtrar</button>
            </div>
        </form>
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-header">
                    <tr><th>Ticket</th><th>Equipo</th><th>Técnico</th><th>Problema</th><th>Estatus</th><th>Acciones</th></tr>
                </thead>
                <tbody>
                    @foreach($ordenes as $orden)
                    <tr>
                        <td class="fw-semibold">{{ $orden->codigo_ticket }}</td>
                        <td>{{ $orden->equipo->codigo_inventario_institucional }}</td>
                        <td>{{ $orden->tecnico->name }}</td>
                        <td>{{ Str::limit($orden->problema_reportado_usuario, 40) }}</td>
                        <td>
                            @if(!$orden->estatus_final)<span class="badge badge-revision">Abierta</span>
                            @elseif($orden->estatus_final=='Reparado')<span class="badge badge-operativo">Reparado</span>
                            @elseif($orden->estatus_final=='En Espera de Repuesto')<span class="badge badge-mantenimiento">En Espera</span>
                            @else<span class="badge badge-inoperativo">{{ $orden->estatus_final }}</span>@endif
                        </td>
                        <td>
                            <a href="{{ route('admin.ordenes.show', $orden) }}" class="btn btn-sm btn-outline-conapdis me-1"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.ordenes.edit', $orden) }}" class="btn btn-sm btn-outline-conapdis"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $ordenes->links() }}
    </div>
</div>
@endsection