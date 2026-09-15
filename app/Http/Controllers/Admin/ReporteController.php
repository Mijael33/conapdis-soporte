<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\OrdenServicio;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteController extends Controller
{
    /**
     * Generar PDF con ficha técnica del equipo.
     * Incluye desglose de hardware, estatus y recuadros para firmas.
     */
    public function fichaTecnica(Equipo $equipo)
    {
        $equipo->load([
            'tipoEquipo',
            'departamento.sede.estado',
            'componentes.categoria',
            'ordenesServicio' => function ($query) {
                $query->orderBy('created_at', 'desc')->limit(5);
            }
        ]);

        $pdf = Pdf::loadView('admin.reportes.ficha-tecnica', compact('equipo'));
        $pdf->setPaper('letter');

        return $pdf->download('Ficha-Tecnica-' . $equipo->codigo_inventario_institucional . '.pdf');
    }

    /**
     * Generar PDF de orden de servicio.
     */
    public function ordenServicio(OrdenServicio $ordene)
    {
        $ordene->load(['equipo.tipoEquipo', 'equipo.departamento.sede.estado', 'tecnico']);

        $pdf = Pdf::loadView('admin.reportes.orden-servicio', compact('ordene'));
        $pdf->setPaper('letter');

        return $pdf->download('Orden-Servicio-' . $ordene->codigo_ticket . '.pdf');
    }
}