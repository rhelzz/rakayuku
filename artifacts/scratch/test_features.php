<?php

use App\Models\Material;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Purchase;
use App\Models\Cashflow;
use App\Services\PurchaseService;
use App\Services\OrderService;
use App\Services\ProductionService;

try {
    echo "--- STARTING UNIT TESTING ---\n";

    // 1. Initial Setup: Create Cashflow Initial Balance
    Cashflow::create(['type' => 'INITIAL', 'amount' => 10000000, 'description' => 'Saldo Awal Testing']);
    echo "Initial Balance: " . Cashflow::currentBalance() . "\n";

    // 2. Material Testing
    $material = Material::create([
        'name' => 'Kayu Jati Test ' . uniqid(),
        'code' => 'KJT-' . rand(100, 999),
        'unit' => 'Meter',
        'current_qty' => 0,
        'avg_price' => 0
    ]);
    echo "Material Created: {$material->name}\n";

    // 3. Purchase Testing (Stock In)
    $purchaseService = app(PurchaseService::class);
    $purchase = $purchaseService->createPurchase([
        'supplier_name' => 'Supplier A',
        'purchase_date' => now(),
        'is_credit' => false
    ], [
        ['material_id' => $material->id, 'qty' => 10, 'price' => 100000]
    ]);
    $material->refresh();
    echo "Purchase Total: {$purchase->total_price}\n";
    echo "Stock after Purchase: {$material->current_qty}\n";
    echo "Avg Price after Purchase: {$material->avg_price}\n";
    echo "Cash Balance after Purchase: " . Cashflow::currentBalance() . "\n";

    // 4. Order Testing (Sales)
    $orderService = app(OrderService::class);
    $customer = Customer::create([
        'name' => 'Customer Test ' . uniqid(), 
        'phone' => '0812' . rand(1111, 9999),
        'address' => 'Jl. Testing No. 123'
    ]);
    $order = $orderService->createOrder([
        'customer_id' => $customer->id,
        'project_name' => 'Meja Jati Test',
        'selling_price' => 2000000,
        'dp_amount' => 500000
    ]);
    echo "Order Created: {$order->order_number}\n";
    echo "Order Payment Status: {$order->payment_status}\n";
    echo "Cash Balance after Order DP: " . Cashflow::currentBalance() . "\n";

    // 5. Production Testing (Usage)
    $productionService = app(ProductionService::class);
    $orderService->startProduction($order);
    $productionService->addMaterialToOrder($order, $material, 2);
    $material->refresh();
    $order->refresh();
    echo "Stock after Production Usage: {$material->current_qty}\n";
    echo "Order Total Cost: {$order->total_cost}\n";
    echo "Order Profit: {$order->profit}\n";

    // 6. Residue Return Testing
    $orderMaterial = $order->materials->first();
    $productionService->addResidueToOrder($order, $orderMaterial, [
        'qty' => 0.5,
        'type' => 'REUSABLE'
    ]);
    $material->refresh();
    $order->refresh();
    echo "Stock after Residue Return: {$material->current_qty}\n";
    echo "Order Total Cost after Residue: {$order->total_cost}\n";

    // 7. Finish Order & Delivery
    $orderService->finishOrder($order);
    echo "Order Status after Finish: {$order->status}\n";

    $orderService->markAsDelivered($order);
    $order->refresh();
    echo "Order Status after Delivery: {$order->status}\n";

    // 8. Final Payment
    $orderService->payOrder($order, 1500000, 'FINAL');
    $order->refresh();
    echo "Order Payment Status after Final: {$order->payment_status}\n";
    echo "Order Final Status: {$order->status}\n";
    echo "Final Cash Balance: " . Cashflow::currentBalance() . "\n";

    echo "--- TESTING COMPLETED SUCCESSFULLY ---\n";

} catch (\Exception $e) {
    echo "ERROR DURING TESTING: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
