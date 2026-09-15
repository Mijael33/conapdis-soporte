<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Equipo;
use App\Models\TipoEquipo;
use App\Models\Departamento;
use App\Models\Componente;
use App\Models\EquipoSistemaOperativo;
use App\Models\BitacoraEquipo;
use App\Models\BitacoraComponente;
use App\Models\BitacoraAccesoContrasena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use App\Traits\FiltroSedeTrait;
use App\Services\ImportacionService;
use App\Imports\EquiposImport;
use App\Exports\EquiposExport;
use App\Exports\PlantillaEquiposExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class EquipoController extends Controller
{
    use FiltroSedeTrait;

    public function index(Request $request)
    {
        $query = Equipo::with(['tipoEquipo', 'departamento.sede.estado']);
        $query = $this->filtrarPorSede($query);

        if ($request->estatus) {
            $query->where('estatus_general', $request->estatus);
        }
        if ($request->departamento_id) {
            $query->where('departamento_id', $request->departamento_id);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_inventario_institucional', 'ILIKE', "%{$search}%")
                  ->orWhere('serial_chasis', 'ILIKE', "%{$search}%")
                  ->orWhere('marca', 'ILIKE', "%{$search}%")
                  ->orWhere('modelo', 'ILIKE', "%{$search}%");
            });
        }

        $equipos = $query->orderBy('codigo_inventario_institucional')->paginate(15);
        $departamentos = Departamento::with('sede')->orderBy('nombre_departamento')->get();

        return view('admin.equipos.index', compact('equipos', 'departamentos'));
    }

    public function show($id)
    {
        $equipo = Equipo::with([
            'tipoEquipo',
            'departamento.sede.estado',
            'componentes.categoria',
            'sistemasOperativos',
            'ordenesServicio.tecnico',
            'bitacoras.usuario'
        ])->findOrFail($id);
        return view('admin.equipos.show', compact('equipo'));
    }

    public function create()
    {
        $tiposEquipos = TipoEquipo::orderBy('nombre')->get();
        $departamentos = Departamento::with('sede.estado')->orderBy('nombre_departamento')->get();
        return view('admin.equipos.create', compact('tiposEquipos', 'departamentos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_inventario_institucional' => 'required|string|max:50|unique:equipos',
            'serial_chasis' => 'nullable|string|max:150',
            'tipo_equipo_id' => 'required|exists:tipos_equipos,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'usuario_asignado_nombre' => 'nullable|string|max:150',
            'usuario_asignado_cedula' => 'nullable|string|max:20',
            'usuario_asignado_cargo' => 'nullable|string|max:150',
            'so_nombre' => 'nullable|array',
            'so_nombre.*' => 'nullable|string|max:100',
            'so_arquitectura' => 'nullable|array',
            'so_arquitectura.*' => 'nullable|string|max:20',
            'so_password' => 'nullable|array',
            'so_password.*' => 'nullable|string|max:255',
            'so_notas' => 'nullable|array',
            'so_notas.*' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $equipo = Equipo::create($validated);

            if ($request->has('so_nombre') && is_array($request->so_nombre)) {
                foreach ($request->so_nombre as $index => $nombre) {
                    if (!empty($nombre)) {
                        EquipoSistemaOperativo::create([
                            'equipo_id' => $equipo->id,
                            'nombre' => $nombre,
                            'arquitectura' => $request->so_arquitectura[$index] ?? null,
                            'password_encriptada' => !empty($request->so_password[$index]) ? Crypt::encryptString($request->so_password[$index]) : null,
                            'tiene_contrasena' => !empty($request->so_password[$index]),
                            'notas' => $request->so_notas[$index] ?? null,
                        ]);
                    }
                }
            }

            BitacoraEquipo::create([
                'equipo_id' => $equipo->id,
                'usuario_id' => auth()->id(),
                'accion' => 'crear',
                'descripcion_detallada' => 'Equipo creado: ' . $equipo->codigo_inventario_institucional,
                'datos_nuevos' => $equipo->toArray(),
                'fecha_registro' => now(),
            ]);

            DB::commit();
            return redirect()->route('admin.equipos.index')->with('success', 'Equipo creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $equipo = Equipo::with('sistemasOperativos')->findOrFail($id);
        $tiposEquipos = TipoEquipo::orderBy('nombre')->get();
        $departamentos = Departamento::with('sede.estado')->orderBy('nombre_departamento')->get();
        return view('admin.equipos.edit', compact('equipo', 'tiposEquipos', 'departamentos'));
    }

    public function update(Request $request, $id)
    {
        $equipo = Equipo::findOrFail($id);

        $validated = $request->validate([
            'codigo_inventario_institucional' => 'required|string|max:50|unique:equipos,codigo_inventario_institucional,' . $equipo->id,
            'serial_chasis' => 'nullable|string|max:150',
            'tipo_equipo_id' => 'required|exists:tipos_equipos,id',
            'departamento_id' => 'required|exists:departamentos,id',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'estatus_general' => 'required|in:Operativo,En Mantenimiento,Inoperativo,Donado/Desincorporado',
            'usuario_asignado_nombre' => 'nullable|string|max:150',
            'usuario_asignado_cedula' => 'nullable|string|max:20',
            'usuario_asignado_cargo' => 'nullable|string|max:150',
            'so_nombre' => 'nullable|array',
            'so_nombre.*' => 'nullable|string|max:100',
            'so_arquitectura' => 'nullable|array',
            'so_arquitectura.*' => 'nullable|string|max:20',
            'so_password' => 'nullable|array',
            'so_password.*' => 'nullable|string|max:255',
            'so_notas' => 'nullable|array',
            'so_notas.*' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $datosAnteriores = $equipo->toArray();
            $equipo->update($validated);

            $equipo->sistemasOperativos()->delete();
            if ($request->has('so_nombre') && is_array($request->so_nombre)) {
                foreach ($request->so_nombre as $index => $nombre) {
                    if (!empty($nombre)) {
                        EquipoSistemaOperativo::create([
                            'equipo_id' => $equipo->id,
                            'nombre' => $nombre,
                            'arquitectura' => $request->so_arquitectura[$index] ?? null,
                            'password_encriptada' => !empty($request->so_password[$index]) ? Crypt::encryptString($request->so_password[$index]) : null,
                            'tiene_contrasena' => !empty($request->so_password[$index]),
                            'notas' => $request->so_notas[$index] ?? null,
                        ]);
                    }
                }
            }

            BitacoraEquipo::create([
                'equipo_id' => $equipo->id,
                'usuario_id' => auth()->id(),
                'accion' => 'actualizar',
                'descripcion_detallada' => 'Equipo actualizado: ' . $equipo->codigo_inventario_institucional,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => $equipo->fresh()->toArray(),
                'fecha_registro' => now(),
            ]);

            DB::commit();
            return redirect()->route('admin.equipos.index')->with('success', 'Equipo actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $equipo = Equipo::findOrFail($id);

        if ($equipo->componentes()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el equipo porque tiene componentes instalados.');
        }
        if ($equipo->ordenesServicio()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el equipo porque tiene órdenes de servicio asociadas.');
        }

        DB::beginTransaction();
        try {
            BitacoraEquipo::create([
                'equipo_id' => $equipo->id,
                'usuario_id' => auth()->id(),
                'accion' => 'eliminado',
                'descripcion_detallada' => "Equipo {$equipo->codigo_inventario_institucional} eliminado",
                'datos_anteriores' => $equipo->toArray(),
                'fecha_registro' => now(),
            ]);

            $equipo->delete();

            DB::commit();
            return redirect()->route('admin.equipos.index')->with('success', 'Equipo eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }

    public function verPassword($soId)
    {
        $so = EquipoSistemaOperativo::with('equipo')->findOrFail($soId);

        BitacoraAccesoContrasena::create([
            'equipo_so_id' => $so->id,
            'usuario_id' => auth()->id(),
            'motivo' => 'Consulta de contraseña del SO: ' . $so->nombre . ' del equipo ' . ($so->equipo->codigo_inventario_institucional ?? 'N/A'),
            'ip_address' => request()->ip(),
            'fecha_acceso' => now(),
        ]);

        $password = $so->password_encriptada ? Crypt::decryptString($so->password_encriptada) : null;

        return response()->json([
            'so' => $so->nombre,
            'password' => $password,
            'tiene_contrasena' => $so->tiene_contrasena,
        ]);
    }

    public function asignarComponente($id)
    {
        $equipo = Equipo::findOrFail($id);
        $componentesDisponibles = Componente::where('estatus', 'Disponible')
            ->where('sede_id', $equipo->departamento->sede_id)
            ->orderBy('marca')->get();
        return view('admin.equipos.asignar-componente', compact('equipo', 'componentesDisponibles'));
    }

    public function storeComponente(Request $request, $id)
    {
        $equipo = Equipo::findOrFail($id);
        $request->validate(['componente_id' => 'required|exists:componentes,id']);
        $componente = Componente::findOrFail($request->componente_id);

        if ($componente->estatus !== 'Disponible') {
            return back()->with('error', 'Componente no disponible.');
        }

        DB::beginTransaction();
        try {
            $equipo->componentes()->attach($componente->id, ['fecha_instalacion' => now(), 'activo' => true]);
            $componente->update(['estatus' => 'Instalado']);

            BitacoraEquipo::create([
                'equipo_id' => $equipo->id,
                'usuario_id' => auth()->id(),
                'accion' => 'instalar_componente',
                'descripcion_detallada' => "Componente {$componente->serial_unico} instalado en {$equipo->codigo_inventario_institucional}",
                'datos_nuevos' => ['componente_id' => $componente->id, 'serial' => $componente->serial_unico],
                'fecha_registro' => now(),
            ]);

            BitacoraComponente::create([
                'componente_id' => $componente->id,
                'usuario_id' => auth()->id(),
                'accion' => 'instalado_en_equipo',
                'descripcion_detallada' => "Instalado en equipo {$equipo->codigo_inventario_institucional}",
                'datos_nuevos' => ['equipo' => $equipo->codigo_inventario_institucional],
                'fecha_registro' => now(),
            ]);

            DB::commit();
            return redirect()->route('admin.equipos.show', $equipo)->with('success', 'Componente asignado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function removerComponente($equipoId, $componenteId)
    {
        $equipo = Equipo::findOrFail($equipoId);
        $componente = Componente::findOrFail($componenteId);

        DB::beginTransaction();
        try {
            $equipo->componentes()->updateExistingPivot($componente->id, [
                'activo' => false,
                'fecha_desinstalacion' => now(),
            ]);
            $componente->update(['estatus' => 'En Revisión']);

            BitacoraEquipo::create([
                'equipo_id' => $equipo->id,
                'usuario_id' => auth()->id(),
                'accion' => 'remover_componente',
                'descripcion_detallada' => "Componente {$componente->serial_unico} removido de {$equipo->codigo_inventario_institucional}",
                'datos_anteriores' => ['componente_id' => $componente->id, 'serial' => $componente->serial_unico],
                'fecha_registro' => now(),
            ]);

            BitacoraComponente::create([
                'componente_id' => $componente->id,
                'usuario_id' => auth()->id(),
                'accion' => 'removido_de_equipo',
                'descripcion_detallada' => "Removido del equipo {$equipo->codigo_inventario_institucional}",
                'datos_anteriores' => ['equipo' => $equipo->codigo_inventario_institucional],
                'datos_nuevos' => ['estatus' => 'En Revisión'],
                'fecha_registro' => now(),
            ]);

            DB::commit();
            return redirect()->route('admin.equipos.show', $equipo)->with('success', 'Componente removido.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /*
    |--------------------------------------------------------------------------
    | IMPORTACIÓN Y EXPORTACIÓN
    |--------------------------------------------------------------------------
    */

    public function importar()
    {
        return view('admin.equipos.importar');
    }

    public function procesarImportacion(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $service = new ImportacionService('equipos');
            $import = new EquiposImport($service);

            Excel::import($import, $request->file('archivo'));

            $resumen = $service->generarResumen();

            return redirect()->route('admin.equipos.index')
                ->with('importacion_resumen', $resumen)
                ->with('success', 'Equipos importados exitosamente.');

        } catch (\Exception $e) {
            return back()->with('error', 'Error crítico en la importación: ' . $e->getMessage());
        }
    }

    public function descargarErrores($archivo)
    {
        $ruta = 'importaciones/' . $archivo;
        if (!Storage::disk('public')->exists($ruta)) {
            return back()->with('error', 'El archivo de errores no existe.');
        }
        return Storage::disk('public')->download($ruta);
    }

    public function exportarExcel()
    {
        return Excel::download(new EquiposExport, 'equipos-' . date('Y-m-d') . '.xlsx');
    }

    public function exportarPDF()
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = Equipo::with(['tipoEquipo', 'departamento.sede.estado']);

        if (!$esAdmin && !$esAuditor) {
            $query->whereHas('departamento.sede', fn($q) => $q->where('id', $user->sede_id));
        } elseif ($sedeId) {
            $query->whereHas('departamento.sede', fn($q) => $q->where('id', $sedeId));
        } elseif ($estadoId) {
            $query->whereHas('departamento.sede', fn($q) => $q->where('estado_id', $estadoId));
        }

        $equipos = $query->orderBy('codigo_inventario_institucional')->get();

        $pdf = Pdf::loadView('admin.equipos.pdf', compact('equipos'));
        $pdf->setPaper('letter', 'landscape');
        return $pdf->download('equipos-' . date('Y-m-d') . '.pdf');
    }

    public function descargarPlantilla()
    {
        return Excel::download(new PlantillaEquiposExport, 'plantilla-equipos.xlsx');
    }

    /*
    |--------------------------------------------------------------------------
    | PDF INDIVIDUAL Y PEGATINA
    |--------------------------------------------------------------------------
    */

    public function pdfIndividual($id)
    {
        $equipo = Equipo::with([
            'tipoEquipo',
            'departamento.sede.estado',
            'componentes.categoria',
            'sistemasOperativos',
            'ordenesServicio.tecnico',
        ])->findOrFail($id);

        $pdf = Pdf::loadView('admin.reportes.ficha-tecnica', compact('equipo'));
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        return $pdf->download('Ficha-Tecnica-' . $equipo->codigo_inventario_institucional . '.pdf');
    }

    public function pdfPegatina($id)
    {
        $equipo = Equipo::with([
            'tipoEquipo',
            'departamento.sede.estado'
        ])->findOrFail($id);
    
        $pdf = Pdf::loadView('admin.equipos.pdf_pegatina', compact('equipo'));
    
        $pdf->setPaper([
            0,
            0,
            90 * 2.83464567,
            45 * 2.83464567
        ]);
    
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
        ]);
    
        return $pdf->download(
            'Pegatina-' . $equipo->codigo_inventario_institucional . '.pdf'
        );
    }
}