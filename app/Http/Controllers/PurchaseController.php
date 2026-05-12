<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Material;
use App\Services\PurchaseService;
use App\Exports\PurchaseExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

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
        $materials = Material::sort('name', 'asc')->get();
        return view('purchases.create', compact('materials'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'nullable|string|max:255',
            'invoice_number' => 'nullable|string|max:255',
            'invoice_proof' => 'nullable|image|max:3072', // Max 3MB
            'purchase_date' => 'required|date|before_or_equal:today',
            'is_credit' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.material_id' => 'required|exists:materials,id',
            'items.*.qty' => 'required|numeric|min:0.01',
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

    public function export(Request $request)
    {
        return Excel::download(new PurchaseExport($request->start_date, $request->end_date), 'Daftar_Pembelian_' . now()->format('Y-m-d_His') . '.xlsx');
    }

    public function pay(Request $request, Purchase $purchase)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:255',
        ]);

        try {
            $this->purchaseService->payPurchase($purchase, $request->amount, $request->notes);
            return back()->with('success', 'Pembayaran hutang berhasil dicatat.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
