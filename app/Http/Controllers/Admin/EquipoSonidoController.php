<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EquipoSonido;
use App\Models\CategoriaSonido;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\FiltroSedeTrait;
use App\Services\ImportacionService;
use App\Imports\SonidoImport;
use App\Exports\SonidoExport;
use App\Exports\PlantillaSonidoExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class EquipoSonidoController extends Controller
{
    use FiltroSedeTrait;

    public function index(Request $request)
    {
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = EquipoSonido::with(['categoria', 'sede.estado']);

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        if ($request->categoria_id) {
            $query->where('categoria_sonido_id', $request->categoria_id);
        }
        if ($request->estatus) {
            $query->where('estatus', $request->estatus);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('serial', 'ILIKE', "%{$search}%")
                  ->orWhere('marca', 'ILIKE', "%{$search}%")
                  ->orWhere('modelo', 'ILIKE', "%{$search}%");
            });
        }

        $equipos = $query->orderBy('serial')->paginate(15);
        $categorias = CategoriaSonido::orderBy('nombre')->get();

        return view('admin.sonido.index', compact('equipos', 'categorias'));
    }

    public function show($id)
    {
        $equipo = EquipoSonido::with(['categoria', 'sede.estado'])->findOrFail($id);
        return view('admin.sonido.show', compact('equipo'));
    }

    public function create()
    {
        $categorias = CategoriaSonido::orderBy('nombre')->get();
        $sedes = Sede::with('estado')->orderBy('nombre_sede')->get();
        return view('admin.sonido.create', compact('categorias', 'sedes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_inventario' => 'required|string|max:50|unique:equipos_sonido',
            'categoria_sonido_id' => 'required|exists:categorias_sonido,id',
            'sede_id' => 'required|exists:sedes,id',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'serial' => 'required|string|max:150|unique:equipos_sonido',
            'potencia' => 'nullable|string|max:100',
            'observaciones' => 'nullable|string',
        ]);

        EquipoSonido::create($validated);
        return redirect()->route('admin.sonido.index')->with('success', 'Equipo de sonido creado exitosamente.');
    }

    public function edit($id)
    {
        $equipo = EquipoSonido::findOrFail($id);
        $categorias = CategoriaSonido::orderBy('nombre')->get();
        $sedes = Sede::with('estado')->orderBy('nombre_sede')->get();
        return view('admin.sonido.edit', compact('equipo', 'categorias', 'sedes'));
    }

    public function update(Request $request, $id)
    {
        $equipo = EquipoSonido::findOrFail($id);

        $validated = $request->validate([
            'codigo_inventario' => 'required|string|max:50|unique:equipos_sonido,codigo_inventario,' . $equipo->id,
            'categoria_sonido_id' => 'required|exists:categorias_sonido,id',
            'sede_id' => 'required|exists:sedes,id',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'serial' => 'required|string|max:150|unique:equipos_sonido,serial,' . $equipo->id,
            'potencia' => 'nullable|string|max:100',
            'estatus' => 'required|in:Disponible,Asignado,En Mantenimiento,Desincorporado',
            'usuario_asignado_nombre' => 'nullable|string|max:150',
            'usuario_asignado_cedula' => 'nullable|string|max:20',
            'usuario_asignado_cargo' => 'nullable|string|max:150',
            'observaciones' => 'nullable|string',
        ]);

        $equipo->update($validated);
        return redirect()->route('admin.sonido.index')->with('success', 'Equipo de sonido actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $equipo = EquipoSonido::findOrFail($id);
        $equipo->delete();
        return redirect()->route('admin.sonido.index')->with('success', 'Equipo de sonido eliminado exitosamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | IMPORTACIÓN Y EXPORTACIÓN
    |--------------------------------------------------------------------------
    */

    public function importar()
    {
        return view('admin.sonido.importar');
    }

    public function procesarImportacion(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $service = new ImportacionService('sonido');
            $import = new SonidoImport($service);

            Excel::import($import, $request->file('archivo'));

            $resumen = $service->generarResumen();

            return redirect()->route('admin.sonido.index')
                ->with('importacion_resumen', $resumen)
                ->with('success', 'Equipos de sonido importados exitosamente.');

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
        return Excel::download(new SonidoExport, 'sonido-' . date('Y-m-d') . '.xlsx');
    }

    public function exportarPDF()
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = EquipoSonido::with(['categoria', 'sede.estado']);
        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        $equipos = $query->orderBy('serial')->get();
        $pdf = Pdf::loadView('admin.sonido.pdf', compact('equipos'));
        $pdf->setPaper('letter', 'landscape');
        return $pdf->download('sonido-' . date('Y-m-d') . '.pdf');
    }

    public function descargarPlantilla()
    {
        return Excel::download(new PlantillaSonidoExport, 'plantilla-sonido.xlsx');
    }

    /*
    |--------------------------------------------------------------------------
    | PDF INDIVIDUAL Y PEGATINA
    |--------------------------------------------------------------------------
    */

    public function pdfIndividual($id)
    {
        $equipo = EquipoSonido::with(['categoria', 'sede.estado'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.sonido.pdf_individual', compact('equipo'));
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        return $pdf->download('Ficha-Sonido-' . $equipo->serial . '.pdf');
    }

    public function pdfPegatina($id)
    {
        $equipo = EquipoSonido::with(['categoria', 'sede.estado'])->findOrFail($id);
    
        $pdf = Pdf::loadView('admin.sonido.pdf_pegatina', compact('equipo'));
        $pdf->setPaper([0, 0, 90 * 2.83464567, 45 * 2.83464567]);
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
        ]);
    
        return $pdf->download('Pegatina-' . $equipo->serial . '.pdf');
    }
}