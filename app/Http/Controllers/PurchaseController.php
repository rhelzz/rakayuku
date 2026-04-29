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

    public function index()
    {
        $purchases = Purchase::latest('created_at')->get(['*']);
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
            'purchase_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.qty' => 'required|numeric|min:0.01',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        try {
            $this->purchaseService->createPurchase($request->only(['supplier_name', 'invoice_number', 'purchase_date']), $request->items);
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
