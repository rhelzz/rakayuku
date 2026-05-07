<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            $sellingPrice = $data['selling_price'] ?? 0;
            $dpAmount = $data['dp_amount'] ?? 0;

            if ($dpAmount > $sellingPrice) {
                throw new Exception('Uang muka (DP) tidak boleh melebihi harga jual.');
            }

            $order = Order::create([
                'customer_id' => $data['customer_id'],
                'order_number' => $data['order_number'] ?? $this->generateOrderNumber($data['customer_id']),
                'project_name' => $data['project_name'],
                'project_description' => $data['project_description'] ?? null,
                'deadline' => $data['deadline'] ?? null,
                'selling_price' => $sellingPrice,
                'dp_amount' => $dpAmount,
                'status' => Order::STATUS_PENDING,
                'payment_status' => $dpAmount >= $sellingPrice ? Order::PAYMENT_PAID : ($dpAmount > 0 ? Order::PAYMENT_PARTIAL : Order::PAYMENT_UNPAID),
            ]);

            $order->customer->increment('orders_count');

            if ($dpAmount > 0) {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $dpAmount,
                    'type' => Payment::TYPE_DP,
                    'payment_date' => now(),
                    'notes' => 'DP awal saat pembuatan pesanan',
                ]);
            }

            return $order;
        });
    }

    public function updateOrder(Order $order, array $data)
    {
        if (!$order->isEditable()) {
            throw new Exception('Pesanan hanya bisa diedit saat status PENDING.');
        }

        return DB::transaction(function () use ($order, $data) {
            $sellingPrice = $data['selling_price'] ?? $order->selling_price;

            $totalPaid = $order->payments()->sum('amount');
            $paymentStatus = $totalPaid >= $sellingPrice 
                ? Order::PAYMENT_PAID 
                : ($totalPaid > 0 ? Order::PAYMENT_PARTIAL : Order::PAYMENT_UNPAID);

            $order->update([
                'project_name' => $data['project_name'] ?? $order->project_name,
                'project_description' => $data['project_description'] ?? $order->project_description,
                'deadline' => $data['deadline'] ?? $order->deadline,
                'selling_price' => $sellingPrice,
                'payment_status' => $paymentStatus,
                'profit' => $sellingPrice - $order->total_cost,
            ]);

            return $order;
        });
    }

    public function cancelOrder(Order $order, string $reason = null)
    {
        if (!$order->isCancellable()) {
            throw new Exception('Pesanan hanya bisa dibatalkan saat status PENDING atau PRODUKSI.');
        }

        return DB::transaction(function () use ($order, $reason) {
            if ($order->status === Order::STATUS_IN_PRODUCTION) {
                $inventoryService = app(InventoryService::class);
                
                foreach ($order->materials as $orderMaterial) {
                    $inventoryService->correctStock(
                        $orderMaterial->material,
                        $orderMaterial->qty_used,
                        'ADJUSTMENT',
                        $order
                    );
                }
            }

            $order->update([
                'status' => Order::STATUS_CANCELLED,
                'cancel_reason' => $reason,
                'cancelled_at' => now(),
            ]);

            $order->customer->decrement('orders_count');

            return $order;
        });
    }

    public function startProduction(Order $order)
    {
        if ($order->status !== Order::STATUS_PENDING) {
            throw new Exception('Produksi hanya bisa dimulai dari status PENDING.');
        }

        $order->update(['status' => Order::STATUS_IN_PRODUCTION]);
        return $order;
    }

    public function finishOrder(Order $order)
    {
        if ($order->status !== Order::STATUS_IN_PRODUCTION) {
            throw new Exception('Hanya pesanan dalam status PRODUKSI yang bisa diselesaikan.');
        }

        if ($order->materials()->count() === 0) {
            throw new Exception('Tidak bisa menyelesaikan produksi. Tambahkan minimal 1 bahan baku yang digunakan.');
        }

        return DB::transaction(function () use ($order) {
            $materialCost = $order->materials()->sum('subtotal');
            $additionalCost = $order->productionCosts()->sum('amount');
            $totalCost = $materialCost + $additionalCost;
            $profit = $order->selling_price - $totalCost;

            $order->update([
                'status' => Order::STATUS_DELIVERING,
                'total_cost' => $totalCost,
                'profit' => $profit,
            ]);

            return $order;
        });
    }

    public function markAsDelivered(Order $order)
    {
        if ($order->status !== Order::STATUS_DELIVERING) {
            throw new Exception('Hanya pesanan dalam status PENGANTARAN yang bisa dikonfirmasi terkirim.');
        }

        return DB::transaction(function () use ($order) {
            $newStatus = $order->payment_status === Order::PAYMENT_PAID 
                ? Order::STATUS_FINISHED 
                : Order::STATUS_UNPAID_DELIVERED;

            $order->update(['status' => $newStatus]);
            
            return $order;
        });
    }

    public function payOrder(Order $order, float $amount, string $type = 'FINAL', string $notes = null)
    {
        return DB::transaction(function () use ($order, $amount, $type, $notes) {
            if ($order->status === Order::STATUS_CANCELLED) {
                throw new Exception('Pesanan yang sudah dibatalkan tidak bisa menerima pembayaran.');
            }

            $currentPaid = $order->payments()->sum('amount');
            $remaining = $order->selling_price - $currentPaid;

            if ($amount > $remaining) {
                throw new Exception('Jumlah pembayaran (Rp ' . number_format($amount, 0, ',', '.') . ') melebihi sisa tagihan (Rp ' . number_format($remaining, 0, ',', '.') . ').');
            }

            if ($type === Payment::TYPE_DP && $order->payments()->where('type', Payment::TYPE_DP)->exists()) {
                throw new Exception('Uang muka (DP) sudah pernah dibayarkan untuk pesanan ini.');
            }

            if ($remaining <= 0) {
                throw new Exception('Pesanan ini sudah lunas. Tidak bisa menambahkan pembayaran.');
            }

            Payment::create([
                'order_id' => $order->id,
                'amount' => $amount,
                'type' => $type,
                'payment_date' => now(),
                'notes' => $notes,
            ]);

            $totalPaid = $currentPaid + $amount;
            $status = $totalPaid >= $order->selling_price ? Order::PAYMENT_PAID : Order::PAYMENT_PARTIAL;

            $updateData = [
                'payment_status' => $status
            ];

            if ($status === Order::PAYMENT_PAID && $order->status === Order::STATUS_UNPAID_DELIVERED) {
                $updateData['status'] = Order::STATUS_FINISHED;
            }

            $order->update($updateData);

            return $order;
        });
    }

    private function generateOrderNumber($customerId): string
    {
        $customer = Customer::find($customerId);
        $date = now()->format('dmY');
        $nameParts = explode(' ', trim($customer->name));
        $firstName = strtoupper($nameParts[0]);
        
        $base = "ORDER-{$date}-{$firstName}";
        
        $orderNumber = $base;
        $counter = 1;
        
        while (Order::where('order_number', $orderNumber)->exists()) {
            $orderNumber = $base . '-' . sprintf('%02d', $counter);
            $counter++;
        }
        
        return $orderNumber;
    }
}
