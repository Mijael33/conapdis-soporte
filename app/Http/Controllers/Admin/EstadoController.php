<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estado;
use App\Models\Sede;
use App\Services\BitacoraService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EstadoController extends Controller
{
    public function index()
    {
        $estados = Estado::withCount('sedes')->orderBy('nombre')->paginate(15);
        return view('admin.estados.index', compact('estados'));
    }

    public function create()
    {
        return view('admin.estados.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:estados',
            'region' => 'required|string|max:50',
            'sedes' => 'nullable|array',
            'sedes.*.nombre_sede' => 'required|string|max:150',
            'sedes.*.direccion' => 'required|string',
            'sedes.*.codigo_postal' => 'nullable|string|max:10',
        ]);

        DB::beginTransaction();
        try {
            $estado = Estado::create([
                'nombre' => $validated['nombre'],
                'region' => $validated['region'],
            ]);

            $sedesCreadas = 0;
            if (!empty($validated['sedes'])) {
                foreach ($validated['sedes'] as $sedeData) {
                    if (!empty($sedeData['nombre_sede']) && !empty($sedeData['direccion'])) {
                        Sede::create([
                            'estado_id' => $estado->id,
                            'nombre_sede' => $sedeData['nombre_sede'],
                            'direccion' => $sedeData['direccion'],
                            'codigo_postal' => $sedeData['codigo_postal'] ?? null,
                        ]);
                        $sedesCreadas++;
                    }
                }
            }

            BitacoraService::crear(
                'estados',
                $estado,
                'Estado creado: ' . $estado->nombre . ' (' . $estado->region . ') con ' . $sedesCreadas . ' sede(s)',
                $estado->nombre
            );

            DB::commit();
            return redirect()->route('admin.estados.index')
                ->with('success', 'Estado creado exitosamente con ' . $sedesCreadas . ' sede(s).');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el estado: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $estado = Estado::with(['sedes' => function ($q) {
            $q->orderBy('nombre_sede');
        }])->findOrFail($id);

        return view('admin.estados.show', compact('estado'));
    }

    public function edit($id)
    {
        $estado = Estado::with('sedes')->findOrFail($id);
        return view('admin.estados.edit', compact('estado'));
    }

    public function update(Request $request, $id)
    {
        $estado = Estado::findOrFail($id);

        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:estados,nombre,' . $estado->id,
            'region' => 'required|string|max:50',
            'sedes' => 'nullable|array',
            'sedes.*.id' => 'nullable|integer|exists:sedes,id',
            'sedes.*.nombre_sede' => 'required|string|max:150',
            'sedes.*.direccion' => 'required|string',
            'sedes.*.codigo_postal' => 'nullable|string|max:10',
        ]);

        $datosAnteriores = $estado->toArray();

        DB::beginTransaction();
        try {
            $estado->update([
                'nombre' => $validated['nombre'],
                'region' => $validated['region'],
            ]);

            // Sincronizar sedes
            $idsRecibidos = [];
            if (!empty($validated['sedes'])) {
                foreach ($validated['sedes'] as $sedeData) {
                    if (empty($sedeData['nombre_sede']) || empty($sedeData['direccion'])) {
                        continue;
                    }

                    if (!empty($sedeData['id'])) {
                        // Actualizar existente
                        $sede = Sede::where('id', $sedeData['id'])
                            ->where('estado_id', $estado->id)
                            ->first();
                        if ($sede) {
                            $sede->update([
                                'nombre_sede' => $sedeData['nombre_sede'],
                                'direccion' => $sedeData['direccion'],
                                'codigo_postal' => $sedeData['codigo_postal'] ?? null,
                            ]);
                            $idsRecibidos[] = $sede->id;
                        }
                    } else {
                        // Crear nueva
                        $nueva = Sede::create([
                            'estado_id' => $estado->id,
                            'nombre_sede' => $sedeData['nombre_sede'],
                            'direccion' => $sedeData['direccion'],
                            'codigo_postal' => $sedeData['codigo_postal'] ?? null,
                        ]);
                        $idsRecibidos[] = $nueva->id;
                    }
                }
            }

            // Eliminar las sedes que no vinieron en el request
            $sedesAEliminar = Sede::where('estado_id', $estado->id)
                ->whereNotIn('id', $idsRecibidos)
                ->get();

            foreach ($sedesAEliminar as $sedeEliminar) {
                // Verificar que no tenga equipos, componentes, bienes, etc.
                $tieneDependencias = $sedeEliminar->equipos()->exists()
                    || $sedeEliminar->componentes()->exists()
                    || $sedeEliminar->bienes()->exists()
                    || $sedeEliminar->vehiculos()->exists()
                    || $sedeEliminar->equiposSonido()->exists()
                    || $sedeEliminar->usuarios()->exists();

                if ($tieneDependencias) {
                    DB::rollBack();
                    return back()->with('error', 'No se puede eliminar la sede "' . $sedeEliminar->nombre_sede . '" porque tiene registros asociados (equipos, usuarios, etc.).')->withInput();
                }

                $sedeEliminar->delete();
            }

            $descripcion = 'Estado actualizado: ' . $estado->nombre;
            if ($datosAnteriores['region'] !== $validated['region']) {
                $descripcion .= " (región: {$datosAnteriores['region']} → {$validated['region']})";
            }
            $descripcion .= ' | Sedes: ' . count($idsRecibidos);

            BitacoraService::editar(
                'estados',
                $estado,
                $datosAnteriores,
                $descripcion,
                $estado->nombre
            );

            DB::commit();
            return redirect()->route('admin.estados.index')
                ->with('success', 'Estado y sedes actualizados exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al actualizar: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $estado = Estado::withCount('sedes')->findOrFail($id);

        if ($estado->sedes_count > 0) {
            return back()->with('error', 'No se puede eliminar el estado "' . $estado->nombre . '" porque tiene ' . $estado->sedes_count . ' sede(s) asociada(s). Elimina primero las sedes.');
        }

        DB::beginTransaction();
        try {
            BitacoraService::eliminar(
                'estados',
                $estado,
                'Estado eliminado: ' . $estado->nombre,
                $estado->nombre
            );

            $estado->delete();

            DB::commit();
            return redirect()->route('admin.estados.index')->with('success', 'Estado eliminado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al eliminar: ' . $e->getMessage());
        }
    }
}