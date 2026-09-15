<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CategoriaComponente;
use Illuminate\Http\Request;

class CategoriaComponenteController extends Controller
{
    public function index()
    {
        $categorias = CategoriaComponente::orderBy('id')->paginate(15);
        return view('admin.categorias_componentes.index', compact('categorias'));
    }

    public function create()
    {
        return view('admin.categorias_componentes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias_componentes',
            'descripcion' => 'nullable|string',
        ]);

        CategoriaComponente::create($validated);
        return redirect()->route('admin.categorias-componentes.index')->with('success', 'Categoría creada.');
    }

    public function edit($id)
    {
        $categoriasComponente = CategoriaComponente::findOrFail($id);
        return view('admin.categorias_componentes.edit', compact('categoriasComponente'));
    }

    public function update(Request $request, $id)
    {
        $categoriasComponente = CategoriaComponente::findOrFail($id);
        $validated = $request->validate([
            'nombre' => 'required|string|max:100|unique:categorias_componentes,nombre,' . $categoriasComponente->id,
            'descripcion' => 'nullable|string',
        ]);

        $categoriasComponente->update($validated);
        return redirect()->route('admin.categorias-componentes.index')->with('success', 'Categoría actualizada.');
    }

    public function destroy($id)
    {
        $categoriasComponente = CategoriaComponente::findOrFail($id);
        $categoriasComponente->delete();
        return redirect()->route('admin.categorias-componentes.index')->with('success', 'Categoría eliminada.');
    }
}