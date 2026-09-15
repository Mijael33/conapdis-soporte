<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BienNacional;
use App\Models\CategoriaBien;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\FiltroSedeTrait;
use App\Services\ImportacionService;
use App\Imports\BienesImport;
use App\Exports\BienesExport;
use App\Exports\PlantillaBienesExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class BienNacionalController extends Controller
{
    use FiltroSedeTrait;

    public function index(Request $request)
    {
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = BienNacional::with(['categoria', 'sede.estado']);

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        if ($request->categoria_id) {
            $query->where('categoria_bien_id', $request->categoria_id);
        }
        if ($request->estatus) {
            $query->where('estatus', $request->estatus);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('codigo_inventario', 'ILIKE', "%{$search}%")
                  ->orWhere('descripcion', 'ILIKE', "%{$search}%")
                  ->orWhere('marca', 'ILIKE', "%{$search}%");
            });
        }

        $bienes = $query->orderBy('codigo_inventario')->paginate(15);
        $categorias = CategoriaBien::orderBy('nombre')->get();

        return view('admin.bienes.index', compact('bienes', 'categorias'));
    }

    public function show($id)
    {
        $bien = BienNacional::with(['categoria', 'sede.estado'])->findOrFail($id);
        return view('admin.bienes.show', compact('bien'));
    }

    public function create()
    {
        $categorias = CategoriaBien::orderBy('nombre')->get();
        $sedes = Sede::with('estado')->orderBy('nombre_sede')->get();
        return view('admin.bienes.create', compact('categorias', 'sedes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_inventario' => 'required|string|max:50|unique:bienes_nacionales',
            'categoria_bien_id' => 'required|exists:categorias_bienes,id',
            'sede_id' => 'required|exists:sedes,id',
            'descripcion' => 'required|string',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'serial' => 'nullable|string|max:150',
            'color' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:100',
            'usuario_asignado_nombre' => 'nullable|string|max:150',
            'usuario_asignado_cedula' => 'nullable|string|max:20',
            'usuario_asignado_cargo' => 'nullable|string|max:150',
            'valor_adquisicion' => 'nullable|numeric',
            'fecha_adquisicion' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        BienNacional::create($validated);
        return redirect()->route('admin.bienes.index')->with('success', 'Bien creado exitosamente.');
    }

    public function edit($id)
    {
        $bien = BienNacional::findOrFail($id);
        $categorias = CategoriaBien::orderBy('nombre')->get();
        $sedes = Sede::with('estado')->orderBy('nombre_sede')->get();
        return view('admin.bienes.edit', compact('bien', 'categorias', 'sedes'));
    }

    public function update(Request $request, $id)
    {
        $bien = BienNacional::findOrFail($id);

        $validated = $request->validate([
            'codigo_inventario' => 'required|string|max:50|unique:bienes_nacionales,codigo_inventario,' . $bien->id,
            'categoria_bien_id' => 'required|exists:categorias_bienes,id',
            'sede_id' => 'required|exists:sedes,id',
            'descripcion' => 'required|string',
            'marca' => 'nullable|string|max:100',
            'modelo' => 'nullable|string|max:100',
            'serial' => 'nullable|string|max:150',
            'color' => 'nullable|string|max:50',
            'material' => 'nullable|string|max:100',
            'estatus' => 'required|in:Disponible,Asignado,En Mantenimiento,Desincorporado',
            'usuario_asignado_nombre' => 'nullable|string|max:150',
            'usuario_asignado_cedula' => 'nullable|string|max:20',
            'usuario_asignado_cargo' => 'nullable|string|max:150',
            'valor_adquisicion' => 'nullable|numeric',
            'fecha_adquisicion' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        $bien->update($validated);
        return redirect()->route('admin.bienes.index')->with('success', 'Bien actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $bien = BienNacional::findOrFail($id);
        $bien->delete();
        return redirect()->route('admin.bienes.index')->with('success', 'Bien eliminado exitosamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | IMPORTACIÓN Y EXPORTACIÓN
    |--------------------------------------------------------------------------
    */

    public function importar()
    {
        return view('admin.bienes.importar');
    }

    public function procesarImportacion(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $service = new ImportacionService('bienes');
            $import = new BienesImport($service);

            Excel::import($import, $request->file('archivo'));

            $resumen = $service->generarResumen();

            return redirect()->route('admin.bienes.index')
                ->with('importacion_resumen', $resumen)
                ->with('success', 'Bienes importados exitosamente.');

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
        return Excel::download(new BienesExport, 'bienes-' . date('Y-m-d') . '.xlsx');
    }

    public function exportarPDF()
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = BienNacional::with(['categoria', 'sede.estado']);
        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        $bienes = $query->orderBy('codigo_inventario')->get();
        $pdf = Pdf::loadView('admin.bienes.pdf', compact('bienes'));
        $pdf->setPaper('letter', 'landscape');
        return $pdf->download('bienes-' . date('Y-m-d') . '.pdf');
    }

    public function descargarPlantilla()
    {
        return Excel::download(new PlantillaBienesExport, 'plantilla-bienes.xlsx');
    }

    /*
    |--------------------------------------------------------------------------
    | PDF INDIVIDUAL Y PEGATINA
    |--------------------------------------------------------------------------
    */

    public function pdfIndividual($id)
    {
        $bien = BienNacional::with(['categoria', 'sede.estado'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.bienes.pdf_individual', compact('bien'));
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        return $pdf->download('Ficha-Bien-' . $bien->codigo_inventario . '.pdf');
    }

    public function pdfPegatina($id)
    {
        $bien = BienNacional::with(['categoria', 'sede.estado'])->findOrFail($id);
    
        $pdf = Pdf::loadView('admin.bienes.pdf_pegatina', compact('bien'));
        $pdf->setPaper([0, 0, 90 * 2.83464567, 45 * 2.83464567]);
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
        ]);
    
        return $pdf->download('Pegatina-' . $bien->codigo_inventario . '.pdf');
    }
}