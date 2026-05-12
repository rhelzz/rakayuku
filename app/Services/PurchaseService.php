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
                $qty = $item['qty'] ?? 0;
                // If it's a dimension-based item and piece_count/length is provided, calculate total qty
                if (isset($item['piece_count']) && isset($item['length']) && $item['piece_count'] > 0 && $item['length'] > 0) {
                    $qty = $item['piece_count'] * $item['length'];
                }
                $totalPrice += $qty * $item['price'];
            }

            $isCredit = isset($data['is_credit']) && $data['is_credit'];
            $paidAmount = $isCredit ? 0 : $totalPrice;
            $paymentStatus = $isCredit ? Purchase::PAYMENT_UNPAID : Purchase::PAYMENT_PAID;

            if (!$isCredit && $totalPrice > 0 && $totalPrice > Cashflow::currentBalance()) {
                throw new \Exception('Saldo perusahaan tidak mencukupi untuk melakukan pembelian tunai. Sisa Saldo: ' . formatRupiah(Cashflow::currentBalance()));
            }

            $purchase = Purchase::create([
                'supplier_name' => $data['supplier_name'] ?? null,
                'invoice_number' => $data['invoice_number'] ?? null,
                'invoice_proof' => $invoiceProof,
                'purchase_date' => $data['purchase_date'] ?? now(),
                'total_price' => $totalPrice, 
                'paid_amount' => $paidAmount,
                'payment_status' => $paymentStatus,
            ]);

            foreach ($items as $item) {
                $material = Material::findOrFail($item['material_id']);
                
                $pieceCount = $item['piece_count'] ?? 0;
                $length = $item['length'] ?? 0;
                $width = $item['width'] ?? 0;
                $thickness = $item['thickness'] ?? 0;
                
                $qty = $item['qty'];
                if ($pieceCount > 0 && $length > 0) {
                    $qty = $pieceCount * $length;
                }

                $price = $item['price'];
                $subtotal = $qty * $price;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'material_id' => $material->id,
                    'piece_count' => $pieceCount,
                    'length' => $length,
                    'width' => $width,
                    'thickness' => $thickness,
                    'qty' => $qty,
                    'price' => $price,
                    'subtotal' => $subtotal,
                ]);

                $this->inventoryService->addStockFromPurchase($material, $qty, $price, $purchase, [
                    'piece_count' => $pieceCount,
                    'length' => $length,
                    'width' => $width,
                ]);
            }

            if ($paidAmount > 0) {
                $purchase->cashflow()->create([
                    'type' => 'OUT',
                    'amount' => $paidAmount,
                    'description' => 'Pembayaran pembelian bahan baku ke ' . ($purchase->supplier_name ?? 'Supplier') . ' (Inv: ' . ($purchase->invoice_number ?? '-') . ')',
                ]);
            }

            return $purchase;
        });
    }

    public function payPurchase(Purchase $purchase, float $amount, ?string $notes = null)
    {
        return DB::transaction(function () use ($purchase, $amount, $notes) {
            $remaining = $purchase->total_price - $purchase->paid_amount;

            if ($amount > $remaining) {
                throw new \Exception('Jumlah pembayaran melebihi sisa hutang.');
            }

            if ($amount > Cashflow::currentBalance()) {
                throw new \Exception('Saldo perusahaan tidak mencukupi untuk melakukan pembayaran ini.');
            }

            $purchase->increment('paid_amount', $amount, []);
            
            $newPaidAmount = $purchase->paid_amount;
            if ($newPaidAmount >= $purchase->total_price) {
                $purchase->update(['payment_status' => Purchase::PAYMENT_PAID]);
            } else if ($newPaidAmount > 0) {
                $purchase->update(['payment_status' => Purchase::PAYMENT_PARTIAL]);
            }

            $purchase->cashflow()->create([
                'type' => 'OUT',
                'amount' => $amount,
                'description' => 'Pelunasan Hutang Pembelian ke ' . ($purchase->supplier_name ?? 'Supplier') . ' (Inv: ' . ($purchase->invoice_number ?? '-') . ')' . ($notes ? ' - ' . $notes : ''),
            ]);

            return $purchase;
        });
    }
}
