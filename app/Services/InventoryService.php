<?php

namespace App\Services;

use App\Models\Material;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Exception;

class InventoryService
{
    /**
     * Add stock from purchase and calculate Moving Average HPP
     */
    public function addStockFromPurchase(Material $material, float $qty, float $newPrice, ?object $reference = null)
    {
        return DB::transaction(function () use ($material, $qty, $newPrice, $reference) {
            // Lock the material for update to prevent race conditions
            $material = Material::lockForUpdate()->find($material->id);
            
            $oldQty = $material->current_qty;
            $oldPrice = $material->avg_price;
            
            $totalQty = $oldQty + $qty;
            
            if ($totalQty > 0) {
                // Moving Average Calculation: ((OldQty * OldAvgPrice) + (NewQty * NewPrice)) / TotalQty
                $avgPrice = (($oldQty * $oldPrice) + ($qty * $newPrice)) / $totalQty;
            } else {
                $avgPrice = $newPrice;
            }

            // Update material master data
            $material->update([
                'current_qty' => $totalQty,
                'avg_price' => $avgPrice,
            ]);

            // Record movement
            StockMovement::create([
                'material_id' => $material->id,
                'type' => 'IN',
                'qty' => $qty,
                'price_snapshot' => $newPrice,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
            ]);

            return $material;
        });
    }

    /**
     * Reduce stock for production usage
     */
    public function reduceStockForProduction(Material $material, float $qty, ?object $reference = null)
    {
        return DB::transaction(function () use ($material, $qty, $reference) {
            $material = Material::lockForUpdate()->find($material->id);

            if ($material->current_qty < $qty) {
                throw new Exception("Stock tidak mencukupi untuk bahan: {$material->name}");
            }

            $material->update([
                'current_qty' => $material->current_qty - $qty,
            ]);

            // Record movement with snapshot of avg_price at the time of usage
            StockMovement::create([
                'material_id' => $material->id,
                'type' => 'OUT',
                'qty' => $qty,
                'price_snapshot' => $material->avg_price,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
            ]);

            return $material;
        });
    }

    /**
     * Correct stock (usually for reversing production usage or manual adjustment)
     */
    public function correctStock(Material $material, float $qty, string $type = 'ADJUSTMENT', ?object $reference = null)
    {
        return DB::transaction(function () use ($material, $qty, $type, $reference) {
            $material = Material::lockForUpdate()->find($material->id);

            $newQty = $material->current_qty + $qty;

            if ($newQty < 0) {
                throw new Exception("Koreksi stok akan menghasilkan nilai negatif for: {$material->name}");
            }

            $material->update([
                'current_qty' => $newQty,
            ]);

            // Record movement
            StockMovement::create([
                'material_id' => $material->id,
                'type' => $type,
                'qty' => $qty,
                'price_snapshot' => $material->avg_price,
                'reference_type' => $reference ? get_class($reference) : null,
                'reference_id' => $reference ? $reference->id : null,
            ]);

            return $material;
        });
    }
}
