<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estado;
use Illuminate\Http\Request;

class EstadoController extends Controller
{
    public function index()
    {
        $estados = Estado::orderBy('id')->paginate(15);
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
        ]);

        Estado::create($validated);
        return redirect()->route('admin.estados.index')->with('success', 'Estado creado exitosamente.');
    }

    public function edit($id)
    {
        $estado = Estado::findOrFail($id);
        return view('admin.estados.edit', compact('estado'));
    }

    public function update(Request $request, $id)
    {
        $estado = Estado::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:estados,nombre,' . $estado->id,
            'region' => 'required|string|max:50',
        ]);

        $estado->update($validated);
        return redirect()->route('admin.estados.index')->with('success', 'Estado actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $estado = Estado::findOrFail($id);
        $estado->delete();
        return redirect()->route('admin.estados.index')->with('success', 'Estado eliminado exitosamente.');
    }
}