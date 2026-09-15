<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EstadoController;
use App\Http\Controllers\Admin\SedeController;
use App\Http\Controllers\Admin\DepartamentoController;
use App\Http\Controllers\Admin\TipoEquipoController;
use App\Http\Controllers\Admin\CategoriaComponenteController;
use App\Http\Controllers\Admin\ComponenteController;
use App\Http\Controllers\Admin\EquipoController;
use App\Http\Controllers\Admin\OrdenServicioController;
use App\Http\Controllers\Admin\BitacoraController;
use App\Http\Controllers\Admin\ReporteController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CategoriaBienController;
use App\Http\Controllers\Admin\BienNacionalController;
use App\Http\Controllers\Admin\CategoriaVehiculoController;
use App\Http\Controllers\Admin\VehiculoController;
use App\Http\Controllers\Admin\CategoriaSonidoController;
use App\Http\Controllers\Admin\EquipoSonidoController;
use App\Http\Controllers\Admin\EntradaSalidaController;

Route::get('/login', function () { abort(404); });
Route::get('/admin', function () { abort(404); });

Route::prefix('acceso')->name('admin.')->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/', [LoginController::class, 'login'])->name('login.submit');
    Route::post('/salir', [LoginController::class, 'logout'])->name('logout');
});

Route::prefix('panel')->name('admin.')->middleware(['auth', 'filtro.sede'])->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('api/sedes/{estado}', [DashboardController::class, 'getSedesPorEstado'])->name('api.sedes-por-estado');

    /*
    |--------------------------------------------------------------------------
    | TECNOLOGÍA
    |--------------------------------------------------------------------------
    */
    Route::resource('tipos-equipos', TipoEquipoController::class)->middleware('permission:tecnologia.tipos-equipos.ver');
    Route::resource('categorias-componentes', CategoriaComponenteController::class)->middleware('permission:tecnologia.categorias-componentes.ver');

    // EQUIPOS
    Route::get('equipos/importar', [EquipoController::class, 'importar'])->name('equipos.importar')->middleware('permission:tecnologia.equipos.importar');
    Route::post('equipos/importar', [EquipoController::class, 'procesarImportacion'])->name('equipos.procesar-importacion')->middleware('permission:tecnologia.equipos.importar');
    Route::get('equipos/descargar-errores/{archivo}', [EquipoController::class, 'descargarErrores'])->name('equipos.descargar-errores')->middleware('permission:tecnologia.equipos.importar');
    Route::get('equipos/exportar-excel', [EquipoController::class, 'exportarExcel'])->name('equipos.exportar-excel')->middleware('permission:tecnologia.equipos.exportar');
    Route::get('equipos/exportar-pdf', [EquipoController::class, 'exportarPDF'])->name('equipos.exportar-pdf')->middleware('permission:tecnologia.equipos.exportar');
    Route::get('equipos/plantilla', [EquipoController::class, 'descargarPlantilla'])->name('equipos.plantilla')->middleware('permission:tecnologia.equipos.importar');
    Route::get('equipos/{equipo}/pdf', [EquipoController::class, 'pdfIndividual'])->name('equipos.pdf')->middleware('permission:tecnologia.equipos.ver');
    Route::get('equipos/{equipo}/pegatina', [EquipoController::class, 'pdfPegatina'])->name('equipos.pegatina')->middleware('permission:tecnologia.equipos.ver');
    Route::get('equipos/{equipo}/asignar-componente', [EquipoController::class, 'asignarComponente'])->name('equipos.asignar-componente')->middleware('permission:tecnologia.equipos.editar');
    Route::post('equipos/{equipo}/componente', [EquipoController::class, 'storeComponente'])->name('equipos.store-componente')->middleware('permission:tecnologia.equipos.editar');
    Route::delete('equipos/{equipo}/componente/{componente}', [EquipoController::class, 'removerComponente'])->name('equipos.remover-componente')->middleware('permission:tecnologia.equipos.editar');
    Route::get('equipos/so/{so}/password', [EquipoController::class, 'verPassword'])->name('equipos.ver-password')->middleware('permission:tecnologia.equipos.ver');
    Route::resource('equipos', EquipoController::class)->middleware('permission:tecnologia.equipos.ver');

    // COMPONENTES
    Route::get('componentes/importar', [ComponenteController::class, 'importar'])->name('componentes.importar')->middleware('permission:tecnologia.componentes.importar');
    Route::post('componentes/importar', [ComponenteController::class, 'procesarImportacion'])->name('componentes.procesar-importacion')->middleware('permission:tecnologia.componentes.importar');
    Route::get('componentes/descargar-errores/{archivo}', [ComponenteController::class, 'descargarErrores'])->name('componentes.descargar-errores')->middleware('permission:tecnologia.componentes.importar');
    Route::get('componentes/exportar-excel', [ComponenteController::class, 'exportarExcel'])->name('componentes.exportar-excel')->middleware('permission:tecnologia.componentes.exportar');
    Route::get('componentes/exportar-pdf', [ComponenteController::class, 'exportarPDF'])->name('componentes.exportar-pdf')->middleware('permission:tecnologia.componentes.exportar');
    Route::get('componentes/plantilla', [ComponenteController::class, 'descargarPlantilla'])->name('componentes.plantilla')->middleware('permission:tecnologia.componentes.importar');
    Route::get('componentes/{componente}/pdf', [ComponenteController::class, 'pdfIndividual'])->name('componentes.pdf')->middleware('permission:tecnologia.componentes.ver');
    Route::get('componentes/{componente}/pegatina', [ComponenteController::class, 'pdfPegatina'])->name('componentes.pegatina')->middleware('permission:tecnologia.componentes.ver');
    Route::resource('componentes', ComponenteController::class)->middleware('permission:tecnologia.componentes.ver');

    // ÓRDENES
    Route::resource('ordenes', OrdenServicioController::class)->middleware('permission:tecnologia.ordenes.ver');

    /*
    |--------------------------------------------------------------------------
    | BIENES NACIONALES
    |--------------------------------------------------------------------------
    */
    Route::resource('bienes-categorias', CategoriaBienController::class)->middleware('permission:bienes.categorias.ver');

    Route::get('bienes/importar', [BienNacionalController::class, 'importar'])->name('bienes.importar')->middleware('permission:bienes.importar');
    Route::post('bienes/importar', [BienNacionalController::class, 'procesarImportacion'])->name('bienes.procesar-importacion')->middleware('permission:bienes.importar');
    Route::get('bienes/descargar-errores/{archivo}', [BienNacionalController::class, 'descargarErrores'])->name('bienes.descargar-errores')->middleware('permission:bienes.importar');
    Route::get('bienes/exportar-excel', [BienNacionalController::class, 'exportarExcel'])->name('bienes.exportar-excel')->middleware('permission:bienes.exportar');
    Route::get('bienes/exportar-pdf', [BienNacionalController::class, 'exportarPDF'])->name('bienes.exportar-pdf')->middleware('permission:bienes.exportar');
    Route::get('bienes/plantilla', [BienNacionalController::class, 'descargarPlantilla'])->name('bienes.plantilla')->middleware('permission:bienes.importar');
    Route::get('bienes/{bien}/pdf', [BienNacionalController::class, 'pdfIndividual'])->name('bienes.pdf')->middleware('permission:bienes.ver');
    Route::get('bienes/{bien}/pegatina', [BienNacionalController::class, 'pdfPegatina'])->name('bienes.pegatina')->middleware('permission:bienes.ver');
    Route::resource('bienes', BienNacionalController::class)->middleware('permission:bienes.ver');

    /*
    |--------------------------------------------------------------------------
    | VEHÍCULOS
    |--------------------------------------------------------------------------
    */
    Route::resource('vehiculos-categorias', CategoriaVehiculoController::class)->middleware('permission:vehiculos.categorias.ver');

    Route::get('vehiculos/importar', [VehiculoController::class, 'importar'])->name('vehiculos.importar')->middleware('permission:vehiculos.importar');
    Route::post('vehiculos/importar', [VehiculoController::class, 'procesarImportacion'])->name('vehiculos.procesar-importacion')->middleware('permission:vehiculos.importar');
    Route::get('vehiculos/descargar-errores/{archivo}', [VehiculoController::class, 'descargarErrores'])->name('vehiculos.descargar-errores')->middleware('permission:vehiculos.importar');
    Route::get('vehiculos/exportar-excel', [VehiculoController::class, 'exportarExcel'])->name('vehiculos.exportar-excel')->middleware('permission:vehiculos.exportar');
    Route::get('vehiculos/exportar-pdf', [VehiculoController::class, 'exportarPDF'])->name('vehiculos.exportar-pdf')->middleware('permission:vehiculos.exportar');
    Route::get('vehiculos/plantilla', [VehiculoController::class, 'descargarPlantilla'])->name('vehiculos.plantilla')->middleware('permission:vehiculos.importar');
    Route::get('vehiculos/{vehiculo}/pdf', [VehiculoController::class, 'pdfIndividual'])->name('vehiculos.pdf')->middleware('permission:vehiculos.ver');
    Route::get('vehiculos/{vehiculo}/pegatina', [VehiculoController::class, 'pdfPegatina'])->name('vehiculos.pegatina')->middleware('permission:vehiculos.ver');
    Route::resource('vehiculos', VehiculoController::class)->middleware('permission:vehiculos.ver');

    /*
    |--------------------------------------------------------------------------
    | EQUIPOS DE SONIDO
    |--------------------------------------------------------------------------
    */
    Route::resource('sonido-categorias', CategoriaSonidoController::class)->middleware('permission:sonido.categorias.ver');

    Route::get('sonido/importar', [EquipoSonidoController::class, 'importar'])->name('sonido.importar')->middleware('permission:sonido.importar');
    Route::post('sonido/importar', [EquipoSonidoController::class, 'procesarImportacion'])->name('sonido.procesar-importacion')->middleware('permission:sonido.importar');
    Route::get('sonido/descargar-errores/{archivo}', [EquipoSonidoController::class, 'descargarErrores'])->name('sonido.descargar-errores')->middleware('permission:sonido.importar');
    Route::get('sonido/exportar-excel', [EquipoSonidoController::class, 'exportarExcel'])->name('sonido.exportar-excel')->middleware('permission:sonido.exportar');
    Route::get('sonido/exportar-pdf', [EquipoSonidoController::class, 'exportarPDF'])->name('sonido.exportar-pdf')->middleware('permission:sonido.exportar');
    Route::get('sonido/plantilla', [EquipoSonidoController::class, 'descargarPlantilla'])->name('sonido.plantilla')->middleware('permission:sonido.importar');
    Route::get('sonido/{equipo}/pdf', [EquipoSonidoController::class, 'pdfIndividual'])->name('sonido.pdf')->middleware('permission:sonido.ver');
    Route::get('sonido/{equipo}/pegatina', [EquipoSonidoController::class, 'pdfPegatina'])->name('sonido.pegatina')->middleware('permission:sonido.ver');
    Route::resource('sonido', EquipoSonidoController::class)->middleware('permission:sonido.ver');

    /*
    |--------------------------------------------------------------------------
    | ENTRADA/SALIDA
    |--------------------------------------------------------------------------
    */
    Route::get('entrada-salida/exportar-excel', [EntradaSalidaController::class, 'exportarExcel'])->name('entrada-salida.exportar-excel')->middleware('permission:entrada-salida.exportar');
    Route::get('entrada-salida/pdf-listado', [EntradaSalidaController::class, 'pdfListado'])->name('entrada-salida.pdf-listado')->middleware('permission:entrada-salida.ver');
    Route::get('entrada-salida/{id}/pdf', [EntradaSalidaController::class, 'pdf'])->name('entrada-salida.pdf')->middleware('permission:entrada-salida.ver');
    Route::resource('entrada-salida', EntradaSalidaController::class)->middleware('permission:entrada-salida.ver');

    /*
    |--------------------------------------------------------------------------
    | BITÁCORA
    |--------------------------------------------------------------------------
    */
    Route::get('bitacora', [BitacoraController::class, 'index'])->name('bitacora.index')->middleware('permission:bitacora.ver');
    Route::get('bitacora/{bitacora}', [BitacoraController::class, 'show'])->name('bitacora.show')->middleware('permission:bitacora.ver');

    /*
    |--------------------------------------------------------------------------
    | REPORTES
    |--------------------------------------------------------------------------
    */
    Route::get('reportes/equipo/{equipo}', [ReporteController::class, 'fichaTecnica'])->name('reportes.ficha-tecnica')->middleware('permission:reportes.generar');
    Route::get('reportes/orden/{ordene}', [ReporteController::class, 'ordenServicio'])->name('reportes.orden-servicio')->middleware('permission:reportes.generar');

    /*
    |--------------------------------------------------------------------------
    | USUARIOS Y ROLES
    |--------------------------------------------------------------------------
    */
    Route::resource('usuarios', UsuarioController::class)->middleware('permission:usuarios.ver');
    Route::resource('roles', RoleController::class)->middleware('permission:roles.ver');
});