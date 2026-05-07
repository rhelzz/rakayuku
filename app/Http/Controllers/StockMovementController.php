<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Exports\StockMovementExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['material', 'reference'])
            ->search($request->search, ['material.name'])
            ->dateRange($request->date_range, $request->start_date, $request->end_date)
            ->when($request->type, fn($q) => $q->where('type', $request->type))
            ->when($request->material_id, fn($q) => $q->where('material_id', $request->material_id))
            ->sort($request->sort_field ?? 'created_at', $request->sort_dir ?? 'desc');

        $movements = $query->paginate(20)->withQueryString();
        
        $materials = \App\Models\Material::orderBy('name')->get();

        return view('inventory.movements', compact('movements', 'materials'));
    }

    public function export(Request $request)
    {
        return Excel::download(new StockMovementExport($request->start_date, $request->end_date), 'Laporan_Pergerakan_Stok_' . now()->format('Y-m-d_His') . '.xlsx');
    }
}
