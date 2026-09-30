<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BienNacional;
use App\Models\CategoriaBien;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Traits\FiltroSedeTrait;
use App\Services\ImportacionService;
use App\Services\BitacoraService;
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

        if ($request->categoria_id) $query->where('categoria_bien_id', $request->categoria_id);
        if ($request->estatus) $query->where('estatus', $request->estatus);
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
            'estatus' => 'nullable|in:Disponible,Asignado,En Mantenimiento,Desincorporado',
            'usuario_asignado_nombre' => 'nullable|string|max:150',
            'usuario_asignado_cedula' => 'nullable|string|max:20',
            'usuario_asignado_cargo' => 'nullable|string|max:150',
            'valor_prudencial' => 'nullable|numeric|min:0',
            'valor_adquisicion' => 'nullable|numeric|min:0',
            'fecha_adquisicion' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        if (empty($validated['valor_prudencial']) && empty($validated['valor_adquisicion'])) {
            return back()->with('error', 'Debe ingresar al menos uno de los dos valores: Valor Prudencial o Valor de Adquisición.')->withInput();
        }

        // Default estatus si no viene
        $validated['estatus'] = $validated['estatus'] ?? 'Disponible';

        $bien = BienNacional::create($validated);

        BitacoraService::crear(
            'bienes',
            $bien,
            'Bien creado: ' . $bien->codigo_inventario . ' (' . Str::limit($bien->descripcion, 50) . ')',
            $bien->codigo_inventario
        );

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
            'valor_prudencial' => 'nullable|numeric|min:0',
            'valor_adquisicion' => 'nullable|numeric|min:0',
            'fecha_adquisicion' => 'nullable|date',
            'observaciones' => 'nullable|string',
        ]);

        if (empty($validated['valor_prudencial']) && empty($validated['valor_adquisicion'])) {
            return back()->with('error', 'Debe ingresar al menos uno de los dos valores: Valor Prudencial o Valor de Adquisición.')->withInput();
        }

        $datosAnteriores = $bien->toArray();
        $estatusAnterior = $bien->estatus;

        $bien->update($validated);

        $descripcion = 'Bien actualizado: ' . $bien->codigo_inventario;
        if ($estatusAnterior !== $validated['estatus']) {
            $descripcion .= " (estatus: {$estatusAnterior} → {$validated['estatus']})";
        }

        BitacoraService::editar('bienes', $bien, $datosAnteriores, $descripcion, $bien->codigo_inventario);

        return redirect()->route('admin.bienes.index')->with('success', 'Bien actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $bien = BienNacional::findOrFail($id);
        BitacoraService::eliminar('bienes', $bien, 'Bien eliminado: ' . $bien->codigo_inventario, $bien->codigo_inventario);
        $bien->delete();
        return redirect()->route('admin.bienes.index')->with('success', 'Bien eliminado exitosamente.');
    }

    public function importar()
    {
        return view('admin.bienes.importar');
    }

    public function procesarImportacion(Request $request)
    {
        $request->validate(['archivo' => 'required|file|mimes:xlsx,xls,csv|max:20480']);
        try {
            $service = new ImportacionService('bienes');
            $import = new BienesImport($service);
            Excel::import($import, $request->file('archivo'));
            $resumen = $service->generarResumen();
            BitacoraService::accion('bienes', 'importar', 'Importación masiva de bienes: ' . $resumen['importados'] . ' importados, ' . $resumen['fallidos'] . ' fallidos', null, $resumen);
            return redirect()->route('admin.bienes.index')->with('importacion_resumen', $resumen)->with('success', 'Bienes importados exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function descargarErrores($archivo)
    {
        $ruta = 'importaciones/' . $archivo;
        if (!Storage::disk('public')->exists($ruta)) {
            return back()->with('error', 'El archivo no existe.');
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

    public function pdfIndividual($id)
    {
        $bien = BienNacional::with(['categoria', 'sede.estado'])->findOrFail($id);
        $pdf = Pdf::loadView('admin.bienes.pdf_individual', compact('bien'));
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true]);
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