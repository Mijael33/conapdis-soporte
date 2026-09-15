<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaSonido;
use Illuminate\Http\Request;

class CategoriaSonidoController extends Controller
{
    public function index()
    {
        $categorias = CategoriaSonido::orderBy('id')->paginate(15);
        return view('admin.sonido_categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('admin.sonido_categorias.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias_sonido',
            'descripcion' => 'nullable|string',
        ]);

        CategoriaSonido::create($validated);
        return redirect()->route('admin.sonido-categorias.index')->with('success', 'Categoría creada.');
    }

    public function edit($id)
    {
        $categoria = CategoriaSonido::findOrFail($id);
        return view('admin.sonido_categorias.edit', compact('categoria'));
    }

    public function update(Request $request, $id)
    {
        $categoria = CategoriaSonido::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias_sonido,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string',
        ]);

        $categoria->update($validated);
        return redirect()->route('admin.sonido-categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy($id)
    {
        $categoria = CategoriaSonido::findOrFail($id);
        $categoria->delete();
        return redirect()->route('admin.sonido-categorias.index')->with('success', 'Categoría eliminada.');
    }
}