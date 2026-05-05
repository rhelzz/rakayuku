<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Material;
use App\Services\PurchaseService;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    protected PurchaseService $purchaseService;

    public function __construct(PurchaseService $purchaseService)
    {
        $this->purchaseService = $purchaseService;
    }

    public function index(Request $request)
    {
        $purchases = Purchase::search($request->search, ['invoice_number', 'supplier_name'])
            ->dateRange($request->date_range, $request->start_date, $request->end_date, 'purchase_date')
            ->sort($request->sort_field ?? 'purchase_date', $request->sort_dir ?? 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $materials = Material::all();
        return view('purchases.create', compact('materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'invoice_proof' => 'nullable|image|max:3072', // Max 3MB
            'purchase_date' => 'required|date|before_or_equal:today',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            $this->purchaseService->createPurchase($request->all(), $request->items);
            return redirect()->route('purchases.index')->with('success', 'Pembelian berhasil dicatat dan stok telah diperbarui.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Purchase $purchase)
    {
        $purchase->load('items.material');
        return view('purchases.show', compact('purchase'));
    }
}
