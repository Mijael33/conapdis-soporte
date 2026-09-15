<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\Sede;
use Illuminate\Http\Request;

class DepartamentoController extends Controller
{
    public function index()
    {
        $departamentos = Departamento::with('sede.estado')->orderBy('id')->paginate(15);
        return view('admin.departamentos.index', compact('departamentos'));
    }

    public function create()
    {
        $sedes = Sede::with('estado')->orderBy('nombre_sede')->get();
        return view('admin.departamentos.create', compact('sedes'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sede_id' => 'required|exists:sedes,id',
            'nombre_departamento' => 'required|string|max:150',
            'piso' => 'nullable|integer',
            'extension_telefonica' => 'nullable|string|max:10',
        ]);

        Departamento::create($validated);
        return redirect()->route('admin.departamentos.index')->with('success', 'Departamento creado exitosamente.');
    }

    public function edit($id)
    {
        $departamento = Departamento::findOrFail($id);
        $sedes = Sede::with('estado')->orderBy('nombre_sede')->get();
        return view('admin.departamentos.edit', compact('departamento', 'sedes'));
    }

    public function update(Request $request, $id)
    {
        $departamento = Departamento::findOrFail($id);
        $validated = $request->validate([
            'sede_id' => 'required|exists:sedes,id',
            'nombre_departamento' => 'required|string|max:150',
            'piso' => 'nullable|integer',
            'extension_telefonica' => 'nullable|string|max:10',
        ]);

        $departamento->update($validated);
        return redirect()->route('admin.departamentos.index')->with('success', 'Departamento actualizado exitosamente.');
    }

    public function destroy($id)
    {
        $departamento = Departamento::findOrFail($id);
        $departamento->delete();
        return redirect()->route('admin.departamentos.index')->with('success', 'Departamento eliminado exitosamente.');
    }
}