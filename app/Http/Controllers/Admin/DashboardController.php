<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\Componente;
use App\Models\OrdenServicio;
use App\Models\Estado;
use App\Models\Sede;
use App\Models\BienNacional;
use App\Models\Vehiculo;
use App\Models\EquipoSonido;
use App\Models\RegistroEntradaSalida;
use App\Models\User;
use Illuminate\Http\Request;
use App\Traits\FiltroSedeTrait;

class DashboardController extends Controller
{
    use FiltroSedeTrait;

    public function index(Request $request)
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');

        $estados = Estado::orderBy('nombre')->get();
        $estadoId = session('filtro_estado_id');
        $sedeId = session('filtro_sede_id');
        $sedes = $estadoId ? Sede::where('estado_id', $estadoId)->orderBy('nombre_sede')->get() : collect([]);

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS DE TECNOLOGÍA
        |--------------------------------------------------------------------------
        */
        $equiposQuery = Equipo::query();
        $equiposQuery = $this->filtrarPorSede($equiposQuery);

        $totalEquipos = $equiposQuery->count();
        $equiposOperativos = (clone $equiposQuery)->where('estatus_general', 'Operativo')->count();
        $equiposMantenimiento = (clone $equiposQuery)->where('estatus_general', 'En Mantenimiento')->count();
        $equiposInoperativos = (clone $equiposQuery)->where('estatus_general', 'Inoperativo')->count();

        $componentesQuery = Componente::query();
        if ($sedeId) {
            $componentesQuery->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $componentesQuery->whereIn('sede_id', $sedeIds);
        } elseif (!$esAdmin && !$esAuditor) {
            $componentesQuery->where('sede_id', $user->sede_id);
        }

        $totalComponentes = $componentesQuery->count();
        $componentesDisponibles = (clone $componentesQuery)->where('estatus', 'Disponible')->count();
        $componentesInstalados = (clone $componentesQuery)->where('estatus', 'Instalado')->count();
        $componentesRevision = (clone $componentesQuery)->where('estatus', 'En Revisión')->count();

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS DE ÓRDENES
        |--------------------------------------------------------------------------
        */
        $ordenesQuery = OrdenServicio::query();
        $ordenesQuery = $this->filtrarOrdenesPorSede($ordenesQuery);

        $ordenesAbiertas = (clone $ordenesQuery)->whereNull('estatus_final')->count();
        $ordenesReparadas = (clone $ordenesQuery)->where('estatus_final', 'Reparado')->count();
        $ordenesEsperaRepuesto = (clone $ordenesQuery)->where('estatus_final', 'En Espera de Repuesto')->count();

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS DE BIENES
        |--------------------------------------------------------------------------
        */
        $bienesQuery = BienNacional::query();
        if ($sedeId) {
            $bienesQuery->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $bienesQuery->whereIn('sede_id', $sedeIds);
        } elseif (!$esAdmin && !$esAuditor) {
            $bienesQuery->where('sede_id', $user->sede_id);
        }
        $totalBienes = $bienesQuery->count();
        $bienesDisponibles = (clone $bienesQuery)->where('estatus', 'Disponible')->count();

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS DE VEHÍCULOS
        |--------------------------------------------------------------------------
        */
        $vehiculosQuery = Vehiculo::query();
        if ($sedeId) {
            $vehiculosQuery->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $vehiculosQuery->whereIn('sede_id', $sedeIds);
        } elseif (!$esAdmin && !$esAuditor) {
            $vehiculosQuery->where('sede_id', $user->sede_id);
        }
        $totalVehiculos = $vehiculosQuery->count();
        $vehiculosDisponibles = (clone $vehiculosQuery)->where('estatus', 'Disponible')->count();

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS DE SONIDO
        |--------------------------------------------------------------------------
        */
        $sonidoQuery = EquipoSonido::query();
        if ($sedeId) {
            $sonidoQuery->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $sonidoQuery->whereIn('sede_id', $sedeIds);
        } elseif (!$esAdmin && !$esAuditor) {
            $sonidoQuery->where('sede_id', $user->sede_id);
        }
        $totalSonido = $sonidoQuery->count();
        $sonidoDisponible = (clone $sonidoQuery)->where('estatus', 'Disponible')->count();

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS DE MOVIMIENTOS
        |--------------------------------------------------------------------------
        */
        $movimientosQuery = RegistroEntradaSalida::query();
        if ($sedeId) {
            $movimientosQuery->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $movimientosQuery->whereIn('sede_id', $sedeIds);
        } elseif (!$esAdmin && !$esAuditor) {
            $movimientosQuery->where('sede_id', $user->sede_id);
        }
        $totalSalidas = (clone $movimientosQuery)->where('tipo', 'Salida')->count();
        $totalEntradas = (clone $movimientosQuery)->where('tipo', 'Entrada')->count();

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS GENERALES
        |--------------------------------------------------------------------------
        */
        $totalUsuarios = User::count();
        $totalEstados = Estado::count();
        $totalSedes = Sede::count();

        return view('admin.dashboard', compact(
            'totalEquipos', 'equiposOperativos', 'equiposMantenimiento', 'equiposInoperativos',
            'totalComponentes', 'componentesDisponibles', 'componentesInstalados', 'componentesRevision',
            'ordenesAbiertas', 'ordenesReparadas', 'ordenesEsperaRepuesto',
            'totalBienes', 'bienesDisponibles',
            'totalVehiculos', 'vehiculosDisponibles',
            'totalSonido', 'sonidoDisponible',
            'totalSalidas', 'totalEntradas',
            'totalUsuarios', 'totalEstados', 'totalSedes',
            'estados', 'sedes', 'estadoId', 'sedeId', 'esAdmin', 'esAuditor'
        ));
    }

    public function getSedesPorEstado($estadoId)
    {
        $sedes = Sede::where('estado_id', $estadoId)->orderBy('nombre_sede')->get();
        return response()->json($sedes);
    }
}