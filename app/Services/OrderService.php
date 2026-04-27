<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Exception;

class OrderService
{
    /**
     * Create new Order
     */
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            $order = Order::create([
                'customer_id' => $data['customer_id'],
                'order_number' => $data['order_number'] ?? uniqid('ORD-'),
                'description' => $data['description'] ?? null,
                'deadline' => $data['deadline'] ?? null,
                'selling_price' => $data['selling_price'] ?? 0,
                'dp_amount' => $data['dp_amount'] ?? 0,
                'status' => 'PENDING',
                'payment_status' => ($data['dp_amount'] ?? 0) > 0 ? 'PARTIAL' : 'UNPAID',
            ]);

            if (($data['dp_amount'] ?? 0) > 0) {
                Payment::create([
                    'order_id' => $order->id,
                    'amount' => $data['dp_amount'],
                    'type' => 'DP',
                    'payment_date' => now(),
                ]);
            }

            return $order;
        });
    }

    /**
     * Start Production Status
     */
    public function startProduction(Order $order)
    {
        $order->update(['status' => 'IN_PRODUCTION']);
        return $order;
    }

    /**
     * Finish Order Production and calculate Profit
     */
    public function finishOrder(Order $order)
    {
        return DB::transaction(function () use ($order) {
            // Re-calculate total cost one last time
            $materialCost = $order->materials()->sum('subtotal');
            $additionalCost = $order->productionCosts()->sum('amount');
            $totalCost = $materialCost + $additionalCost;

            $sellingPrice = $order->selling_price;
            
            // Calculate Profit
            $profit = $sellingPrice - $totalCost;

            $order->update([
                'status' => 'FINISHED',
                'total_cost' => $totalCost,
                'profit' => $profit,
            ]);

            return $order;
        });
    }

    /**
     * Pay order / Pelunasan
     */
    public function payOrder(Order $order, float $amount, string $type = 'FINAL')
    {
        return DB::transaction(function () use ($order, $amount, $type) {
            Payment::create([
                'order_id' => $order->id,
                'amount' => $amount,
                'type' => $type,
                'payment_date' => now(),
            ]);

            $totalPaid = $order->payments()->sum('amount');
            $status = $totalPaid >= $order->selling_price ? 'PAID' : 'PARTIAL';

            $order->update([
                'dp_amount' => $totalPaid, // Note: using dp_amount field to store total paid for now as per old schema but it should probably be renamed or handled via payments relation
                'payment_status' => $status
            ]);

            return $order;
        });
    }
}
