<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaVehiculo;
use Illuminate\Http\Request;

class CategoriaVehiculoController extends Controller
{
    public function index()
    {
        $categorias = CategoriaVehiculo::orderBy('id')->paginate(15);
        return view('admin.vehiculos_categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('admin.vehiculos_categorias.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias_vehiculos',
            'descripcion' => 'nullable|string',
        ]);

        CategoriaVehiculo::create($validated);
        return redirect()->route('admin.vehiculos-categorias.index')->with('success', 'Categoría creada.');
    }

    public function edit($id)
    {
        $categoria = CategoriaVehiculo::findOrFail($id);
        return view('admin.vehiculos_categorias.edit', compact('categoria'));
    }

    public function update(Request $request, $id)
    {
        $categoria = CategoriaVehiculo::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias_vehiculos,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string',
        ]);

        $categoria->update($validated);
        return redirect()->route('admin.vehiculos-categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy($id)
    {
        $categoria = CategoriaVehiculo::findOrFail($id);
        $categoria->delete();
        return redirect()->route('admin.vehiculos-categorias.index')->with('success', 'Categoría eliminada.');
    }
}