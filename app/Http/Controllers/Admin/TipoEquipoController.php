<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TipoEquipo;
use Illuminate\Http\Request;

class TipoEquipoController extends Controller
{
    public function index()
    {
        $tipos = TipoEquipo::orderBy('id')->paginate(15);
        return view('admin.tipos_equipos.index', compact('tipos'));
    }

    public function create()
    {
        return view('admin.tipos_equipos.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:tipos_equipos',
            'descripcion' => 'nullable|string',
        ]);

        TipoEquipo::create($validated);
        return redirect()->route('admin.tipos-equipos.index')->with('success', 'Tipo de equipo creado.');
    }

    public function edit($id)
    {
        $tiposEquipo = TipoEquipo::findOrFail($id);
        return view('admin.tipos_equipos.edit', compact('tiposEquipo'));
    }

    public function update(Request $request, $id)
    {
        $tiposEquipo = TipoEquipo::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:tipos_equipos,nombre,' . $tiposEquipo->id,
            'descripcion' => 'nullable|string',
        ]);

        $tiposEquipo->update($validated);
        return redirect()->route('admin.tipos-equipos.index')->with('success', 'Tipo de equipo actualizado.');
    }

    public function destroy($id)
    {
        $tiposEquipo = TipoEquipo::findOrFail($id);
        $tiposEquipo->delete();
        return redirect()->route('admin.tipos-equipos.index')->with('success', 'Tipo de equipo eliminado.');
    }
}