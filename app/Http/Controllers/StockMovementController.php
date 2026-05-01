<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use Illuminate\Http\Request;

class StockMovementController extends Controller
{
    public function index(Request $request)
    {
        $query = StockMovement::with(['material', 'reference'])->latest();

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('material_id')) {
            $query->where('material_id', $request->material_id);
        }

        $movements = $query->paginate(20)->withQueryString();
        
        // For filter dropdown
        $materials = \App\Models\Material::orderBy('name')->get();

        return view('inventory.movements', compact('movements', 'materials'));
    }
}
