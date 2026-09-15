<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sede;
use App\Models\Estado;
use Illuminate\Http\Request;

class SedeController extends Controller
{
    public function index()
    {
        $sedes = Sede::with('estado')->orderBy('id')->paginate(15);
        return view('admin.sedes.index', compact('sedes'));
    }

    public function create()
    {
        $estados = Estado::orderBy('nombre')->get();
        return view('admin.sedes.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'estado_id' => 'required|exists:estados,id',
            'nombre_sede' => 'required|string|max:150',
            'direccion' => 'required|string',
            'codigo_postal' => 'nullable|string|max:10',
        ]);

        Sede::create($validated);
        return redirect()->route('admin.sedes.index')->with('success', 'Sede creada exitosamente.');
    }

    public function edit($id)
    {
        $sede = Sede::findOrFail($id);
        $estados = Estado::orderBy('nombre')->get();
        return view('admin.sedes.edit', compact('sede', 'estados'));
    }

    public function update(Request $request, $id)
    {
        $sede = Sede::findOrFail($id);
        $validated = $request->validate([
            'estado_id' => 'required|exists:estados,id',
            'nombre_sede' => 'required|string|max:150',
            'direccion' => 'required|string',
            'codigo_postal' => 'nullable|string|max:10',
        ]);

        $sede->update($validated);
        return redirect()->route('admin.sedes.index')->with('success', 'Sede actualizada exitosamente.');
    }

    public function destroy($id)
    {
        $sede = Sede::findOrFail($id);
        $sede->delete();
        return redirect()->route('admin.sedes.index')->with('success', 'Sede eliminada exitosamente.');
    }
}