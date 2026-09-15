<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaBien;
use Illuminate\Http\Request;

class CategoriaBienController extends Controller
{
    public function index()
    {
        $categorias = CategoriaBien::orderBy('id')->paginate(15);
        return view('admin.bienes_categorias.index', compact('categorias'));
    }

    public function create()
    {
        return view('admin.bienes_categorias.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias_bienes',
            'descripcion' => 'nullable|string',
        ]);

        CategoriaBien::create($validated);
        return redirect()->route('admin.bienes-categorias.index')->with('success', 'Categoría creada.');
    }

    public function edit($id)
    {
        $categoria = CategoriaBien::findOrFail($id);
        return view('admin.bienes_categorias.edit', compact('categoria'));
    }

    public function update(Request $request, $id)
    {
        $categoria = CategoriaBien::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias_bienes,nombre,' . $categoria->id,
            'descripcion' => 'nullable|string',
        ]);

        $categoria->update($validated);
        return redirect()->route('admin.bienes-categorias.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy($id)
    {
        $categoria = CategoriaBien::findOrFail($id);
        $categoria->delete();
        return redirect()->route('admin.bienes-categorias.index')->with('success', 'Categoría eliminada.');
    }
}