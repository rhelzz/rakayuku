<?php

namespace App\Http\Controllers;

use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::latest('created_at')->get(['*']);
        return view('materials.index', compact('materials'));
    }

    public function create()
    {
        return view('materials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:20',
        ]);

        Material::create([
            'name' => $request->name,
            'unit' => $request->unit,
            'current_qty' => 0,
            'avg_price' => 0,
        ]);

        return redirect()->route('materials.index')->with('success', 'Material berhasil ditambahkan.');
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:20',
        ]);

        $material->update($request->only(['name', 'unit']));

        return redirect()->route('materials.index')->with('success', 'Material berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        if ($material->current_qty > 0) {
            return back()->with('error', 'Tidak bisa menghapus material yang masih memiliki stok.');
        }

        Material::destroy($material->id);

        return redirect()->route('materials.index')->with('success', 'Material berhasil dihapus.');
    }
}
