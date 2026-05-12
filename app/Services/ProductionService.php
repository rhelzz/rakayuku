<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Material;
use App\Models\OrderMaterial;
use App\Models\ProductionCost;
use App\Models\OrderResidue;
use Illuminate\Support\Facades\DB;
use Exception;

class ProductionService
{
    protected InventoryService $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function addMaterialToOrder(Order $order, Material $material, float $qty)
    {
        return DB::transaction(function () use ($order, $material, $qty) {
            if ($order->status !== Order::STATUS_IN_PRODUCTION) {
                throw new Exception("Bahan baku hanya bisa ditambahkan saat status PRODUKSI.");
            }

            $unitPrice = $material->avg_price;
            $subtotal = $unitPrice * $qty;

            $this->inventoryService->reduceStockForProduction($material, $qty, $order);

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

    public function removeMaterialFromOrder(OrderMaterial $orderMaterial)
    {
        return DB::transaction(function () use ($orderMaterial) {
            $order = $orderMaterial->order;

            if ($order->status !== Order::STATUS_IN_PRODUCTION) {
                throw new Exception("Bahan baku hanya bisa dihapus saat status PRODUKSI.");
            }

            $this->inventoryService->correctStock(
                $orderMaterial->material,
                $orderMaterial->qty_used,
                'ADJUSTMENT',
                $order
            );

            OrderMaterial::destroy($orderMaterial->id);

            $this->updateOrderTotalCost($order);

            return $order;
        });
    }

    public function addResidueToOrder(Order $order, OrderMaterial $orderMaterial, array $data)
    {
        return DB::transaction(function () use ($order, $orderMaterial, $data) {
            if ($order->status !== Order::STATUS_IN_PRODUCTION) {
                throw new Exception("Residu hanya bisa ditambahkan saat status PRODUKSI.");
            }

            $qty = (float) $data['qty'];
            $type = $data['type'];
            $description = $data['description'] ?? null;

            $existingResidueQty = (float) OrderResidue::where('order_material_id', $orderMaterial->id)
                ->sum('qty');
            $remainingQty = (float) $orderMaterial->qty_used - $existingResidueQty;

            if ($remainingQty <= 0) {
                throw new Exception("Sisa residu untuk material ini sudah habis.");
            }

            if ($qty > $remainingQty) {
                throw new Exception("Jumlah residu tidak boleh melebihi sisa terpakai.");
            }

            $priceSnapshot = $orderMaterial->price_snapshot;
            $reductionValue = $qty * $priceSnapshot;

            if (isset($data['reduction_value'])) {
                $reductionValue = (float) $data['reduction_value'];
            }

            $residue = OrderResidue::create([
                'order_id' => $order->id,
                'material_id' => $orderMaterial->material_id,
                'order_material_id' => $orderMaterial->id,
                'type' => $type,
                'qty' => $qty,
                'price_snapshot' => $priceSnapshot,
                'reduction_value' => $reductionValue,
                'description' => $description,
            ]);

            if ($type === 'REUSABLE') {
                $this->inventoryService->addStockFromResidue($orderMaterial->material, $qty, $residue);
            }

            $this->updateOrderTotalCost($order);

            return $residue;
        });
    }

    public function removeResidue(OrderResidue $residue)
    {
        return DB::transaction(function () use ($residue) {
            $order = $residue->order;

            if ($order->status !== Order::STATUS_IN_PRODUCTION) {
                throw new Exception("Residu hanya bisa dihapus saat status PRODUKSI.");
            }

            if ($residue->type === 'REUSABLE') {
                // Tarik kembali stok yang sudah ditambahkan
                $this->inventoryService->reduceStockForProduction($residue->material, $residue->qty, $order);
            }

            OrderResidue::destroy($residue->id);

            $this->updateOrderTotalCost($order);

            return $order;
        });
    }

    public function addProductionCost(Order $order, string $type, float $amount, ?string $description = null)
    {
        return DB::transaction(function () use ($order, $type, $amount, $description) {
            if (!in_array($order->status, [Order::STATUS_IN_PRODUCTION, Order::STATUS_DELIVERING])) {
                throw new Exception("Biaya produksi hanya bisa ditambahkan saat status PRODUKSI atau PENGANTARAN.");
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

    public function removeProductionCost(ProductionCost $productionCost)
    {
        return DB::transaction(function () use ($productionCost) {
            $order = $productionCost->order;

            if (!in_array($order->status, [Order::STATUS_IN_PRODUCTION, Order::STATUS_DELIVERING])) {
                throw new Exception("Biaya produksi hanya bisa dihapus saat status PRODUKSI atau PENGANTARAN.");
            }

            ProductionCost::destroy($productionCost->id);

            $this->updateOrderTotalCost($order);

            return $order;
        });
    }

    public function updateOrderTotalCost(Order $order)
    {
        $materialCost = $order->materials()->sum('subtotal');
        $additionalCost = $order->productionCosts()->sum('amount');
        
        $residueReduction = $order->residues()
            ->whereIn('type', ['REUSABLE', 'RECYCLE'])
            ->sum('reduction_value');

        $totalCost = ($materialCost + $additionalCost) - $residueReduction;
        $profit = $order->selling_price - $totalCost;

        $order->update([
            'total_cost' => $totalCost,
            'profit' => $profit,
        ]);

        return $totalCost;
    }
}
