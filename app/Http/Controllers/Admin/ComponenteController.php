<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Componente;
use App\Models\CategoriaComponente;
use App\Models\Sede;
use App\Models\BitacoraComponente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Traits\FiltroSedeTrait;
use App\Services\ImportacionService;
use App\Imports\ComponentesImport;
use App\Exports\ComponentesExport;
use App\Exports\PlantillaComponentesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class ComponenteController extends Controller
{
    use FiltroSedeTrait;

    public function index(Request $request)
    {
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = Componente::with(['categoria', 'sede.estado', 'equipoActual']);

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        if ($request->categoria_id) {
            $query->where('categoria_componente_id', $request->categoria_id);
        }
        if ($request->estatus) {
            $query->where('estatus', $request->estatus);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('serial_unico', 'ILIKE', "%{$search}%")
                  ->orWhere('marca', 'ILIKE', "%{$search}%")
                  ->orWhere('modelo', 'ILIKE', "%{$search}%");
            });
        }

        $componentes = $query->orderBy('marca')->paginate(15);
        $categorias = CategoriaComponente::orderBy('nombre')->get();

        return view('admin.componentes.index', compact('componentes', 'categorias'));
    }

    public function show($id)
    {
        $componente = Componente::with([
            'categoria',
            'sede.estado',
            'equipoActual.departamento.sede.estado',
            'equipos.departamento.sede.estado',
            'bitacoras.usuario'
        ])->findOrFail($id);

        return view('admin.componentes.show', compact('componente'));
    }

    public function create()
    {
        $categorias = CategoriaComponente::orderBy('nombre')->get();
        $sedes = Sede::with('estado')->orderBy('nombre_sede')->get();
        return view('admin.componentes.create', compact('categorias', 'sedes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'categoria_componente_id' => 'required|exists:categorias_componentes,id',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'serial_unico' => 'required|string|max:150|unique:componentes',
            'sede_id' => 'required|exists:sedes,id',
            'caracteristicas_tecnicas' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        if ($request->filled('caracteristicas_tecnicas')) {
            $decoded = json_decode($request->caracteristicas_tecnicas, true);
            $validated['caracteristicas_tecnicas'] = is_array($decoded) ? $decoded : null;
        } else {
            $validated['caracteristicas_tecnicas'] = null;
        }

        DB::beginTransaction();
        try {
            $componente = Componente::create($validated);

            BitacoraComponente::create([
                'componente_id' => $componente->id,
                'usuario_id' => auth()->id(),
                'accion' => 'creado',
                'descripcion_detallada' => "Componente creado: {$componente->serial_unico} en sede {$componente->sede->nombre_sede}",
                'datos_nuevos' => $componente->toArray(),
                'fecha_registro' => now(),
            ]);

            DB::commit();
            return redirect()->route('admin.componentes.index')->with('success', 'Componente creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $componente = Componente::findOrFail($id);
        $categorias = CategoriaComponente::orderBy('nombre')->get();
        $sedes = Sede::with('estado')->orderBy('nombre_sede')->get();
        return view('admin.componentes.edit', compact('componente', 'categorias', 'sedes'));
    }

    public function update(Request $request, $id)
    {
        $componente = Componente::findOrFail($id);

        $validated = $request->validate([
            'categoria_componente_id' => 'required|exists:categorias_componentes,id',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'serial_unico' => 'required|string|max:150|unique:componentes,serial_unico,' . $componente->id,
            'estatus' => 'required|in:Disponible,Instalado,En Revisión,Desincorporado',
            'sede_id' => 'required|exists:sedes,id',
            'caracteristicas_tecnicas' => 'nullable|string',
            'observaciones' => 'nullable|string',
        ]);

        if ($request->filled('caracteristicas_tecnicas')) {
            $decoded = json_decode($request->caracteristicas_tecnicas, true);
            $validated['caracteristicas_tecnicas'] = is_array($decoded) ? $decoded : null;
        } else {
            $validated['caracteristicas_tecnicas'] = null;
        }

        $datosAnteriores = $componente->toArray();
        $estatusAnterior = $componente->estatus;

        DB::beginTransaction();
        try {
            $componente->update($validated);

            $descripcion = "Componente actualizado: {$componente->serial_unico}";
            if ($estatusAnterior !== $validated['estatus']) {
                $descripcion = "Estatus cambiado de '{$estatusAnterior}' a '{$validated['estatus']}' para {$componente->serial_unico}";
            }

            BitacoraComponente::create([
                'componente_id' => $componente->id,
                'usuario_id' => auth()->id(),
                'accion' => 'actualizado',
                'descripcion_detallada' => $descripcion,
                'datos_anteriores' => $datosAnteriores,
                'datos_nuevos' => $componente->fresh()->toArray(),
                'fecha_registro' => now(),
            ]);

            DB::commit();
            return redirect()->route('admin.componentes.index')->with('success', 'Componente actualizado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $componente = Componente::findOrFail($id);
        $datosComponente = $componente->toArray();

        DB::beginTransaction();
        try {
            BitacoraComponente::create([
                'componente_id' => $componente->id,
                'usuario_id' => auth()->id(),
                'accion' => 'eliminado',
                'descripcion_detallada' => "Componente {$componente->serial_unico} eliminado del sistema",
                'datos_anteriores' => $datosComponente,
                'fecha_registro' => now(),
            ]);

            $componente->delete();

            DB::commit();
            return redirect()->route('admin.componentes.index')->with('success', 'Componente eliminado exitosamente.');
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
        return view('admin.componentes.importar');
    }

    public function procesarImportacion(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $service = new ImportacionService('componentes');
            $import = new ComponentesImport($service);

            Excel::import($import, $request->file('archivo'));

            $resumen = $service->generarResumen();

            return redirect()->route('admin.componentes.index')
                ->with('importacion_resumen', $resumen)
                ->with('success', 'Componentes importados exitosamente.');

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
        return Excel::download(new ComponentesExport, 'componentes-' . date('Y-m-d') . '.xlsx');
    }

    public function exportarPDF()
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = Componente::with(['categoria', 'sede.estado', 'equipoActual']);

        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        $componentes = $query->orderBy('serial_unico')->get();

        $pdf = Pdf::loadView('admin.componentes.pdf', compact('componentes'));
        $pdf->setPaper('letter', 'landscape');
        return $pdf->download('componentes-' . date('Y-m-d') . '.pdf');
    }

    public function descargarPlantilla()
    {
        return Excel::download(new PlantillaComponentesExport, 'plantilla-componentes.xlsx');
    }

    /*
    |--------------------------------------------------------------------------
    | PDF INDIVIDUAL Y PEGATINA
    |--------------------------------------------------------------------------
    */

    public function pdfIndividual($id)
    {
        $componente = Componente::with(['categoria', 'sede.estado', 'equipoActual'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.componentes.pdf_individual', compact('componente'));
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        return $pdf->download('Ficha-Componente-' . $componente->serial_unico . '.pdf');
    }

    public function pdfPegatina($id)
    {
        $componente = Componente::with(['categoria', 'sede.estado'])->findOrFail($id);
    
        $pdf = Pdf::loadView('admin.componentes.pdf_pegatina', compact('componente'));
        $pdf->setPaper([0, 0, 90 * 2.83464567, 45 * 2.83464567]);
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
        ]);
    
        return $pdf->download('Pegatina-' . $componente->serial_unico . '.pdf');
    }
}