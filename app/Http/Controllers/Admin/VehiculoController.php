<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vehiculo;
use App\Models\CategoriaVehiculo;
use App\Models\Sede;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Traits\FiltroSedeTrait;
use App\Services\ImportacionService;
use App\Imports\VehiculosImport;
use App\Exports\VehiculosExport;
use App\Exports\PlantillaVehiculosExport;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class VehiculoController extends Controller
{
    use FiltroSedeTrait;

    public function index(Request $request)
    {
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = Vehiculo::with(['categoria', 'sede.estado']);

        if ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        if ($request->categoria_id) {
            $query->where('categoria_vehiculo_id', $request->categoria_id);
        }
        if ($request->estatus) {
            $query->where('estatus', $request->estatus);
        }
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('placa', 'ILIKE', "%{$search}%")
                  ->orWhere('marca', 'ILIKE', "%{$search}%")
                  ->orWhere('modelo', 'ILIKE', "%{$search}%");
            });
        }

        $vehiculos = $query->orderBy('placa')->paginate(15);
        $categorias = CategoriaVehiculo::orderBy('nombre')->get();

        return view('admin.vehiculos.index', compact('vehiculos', 'categorias'));
    }

    public function show($id)
    {
        $vehiculo = Vehiculo::with(['categoria', 'sede.estado'])->findOrFail($id);
        return view('admin.vehiculos.show', compact('vehiculo'));
    }

    public function create()
    {
        $categorias = CategoriaVehiculo::orderBy('nombre')->get();
        $sedes = Sede::with('estado')->orderBy('nombre_sede')->get();
        return view('admin.vehiculos.create', compact('categorias', 'sedes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo_inventario' => 'required|string|max:50|unique:vehiculos',
            'categoria_vehiculo_id' => 'required|exists:categorias_vehiculos,id',
            'sede_id' => 'required|exists:sedes,id',
            'placa' => 'required|string|max:20|unique:vehiculos',
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'anio' => 'nullable|integer',
            'color' => 'nullable|string|max:50',
            'serial_motor' => 'nullable|string|max:100',
            'serial_chasis' => 'nullable|string|max:100',
            'kilometraje' => 'nullable|integer',
            'observaciones' => 'nullable|string',
        ]);

        Vehiculo::create($validated);
        return redirect()->route('admin.vehiculos.index')->with('success', 'Vehículo creado exitosamente.');
    }

    public function edit($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $categorias = CategoriaVehiculo::orderBy('nombre')->get();
        $sedes = Sede::with('estado')->orderBy('nombre_sede')->get();
        return view('admin.vehiculos.edit', compact('vehiculo', 'categorias', 'sedes'));
    }

    public function update(Request $request, $id)
    {
        $vehiculo = Vehiculo::findOrFail($id);

        $validated = $request->validate([
            'codigo_inventario' => 'required|string|max:50|unique:vehiculos,codigo_inventario,' . $vehiculo->id,
            'categoria_vehiculo_id' => 'required|exists:categorias_vehiculos,id',
            'sede_id' => 'required|exists:sedes,id',
            'placa' => 'required|string|max:20|unique:vehiculos,placa,' . $vehiculo->id,
            'marca' => 'required|string|max:100',
            'modelo' => 'required|string|max:100',
            'anio' => 'nullable|integer',
            'color' => 'nullable|string|max:50',
            'serial_motor' => 'nullable|string|max:100',
            'serial_chasis' => 'nullable|string|max:100',
            'kilometraje' => 'nullable|integer',
            'estatus' => 'required|in:Disponible,Asignado,En Mantenimiento,Desincorporado',
            'usuario_asignado_nombre' => 'nullable|string|max:150',
            'usuario_asignado_cedula' => 'nullable|string|max:20',
            'usuario_asignado_cargo' => 'nullable|string|max:150',
            'observaciones' => 'nullable|string',
        ]);

        $vehiculo->update($validated);
        return redirect()->route('admin.vehiculos.index')->with('success', 'Vehículo actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $vehiculo = Vehiculo::findOrFail($id);
        $vehiculo->delete();
        return redirect()->route('admin.vehiculos.index')->with('success', 'Vehículo eliminado exitosamente.');
    }

    /*
    |--------------------------------------------------------------------------
    | IMPORTACIÓN Y EXPORTACIÓN
    |--------------------------------------------------------------------------
    */

    public function importar()
    {
        return view('admin.vehiculos.importar');
    }

    public function procesarImportacion(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:20480',
        ]);

        try {
            $service = new ImportacionService('vehiculos');
            $import = new VehiculosImport($service);

            Excel::import($import, $request->file('archivo'));

            $resumen = $service->generarResumen();

            return redirect()->route('admin.vehiculos.index')
                ->with('importacion_resumen', $resumen)
                ->with('success', 'Vehículos importados exitosamente.');

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
        return Excel::download(new VehiculosExport, 'vehiculos-' . date('Y-m-d') . '.xlsx');
    }

    public function exportarPDF()
    {
        $user = auth()->user();
        $esAdmin = $user->hasRole('Administrador');
        $esAuditor = $user->hasRole('Auditor');
        $sedeId = session('filtro_sede_id');
        $estadoId = session('filtro_estado_id');

        $query = Vehiculo::with(['categoria', 'sede.estado']);
        if (!$esAdmin && !$esAuditor) {
            $query->where('sede_id', $user->sede_id);
        } elseif ($sedeId) {
            $query->where('sede_id', $sedeId);
        } elseif ($estadoId) {
            $sedeIds = Sede::where('estado_id', $estadoId)->pluck('id');
            $query->whereIn('sede_id', $sedeIds);
        }

        $vehiculos = $query->orderBy('placa')->get();
        $pdf = Pdf::loadView('admin.vehiculos.pdf', compact('vehiculos'));
        $pdf->setPaper('letter', 'landscape');
        return $pdf->download('vehiculos-' . date('Y-m-d') . '.pdf');
    }

    public function descargarPlantilla()
    {
        return Excel::download(new PlantillaVehiculosExport, 'plantilla-vehiculos.xlsx');
    }

    /*
    |--------------------------------------------------------------------------
    | PDF INDIVIDUAL Y PEGATINA
    |--------------------------------------------------------------------------
    */

    public function pdfIndividual($id)
    {
        $vehiculo = Vehiculo::with(['categoria', 'sede.estado'])->findOrFail($id);

        $pdf = Pdf::loadView('admin.vehiculos.pdf_individual', compact('vehiculo'));
        $pdf->setPaper('letter', 'portrait');
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
        ]);

        return $pdf->download('Ficha-Vehiculo-' . $vehiculo->placa . '.pdf');
    }

    public function pdfPegatina($id)
    {
        $vehiculo = Vehiculo::with(['categoria', 'sede.estado'])->findOrFail($id);
    
        $pdf = Pdf::loadView('admin.vehiculos.pdf_pegatina', compact('vehiculo'));
        $pdf->setPaper([0, 0, 90 * 2.83464567, 45 * 2.83464567]);
        $pdf->setOptions([
            'isRemoteEnabled' => true,
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true,
            'defaultFont' => 'DejaVu Sans',
            'dpi' => 96,
        ]);
    
        return $pdf->download('Pegatina-' . $vehiculo->placa . '.pdf');
    }
}