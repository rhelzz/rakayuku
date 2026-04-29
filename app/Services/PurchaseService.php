<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Material;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Create Purchase (Pembelian Bahan Baku) and trigger Inventory stock-in
     */
    public function createPurchase(array $data, array $items)
    {
        return DB::transaction(function () use ($data, $items) {
            $purchase = Purchase::create([
                'supplier_name' => $data['supplier_name'] ?? null,
                'invoice_number' => $data['invoice_number'] ?? null,
                'purchase_date' => $data['purchase_date'] ?? now(),
                'total_price' => 0, // Akan dihitung ulang
            ]);

            $totalPrice = 0;

            foreach ($items as $item) {
                $material = Material::findOrFail($item['material_id']);
                $qty = $item['qty'];
                $price = $item['price'];
                $subtotal = $qty * $price;

                $totalPrice += $subtotal;

                // Catat detail pembelian
                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'material_id' => $material->id,
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                // Update stock dan HPP Moving Average melalui InventoryService
                $this->inventoryService->addStockFromPurchase($material, $qty, $price, $purchase);
            }

            // Update total pembelian
            $purchase->update(['total_price' => $totalPrice]);

            return $purchase;
        });
    }
}
