@props([
    'rutaDescarga' => null,
])

@if(session('importacion_resumen'))
@php $resumen = session('importacion_resumen'); @endphp

<div id="modalImportOverlay">
    <div class="modal-import-content">

        {{-- HEADER --}}
        <div class="p-3 text-white {{ $resumen['tiene_errores'] ? 'bg-warning' : 'bg-success' }}" style="border-radius: 16px 16px 0 0;">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="bi bi-{{ $resumen['tiene_errores'] ? 'exclamation-triangle-fill' : 'check-circle-fill' }} me-2"></i>
                    Resultado de Importación
                </h5>
                <button type="button" class="btn-close btn-close-white" onclick="cerrarModalImportacion()"></button>
            </div>
        </div>

        {{-- BODY --}}
        <div class="p-4">

            <div class="text-center mb-4">
                <h6 class="fw-bold text-secondary mb-1">MÓDULO</h6>
                <h4 class="fw-bold" style="color: #001e5c;">{{ strtoupper($resumen['modulo']) }}</h4>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #003097 !important;">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-collection-fill mb-2" style="font-size: 1.5rem; color: #003097;"></i>
                            <h3 class="fw-bold mb-0" style="color: #003097;">{{ $resumen['total_procesados'] }}</h3>
                            <small class="text-muted">Total procesados</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #4c7f36 !important;">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-check-circle-fill mb-2" style="font-size: 1.5rem; color: #4c7f36;"></i>
                            <h3 class="fw-bold mb-0" style="color: #4c7f36;">{{ $resumen['importados'] }}</h3>
                            <small class="text-muted">Importados</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-3 h-100" style="border-left: 4px solid #ef172f !important;">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-x-circle-fill mb-2" style="font-size: 1.5rem; color: #ef172f;"></i>
                            <h3 class="fw-bold mb-0" style="color: #ef172f;">{{ $resumen['fallidos'] }}</h3>
                            <small class="text-muted">Con errores</small>
                        </div>
                    </div>
                </div>
            </div>

            @if($resumen['tiene_errores'])
            <div class="alert alert-warning rounded-3 border-0 mb-0">
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-info-circle-fill fs-5"></i>
                    <div>
                        <strong>Algunos registros no se pudieron importar.</strong>
                        <p class="mb-0 small">Descarga el archivo con el detalle completo de errores para revisarlos y corregirlos.</p>
                    </div>
                </div>
            </div>
            @else
            <div class="alert alert-success rounded-3 border-0 mb-0 text-center">
                <i class="bi bi-emoji-smile-fill fs-4 d-block mb-1"></i>
                <strong>¡Todos los registros fueron importados exitosamente!</strong>
            </div>
            @endif

        </div>

        {{-- FOOTER --}}
        <div class="p-3 bg-light d-flex justify-content-between" style="border-radius: 0 0 16px 16px;">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4" onclick="cerrarModalImportacion()">
                <i class="bi bi-x-lg"></i> Cerrar
            </button>

            @if($resumen['tiene_errores'] && $resumen['archivo_errores'] && $rutaDescarga)
            <a href="{{ route($rutaDescarga, $resumen['archivo_errores']) }}" class="btn btn-conapdis rounded-pill px-4">
                <i class="bi bi-download"></i> Descargar Errores (TXT)
            </a>
            @endif
        </div>

    </div>
</div>

<script>
    function cerrarModalImportacion() {
        var overlay = document.getElementById('modalImportOverlay');
        if (overlay) {
            overlay.classList.remove('mostrar');
        }
    }

    (function() {
        function mostrarModal() {
            var overlay = document.getElementById('modalImportOverlay');
            if (overlay) {
                overlay.classList.add('mostrar');
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', mostrarModal);
        } else {
            mostrarModal();
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                cerrarModalImportacion();
            }
        });
    })();
</script>
@endif