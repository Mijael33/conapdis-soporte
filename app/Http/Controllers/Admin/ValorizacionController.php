<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\Componente;
use App\Models\BienNacional;
use App\Models\Vehiculo;
use App\Models\EquipoSonido;
use App\Models\Sede;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ValorizacionExport;

class ValorizacionController extends Controller
{
    /**
     * Aplica el filtro de sede/estado según el rol del usuario.
     */
    private function aplicarFiltro($query, $user, $esAdmin, $esAuditor, $sedeId, $estadoId)
    {
        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }
        return $query;
    }

    /**
     * Recolecta los datos de valorización por módulo.
     */
    private function recolectarDatos(Request $request)
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $desde = $request->fecha_desde;
        $hasta = $request->fecha_hasta;
        $incluirComponentes = $request->boolean('incluir_componentes', true);

        /*
        |--------------------------------------------------------------------------
        | EQUIPOS
        |--------------------------------------------------------------------------
        */
        $equiposQuery = Equipo::query();
        $equiposQuery = $this->aplicarFiltro($equiposQuery, $user, $esAdmin, $esAuditor, $sedeId, $estadoId);

        if ($desde) $equiposQuery->whereDate('created_at', '>=', $desde);
        if ($hasta) $equiposQuery->whereDate('created_at', '<=', $hasta);

        $equiposData = [
            'items' => (clone $equiposQuery)->with(['tipoEquipo', 'sede.estado', 'componentes'])->orderBy('codigo_inventario_institucional')->get(),
            'cantidad' => $equiposQuery->count(),
            'suma_prudencial' => (clone $equiposQuery)->sum('valor_prudencial') ?? 0,
            'suma_adquisicion' => (clone $equiposQuery)->sum('valor_adquisicion') ?? 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | COMPONENTES
        |--------------------------------------------------------------------------
        */
        $componentesQuery = Componente::query();
        $componentesQuery = $this->aplicarFiltro($componentesQuery, $user, $esAdmin, $esAuditor, $sedeId, $estadoId);

        if ($desde) $componentesQuery->whereDate('created_at', '>=', $desde);
        if ($hasta) $componentesQuery->whereDate('created_at', '<=', $hasta);

        $componentesData = [
            'items' => (clone $componentesQuery)->with(['categoria', 'sede.estado'])->orderBy('serial_unico')->get(),
            'cantidad' => $componentesQuery->count(),
            'suma_prudencial' => (clone $componentesQuery)->sum('valor_prudencial') ?? 0,
            'suma_adquisicion' => (clone $componentesQuery)->sum('valor_adquisicion') ?? 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | BIENES NACIONALES
        |--------------------------------------------------------------------------
        */
        $bienesQuery = BienNacional::query();
        $bienesQuery = $this->aplicarFiltro($bienesQuery, $user, $esAdmin, $esAuditor, $sedeId, $estadoId);

        if ($desde) $bienesQuery->whereDate('fecha_adquisicion', '>=', $desde);
        if ($hasta) $bienesQuery->whereDate('fecha_adquisicion', '<=', $hasta);

        $bienesData = [
            'items' => (clone $bienesQuery)->with(['categoria', 'sede.estado'])->orderBy('codigo_inventario')->get(),
            'cantidad' => $bienesQuery->count(),
            'suma_prudencial' => (clone $bienesQuery)->sum('valor_prudencial') ?? 0,
            'suma_adquisicion' => (clone $bienesQuery)->sum('valor_adquisicion') ?? 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | VEHÍCULOS
        |--------------------------------------------------------------------------
        */
        $vehiculosQuery = Vehiculo::query();
        $vehiculosQuery = $this->aplicarFiltro($vehiculosQuery, $user, $esAdmin, $esAuditor, $sedeId, $estadoId);

        if ($desde) $vehiculosQuery->whereDate('created_at', '>=', $desde);
        if ($hasta) $vehiculosQuery->whereDate('created_at', '<=', $hasta);

        $vehiculosData = [
            'items' => (clone $vehiculosQuery)->with(['categoria', 'sede.estado'])->orderBy('placa')->get(),
            'cantidad' => $vehiculosQuery->count(),
            'suma_prudencial' => (clone $vehiculosQuery)->sum('valor_prudencial') ?? 0,
            'suma_adquisicion' => (clone $vehiculosQuery)->sum('valor_adquisicion') ?? 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | EQUIPOS DE SONIDO
        |--------------------------------------------------------------------------
        */
        $sonidoQuery = EquipoSonido::query();
        $sonidoQuery = $this->aplicarFiltro($sonidoQuery, $user, $esAdmin, $esAuditor, $sedeId, $estadoId);

        if ($desde) $sonidoQuery->whereDate('created_at', '>=', $desde);
        if ($hasta) $sonidoQuery->whereDate('created_at', '<=', $hasta);

        $sonidoData = [
            'items' => (clone $sonidoQuery)->with(['categoria', 'sede.estado'])->orderBy('serial')->get(),
            'cantidad' => $sonidoQuery->count(),
            'suma_prudencial' => (clone $sonidoQuery)->sum('valor_prudencial') ?? 0,
            'suma_adquisicion' => (clone $sonidoQuery)->sum('valor_adquisicion') ?? 0,
        ];

        /*
        |--------------------------------------------------------------------------
        | TOTALES
        |--------------------------------------------------------------------------
        */
        $totales = [
            'cantidad' => $equiposData['cantidad'] + $componentesData['cantidad']
                + $bienesData['cantidad'] + $vehiculosData['cantidad'] + $sonidoData['cantidad'],
            'suma_prudencial' => $equiposData['suma_prudencial'] + $componentesData['suma_prudencial']
                + $bienesData['suma_prudencial'] + $vehiculosData['suma_prudencial'] + $sonidoData['suma_prudencial'],
            'suma_adquisicion' => $equiposData['suma_adquisicion'] + $componentesData['suma_adquisicion']
                + $bienesData['suma_adquisicion'] + $vehiculosData['suma_adquisicion'] + $sonidoData['suma_adquisicion'],
        ];

        return [
            'equipos' => $equiposData,
            'componentes' => $componentesData,
            'bienes' => $bienesData,
            'vehiculos' => $vehiculosData,
            'sonido' => $sonidoData,
            'totales' => $totales,
            'filtros' => [
                'fecha_desde' => $desde,
                'fecha_hasta' => $hasta,
                'incluir_componentes' => $incluirComponentes,
            ],
        ];
    }

    public function index(Request $request)
    {
        $data = $this->recolectarDatos($request);
        return view('admin.valorizacion.index', $data);
    }

    public function pdf(Request $request)
    {
        $data = $this->recolectarDatos($request);

        $pdf = Pdf::loadView('admin.valorizacion.pdf', $data);
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        return $pdf->download('Valorizacion-Inventario-' . date('Y-m-d') . '.pdf');
    }

    public function excel(Request $request)
    {
        $data = $this->recolectarDatos($request);
        return Excel::download(new ValorizacionExport($data), 'valorizacion-inventario-' . date('Y-m-d') . '.xlsx');
    }
}