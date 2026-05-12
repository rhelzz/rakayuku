<?php

namespace App\Http\Controllers;

use App\Services\MaterialCodeService;
use App\Models\Material;
use App\Exports\MaterialExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MaterialController extends Controller
{
    protected MaterialCodeService $codeService;

    public function __construct(MaterialCodeService $codeService)
    {
        $this->codeService = $codeService;
    }

    public function index(Request $request)
    {
        $materials = Material::search($request->search, ['name', 'code', 'type'])
            ->sort($request->sort_field ?? 'created_at', $request->sort_dir ?? 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('materials.index', compact('materials'));
    }

    public function create()
    {
        return view('materials.create');
    }

    public function show(Material $material)
    {
        return view('materials.show', compact('material'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
            'is_dimension' => 'nullable|boolean',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'thickness' => 'nullable|numeric|min:0',
        ]);

        if ($request->type) {
            $exists = Material::query()->where('name', $request->name)
                ->where('type', $request->type)
                ->exists();

            if ($exists) {
                return back()->withInput()->with('error', 'Barang dengan nama dan tipe yang sama sudah ada.');
            }
        } else {
            $exists = Material::query()->where('name', $request->name)
                ->whereNull('type')
                ->exists();

            if ($exists) {
                return back()->withInput()->with('error', 'Barang dengan nama ini sudah ada.');
            }
        }

        $code = $this->codeService->generateCode($request->name, $request->type);

        Material::create([
            'name' => $request->name,
            'type' => $request->type,
            'unit' => ucfirst(strtolower($request->unit)),
            'code' => $code,
            'is_dimension' => $request->has('is_dimension'),
            'length' => $request->length ?? 0,
            'width' => $request->width ?? 0,
            'thickness' => $request->thickness ?? 0,
            'current_qty' => 0,
            'avg_price' => 0,
        ]);

        return redirect()->route('materials.index')->with('success', 'Material berhasil ditambahkan dengan kode: ' . $code);
    }

    public function edit(Material $material)
    {
        return view('materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'nullable|string|max:255',
            'unit' => 'required|string|max:50',
            'is_dimension' => 'nullable|boolean',
            'length' => 'nullable|numeric|min:0',
            'width' => 'nullable|numeric|min:0',
            'thickness' => 'nullable|numeric|min:0',
        ]);

        if ($request->type) {
            $exists = Material::query()->where('name', $request->name)
                ->where('type', $request->type)
                ->where('id', '!=', $material->id)
                ->exists();

            if ($exists) {
                return back()->withInput()->with('error', 'Bahan baku dengan nama dan tipe yang sama sudah ada.');
            }
        } else {
            $exists = Material::query()->where('name', $request->name)
                ->whereNull('type')
                ->where('id', '!=', $material->id)
                ->exists();

            if ($exists) {
                return back()->withInput()->with('error', 'Bahan baku dengan nama ini sudah ada.');
            }
        }

        $material->update([
            'name' => $request->name,
            'type' => $request->type,
            'unit' => ucfirst(strtolower($request->unit)),
            'is_dimension' => $request->has('is_dimension'),
            'length' => $request->length ?? 0,
            'width' => $request->width ?? 0,
            'thickness' => $request->thickness ?? 0,
        ]);

        return redirect()->route('materials.index')->with('success', 'Material berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        if ($material->current_qty > 0) {
            return back()->with('error', 'Tidak bisa menghapus material yang masih memiliki stok.');
        }

        if ($material->stockMovements()->exists()) {
            return back()->with('error', 'Tidak bisa menghapus material yang sudah memiliki pergerakan stok.');
        }

        Material::destroy($material->id);

        return redirect()->route('materials.index')->with('success', 'Material berhasil dihapus.');
    }

    public function export(Request $request)
    {
        return Excel::download(new MaterialExport($request->start_date, $request->end_date), 'Daftar_Bahan_Baku_' . now()->format('Y-m-d_His') . '.xlsx');
    }
}
