<?php

namespace App\Http\Controllers;

use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use App\Models\Material;
use App\Models\StockMovement;
use App\Exports\StockOpnameExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Maatwebsite\Excel\Facades\Excel;

class StockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'search' => 'nullable|string|max:255',
            'sort_field' => 'nullable|in:' . implode(',', StockOpname::SORTABLE_FIELDS),
            'sort_dir' => 'nullable|in:asc,desc',
        ]);

        $opnames = StockOpname::search($request->search, ['opname_number', 'notes'])
            ->sort($request->sort_field ?? 'created_at', $request->sort_dir ?? 'desc')
            ->withCount('items')
            ->paginate(15)
            ->withQueryString();

        return view('stock-opname.index', compact('opnames'));
    }

    public function create()
    {
        $materials = Material::orderBy('name')->get();
        $opnameNumber = StockOpname::previewOpnameNumber();

        return view('stock-opname.create', compact('materials', 'opnameNumber'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'opname_date' => 'required|date|before_or_equal:today',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id|distinct',
            'items.*.actual_qty' => 'required|numeric|min:0',
        ]);

        try {
            $opname = DB::transaction(function () use ($request) {
                $opname = StockOpname::create([
                    'opname_number' => StockOpname::generateOpnameNumber(),
                    'opname_date' => $request->opname_date,
                    'status' => StockOpname::STATUS_DRAFT,
                    'notes' => $request->notes,
                ]);

                $materialIds = collect($request->items)->pluck('material_id');
                $materials = Material::whereIn('id', $materialIds)->get()->keyBy('id');

                foreach ($request->items as $item) {
                    $material = $materials->get($item['material_id']);
                    if (!$material) {
                        throw new \RuntimeException("Material ID {$item['material_id']} tidak ditemukan.");
                    }

                    $systemQty = $material->current_qty;
                    $actualQty = (float) $item['actual_qty'];
                    $difference = round($actualQty - $systemQty, 2);

                    StockOpnameItem::create([
                        'stock_opname_id' => $opname->id,
                        'material_id' => $material->id,
                        'system_qty' => $systemQty,
                        'actual_qty' => $actualQty,
                        'difference' => $difference,
                        'notes' => $item['notes'] ?? null,
                    ]);
                }

                return $opname;
            });
        } catch (QueryException $e) {
            if ($e->errorInfo[1] === 1062) {
                return back()->withInput()->with('error', 'Nomor opname konflik, silakan coba lagi.');
            }
            throw $e;
        }

        return redirect()->route('stock-opname.show', $opname)
            ->with('success', 'Stock opname berhasil disimpan sebagai draft. Silakan review dan finalisasi.');
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['items.material']);
        return view('stock-opname.show', compact('stockOpname'));
    }

    public function complete(StockOpname $stockOpname)
    {
        if ($stockOpname->isCompleted()) {
            return back()->with('error', 'Stock opname ini sudah selesai difinalisasi.');
        }

        $stockOpname->load('items.material');

        DB::transaction(function () use ($stockOpname) {
            $fresh = StockOpname::where('id', $stockOpname->id)
                ->lockForUpdate()
                ->first();

            if (!$fresh || $fresh->isCompleted()) {
                return; // Already completed by concurrent request
            }

            foreach ($stockOpname->items as $item) {
                if ($item->difference != 0) {
                    StockMovement::create([
                        'material_id' => $item->material_id,
                        'type' => 'ADJUSTMENT',
                        'qty' => $item->difference,
                        'price_snapshot' => $item->material->avg_price,
                        'reference_type' => StockOpname::class,
                        'reference_id' => $stockOpname->id,
                    ]);

                    $item->material->update([
                        'current_qty' => $item->actual_qty,
                    ]);
                }
            }

            $fresh->update([
                'status' => StockOpname::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
        });

        return back()->with('success', 'Stock opname berhasil difinalisasi. Stok sistem telah disesuaikan.');
    }

    public function export(StockOpname $stockOpname)
    {
        $filename = 'Stock_Opname_' . $stockOpname->opname_number . '_' . now()->format('His') . '.xlsx';
        return Excel::download(new StockOpnameExport($stockOpname), $filename);
    }
}
