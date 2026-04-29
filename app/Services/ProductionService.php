<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Material;
use App\Models\OrderMaterial;
use App\Models\ProductionCost;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductionService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Use material for order production
     */
    public function addMaterialToOrder(Order $order, Material $material, int $qty)
    {
        return DB::transaction(function () use ($order, $material, $qty) {
            if ($order->status === 'FINISHED') {
                throw new Exception("Tidak bisa menambah bahan ke pesanan yang sudah selesai (FINISHED).");
            }

            // Snapshot current average price (HPP)
            $unitPrice = $material->avg_price;
            $subtotal = $unitPrice * $qty;

            // Reduce stock via InventoryService
            $this->inventoryService->reduceStockForProduction($material, $qty, $order);

            // Record material usage for the order
            $orderMaterial = OrderMaterial::create([
                'order_id' => $order->id,
                'material_id' => $material->id,
                'qty_used' => $qty,
                'price_snapshot' => $unitPrice,
                'subtotal' => $subtotal,
            ]);

            $this->updateOrderTotalCost($order);

            return $orderMaterial;
        });
    }

    /**
     * Add additional production cost (Labor, Transport, Tools, etc.)
     */
    public function addProductionCost(Order $order, string $type, float $amount, string $description = null)
    {
        return DB::transaction(function () use ($order, $type, $amount, $description) {
            if ($order->status === 'FINISHED') {
                throw new Exception("Tidak bisa menambah biaya produksi ke pesanan yang sudah selesai (FINISHED).");
            }

            $cost = ProductionCost::create([
                'order_id' => $order->id,
                'type' => $type,
                'amount' => $amount,
                'description' => $description,
            ]);

            $this->updateOrderTotalCost($order);

            return $cost;
        });
    }

    /**
     * Recalculate and update the total cost (HPP + Additional) for an order
     */
    public function updateOrderTotalCost(Order $order)
    {
        $materialCost = $order->materials()->sum('subtotal');
        $additionalCost = $order->productionCosts()->sum('amount');
        
        $totalCost = $materialCost + $additionalCost;

        $order->update([
            'total_cost' => $totalCost,
        ]);

        return $totalCost;
    }
}
