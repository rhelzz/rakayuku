<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

use App\Models\Cashflow;

class PurchaseService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }


    public function createPurchase(array $data, array $items)
    {
        return DB::transaction(function () use ($data, $items) {
            $invoiceProof = null;
            if (isset($data['invoice_proof']) && $data['invoice_proof'] instanceof \Illuminate\Http\UploadedFile) {
                $invoiceProof = $data['invoice_proof']->store('invoices', 'public');
            }

            $totalPrice = 0;
            foreach ($items as $item) {
                $totalPrice += $item['qty'] * $item['price'];
            }

            if ($totalPrice > 0 && $totalPrice > Cashflow::currentBalance()) {
                throw new \Exception('Saldo perusahaan tidak mencukupi untuk melakukan pembelian. Sisa Saldo: ' . formatRupiah(Cashflow::currentBalance()));
            }

            $purchase = Purchase::create([
                'supplier_name' => $data['supplier_name'] ?? null,
                'invoice_number' => $data['invoice_number'] ?? null,
                'invoice_proof' => $invoiceProof,
                'purchase_date' => $data['purchase_date'] ?? now(),
                'total_price' => $totalPrice, 
            ]);

            foreach ($items as $item) {
                $material = Material::findOrFail($item['material_id']);
                $qty = $item['qty'];
                $price = $item['price'];
                $subtotal = $qty * $price;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'material_id' => $material->id,
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $this->inventoryService->addStockFromPurchase($material, $qty, $price, $purchase);
            }

            if ($totalPrice > 0) {
                $purchase->cashflow()->create([
                    'type' => 'OUT',
                    'amount' => $totalPrice,
                    'description' => 'Pembelian bahan baku ke ' . ($purchase->supplier_name ?? 'Supplier') . ' (Inv: ' . ($purchase->invoice_number ?? '-') . ')',
                ]);
            }

            return $purchase;
        });
    }
}
