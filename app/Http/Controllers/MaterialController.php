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
            'dimension_unit' => 'nullable|string|max:10',
        ]);

        $name = trim($request->name);
        $type = $request->type ? trim($request->type) : null;

        $query = Material::query()->where('name', $name);

        if ($type) {
            $query->where('type', $type);
        } else {
            $query->whereNull('type');
        }

        if ($request->has('is_dimension')) {
            $query->where('length', $request->length ?? 0)
                ->where('width', $request->width ?? 0)
                ->where('thickness', $request->thickness ?? 0)
                ->where('dimension_unit', $request->dimension_unit ?? 'm');
        }

        if ($query->exists()) {
            $msg = $type 
                ? "Barang dengan nama '$name' dan tipe '$type' sudah ada" 
                : "Barang dengan nama '$name' sudah ada";
            
            if ($request->has('is_dimension')) {
                $msg .= " dengan dimensi yang sama.";
            } else {
                $msg .= ".";
            }

            return back()->withInput()->with('error', $msg);
        }

        $code = $this->codeService->generateCode($name, $type);

        Material::create([
            'name' => $name,
            'type' => $type,
            'unit' => ucfirst(strtolower($request->unit)),
            'code' => $code,
            'is_dimension' => $request->has('is_dimension'),
            'length' => $request->length ?? 0,
            'width' => $request->width ?? 0,
            'thickness' => $request->thickness ?? 0,
            'dimension_unit' => $request->dimension_unit ?? 'm',
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
            'dimension_unit' => 'nullable|string|max:10',
        ]);

        $name = trim($request->name);
        $type = $request->type ? trim($request->type) : null;

        $query = Material::query()
            ->where('name', $name)
            ->where('id', '!=', $material->id);

        if ($type) {
            $query->where('type', $type);
        } else {
            $query->whereNull('type');
        }

        if ($request->has('is_dimension')) {
            $query->where('length', $request->length ?? 0)
                ->where('width', $request->width ?? 0)
                ->where('thickness', $request->thickness ?? 0)
                ->where('dimension_unit', $request->dimension_unit ?? 'm');
        }

        if ($query->exists()) {
            $msg = $type 
                ? "Bahan baku dengan nama '$name' dan tipe '$type' sudah ada" 
                : "Bahan baku dengan nama '$name' sudah ada";
            
            if ($request->has('is_dimension')) {
                $msg .= " dengan dimensi yang sama.";
            } else {
                $msg .= ".";
            }

            return back()->withInput()->with('error', $msg);
        }

        $material->update([
            'name' => $name,
            'type' => $type,
            'unit' => ucfirst(strtolower($request->unit)),
            'is_dimension' => $request->has('is_dimension'),
            'length' => $request->length ?? 0,
            'width' => $request->width ?? 0,
            'thickness' => $request->thickness ?? 0,
            'dimension_unit' => $request->dimension_unit ?? 'm',
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
