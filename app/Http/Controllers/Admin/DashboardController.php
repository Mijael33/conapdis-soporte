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
use Spatie\Permission\Models\Role;
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
        | HELPER LOCAL PARA APLICAR FILTRO DE SEDE/ESTADO
        |--------------------------------------------------------------------------
        */
        $aplicarFiltroSede = function ($query) use ($sedeId, $estadoId, $esAdmin, $esAuditor, $user) {
            if ($sedeId) {
                $query->where('sede_id', $sedeId);
            } elseif ($estadoId) {
                $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
                $query->whereIn('sede_id', $sedeIds);
            } elseif (!$esAdmin && !$esAuditor) {
                $query->where('sede_id', $user->sede_id);
            }
            return $query;
        };

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

        $componentesQuery = $aplicarFiltroSede(Componente::query());
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
        | MÉTRICAS DE BIENES NACIONALES
        |--------------------------------------------------------------------------
        */
        $bienesQuery = $aplicarFiltroSede(BienNacional::query());
        $totalBienes = $bienesQuery->count();
        $bienesDisponibles = (clone $bienesQuery)->where('estatus', 'Disponible')->count();

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS DE VEHÍCULOS
        |--------------------------------------------------------------------------
        */
        $vehiculosQuery = $aplicarFiltroSede(Vehiculo::query());
        $totalVehiculos = $vehiculosQuery->count();
        $vehiculosDisponibles = (clone $vehiculosQuery)->where('estatus', 'Disponible')->count();

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS DE EQUIPOS DE SONIDO
        |--------------------------------------------------------------------------
        */
        $sonidoQuery = $aplicarFiltroSede(EquipoSonido::query());
        $totalSonido = $sonidoQuery->count();
        $sonidoDisponible = (clone $sonidoQuery)->where('estatus', 'Disponible')->count();

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS DE ENTRADA/SALIDA
        |--------------------------------------------------------------------------
        */
        $movimientosQuery = $aplicarFiltroSede(RegistroEntradaSalida::query());
        $totalSalidas = (clone $movimientosQuery)->count();
        $salidasPendientes = (clone $movimientosQuery)->where('estatus', 'Pendiente')->count();
        $totalEntradas = (clone $movimientosQuery)->where('estatus', 'Completado')->count();

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS DE VALORIZACIÓN (NUEVO)
        |--------------------------------------------------------------------------
        | Suma de valor_prudencial y valor_adquisicion por módulo.
        | Usa COALESCE para no romper si algún valor es NULL.
        */
        $valorEquiposPrudencial = (clone $equiposQuery)->sum('valor_prudencial') ?? 0;
        $valorEquiposAdquisicion = (clone $equiposQuery)->sum('valor_adquisicion') ?? 0;

        $valorComponentesPrudencial = (clone $componentesQuery)->sum('valor_prudencial') ?? 0;
        $valorComponentesAdquisicion = (clone $componentesQuery)->sum('valor_adquisicion') ?? 0;

        $valorBienesPrudencial = (clone $bienesQuery)->sum('valor_prudencial') ?? 0;
        $valorBienesAdquisicion = (clone $bienesQuery)->sum('valor_adquisicion') ?? 0;

        $valorVehiculosPrudencial = (clone $vehiculosQuery)->sum('valor_prudencial') ?? 0;
        $valorVehiculosAdquisicion = (clone $vehiculosQuery)->sum('valor_adquisicion') ?? 0;

        $valorSonidoPrudencial = (clone $sonidoQuery)->sum('valor_prudencial') ?? 0;
        $valorSonidoAdquisicion = (clone $sonidoQuery)->sum('valor_adquisicion') ?? 0;

        $totalValorPrudencial = $valorEquiposPrudencial + $valorComponentesPrudencial
            + $valorBienesPrudencial + $valorVehiculosPrudencial + $valorSonidoPrudencial;

        $totalValorAdquisicion = $valorEquiposAdquisicion + $valorComponentesAdquisicion
            + $valorBienesAdquisicion + $valorVehiculosAdquisicion + $valorSonidoAdquisicion;

        /*
        |--------------------------------------------------------------------------
        | MÉTRICAS GENERALES
        |--------------------------------------------------------------------------
        */
        $totalUsuarios = User::count();
        $totalEstados = Estado::count();
        $totalSedes = Sede::count();
        $totalRoles = Role::count();
        $totalCategoriasBienes = \App\Models\CategoriaBien::count();
        $totalCategoriasVehiculos = \App\Models\CategoriaVehiculo::count();
        $totalCategoriasSonido = \App\Models\CategoriaSonido::count();

        return view('admin.dashboard', compact(
            'totalEquipos', 'equiposOperativos', 'equiposMantenimiento', 'equiposInoperativos',
            'totalComponentes', 'componentesDisponibles', 'componentesInstalados', 'componentesRevision',
            'ordenesAbiertas', 'ordenesReparadas', 'ordenesEsperaRepuesto',
            'totalBienes', 'bienesDisponibles',
            'totalVehiculos', 'vehiculosDisponibles',
            'totalSonido', 'sonidoDisponible',
            'totalSalidas', 'salidasPendientes', 'totalEntradas',
            'totalUsuarios', 'totalEstados', 'totalSedes', 'totalRoles',
            'totalCategoriasBienes', 'totalCategoriasVehiculos', 'totalCategoriasSonido',
            'estados', 'sedes', 'estadoId', 'sedeId', 'esAdmin', 'esAuditor',
            // Valorización
            'valorEquiposPrudencial', 'valorEquiposAdquisicion',
            'valorComponentesPrudencial', 'valorComponentesAdquisicion',
            'valorBienesPrudencial', 'valorBienesAdquisicion',
            'valorVehiculosPrudencial', 'valorVehiculosAdquisicion',
            'valorSonidoPrudencial', 'valorSonidoAdquisicion',
            'totalValorPrudencial', 'totalValorAdquisicion'
        ));
    }

    public function getSedesPorEstado($estadoId)
    {
        $sedes = Sede::where('estado_id', $estadoId)->orderBy('nombre_sede')->get();
        return response()->json($sedes);
    }
}