<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Customer;
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
                'order_number' => $data['order_number'] ?? $this->generateOrderNumber($data['customer_id']),
                'project_name' => $data['project_name'],
                'project_description' => $data['project_description'] ?? null,
                'deadline' => $data['deadline'] ?? null,
                'selling_price' => $data['selling_price'] ?? 0,
                'dp_amount' => $data['dp_amount'] ?? 0,
                'status' => Order::STATUS_PENDING,
                'payment_status' => ($data['dp_amount'] ?? 0) > 0 ? Order::PAYMENT_PARTIAL : Order::PAYMENT_UNPAID,
            ]);

            // Increment customer order count
            $order->customer->increment('orders_count');

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
        $order->update(['status' => Order::STATUS_IN_PRODUCTION]);
        return $order;
    }

    /**
     * Finish Order Production and transition to Delivering
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
                'status' => Order::STATUS_DELIVERING,
                'total_cost' => $totalCost,
                'profit' => $profit,
            ]);

            return $order;
        });
    }

    /**
     * Mark as delivered and decide final status based on payment
     */
    public function markAsDelivered(Order $order)
    {
        return DB::transaction(function () use ($order) {
            $newStatus = $order->payment_status === Order::PAYMENT_PAID 
                ? Order::STATUS_FINISHED 
                : Order::STATUS_UNPAID_DELIVERED;

            $order->update(['status' => $newStatus]);
            
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
            $status = $totalPaid >= $order->selling_price ? Order::PAYMENT_PAID : Order::PAYMENT_PARTIAL;

            $updateData = [
                'dp_amount' => $totalPaid,
                'payment_status' => $status
            ];

            // If it was unpaid delivered and now fully paid, mark as finished
            if ($status === Order::PAYMENT_PAID && $order->status === Order::STATUS_UNPAID_DELIVERED) {
                $updateData['status'] = Order::STATUS_FINISHED;
            }

            $order->update($updateData);

            return $order;
        });
    }
    /**
     * Generate Auto Order Number
     * Format: ORDER-DDMMYYYY-FIRSTNAME
     */
    private function generateOrderNumber($customerId): string
    {
        $customer = Customer::find($customerId);
        $date = now()->format('dmY');
        $nameParts = explode(' ', trim($customer->name));
        $firstName = strtoupper($nameParts[0]);
        
        $base = "ORDER-{$date}-{$firstName}";
        
        $orderNumber = $base;
        $counter = 1;
        
        // Ensure uniqueness by appending counter if necessary
        while (Order::where('order_number', $orderNumber)->exists()) {
            $orderNumber = $base . '-' . sprintf('%02d', $counter);
            $counter++;
        }
        
        return $orderNumber;
    }
}
