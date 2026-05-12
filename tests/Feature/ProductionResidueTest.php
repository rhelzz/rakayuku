<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Material;
use App\Models\Order;
use App\Models\OrderMaterial;
use App\Models\OrderResidue;
use App\Services\InventoryService;
use App\Services\ProductionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductionResidueTest extends TestCase
{
    use RefreshDatabase;

    protected ProductionService $productionService;
    protected InventoryService $inventoryService;
    protected int $customerId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->inventoryService = app(InventoryService::class);
        $this->productionService = new ProductionService($this->inventoryService);

        $customer = Customer::create([
            'name' => 'Customer Test',
            'phone' => '08123456789',
            'address' => 'Alamat Test',
        ]);
        $this->customerId = $customer->id;
    }

    public function test_reusable_residue_reduces_hpp_and_increases_stock()
    {
        $material = Material::create([
            'name' => 'Papan Kayu',
            'code' => 'PK-001',
            'unit' => 'm',
            'current_qty' => 10,
            'avg_price' => 10000,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-001',
            'project_name' => 'Proyek Test 1',
            'customer_id' => $this->customerId,
            'status' => Order::STATUS_IN_PRODUCTION,
            'selling_price' => 500000,
        ]);

        $this->productionService->addMaterialToOrder($order, $material, 5);
        
        $orderMaterial = $order->materials->first();
        $this->productionService->addResidueToOrder($order, $orderMaterial, [
            'type' => 'REUSABLE',
            'qty' => 2,
            'description' => 'Sisa potongan bagus',
        ]);

        $order->refresh();
        $material->refresh();

        $this->assertEquals(7, $material->current_qty);
        $this->assertEquals(30000, $order->total_cost);
    }

    public function test_recycle_residue_reduces_hpp_but_not_increases_stock()
    {
        $material = Material::create([
            'name' => 'Besi Hollow',
            'code' => 'BH-001',
            'unit' => 'm',
            'current_qty' => 10,
            'avg_price' => 20000,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-002',
            'project_name' => 'Proyek Test 2',
            'customer_id' => $this->customerId,
            'status' => Order::STATUS_IN_PRODUCTION,
            'selling_price' => 1000000,
        ]);

        $this->productionService->addMaterialToOrder($order, $material, 5);
        
        $orderMaterial = $order->materials->first();
        $this->productionService->addResidueToOrder($order, $orderMaterial, [
            'type' => 'RECYCLE',
            'qty' => 2,
            'reduction_value' => 10000,
            'description' => 'Potongan kecil rongsok',
        ]);

        $order->refresh();
        $material->refresh();

        $this->assertEquals(5, $material->current_qty);
        $this->assertEquals(90000, $order->total_cost);
    }

    public function test_waste_residue_does_not_reduce_hpp_and_does_not_increase_stock()
    {
        $material = Material::create([
            'name' => 'Kain',
            'code' => 'KN-001',
            'unit' => 'm',
            'current_qty' => 10,
            'avg_price' => 15000,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-003',
            'project_name' => 'Proyek Test 3',
            'customer_id' => $this->customerId,
            'status' => Order::STATUS_IN_PRODUCTION,
            'selling_price' => 300000,
        ]);

        $this->productionService->addMaterialToOrder($order, $material, 5);
        
        $orderMaterial = $order->materials->first();
        $this->productionService->addResidueToOrder($order, $orderMaterial, [
            'type' => 'WASTE',
            'qty' => 1,
            'description' => 'Sobek',
        ]);

        $order->refresh();
        $material->refresh();

        $this->assertEquals(5, $material->current_qty);
        $this->assertEquals(75000, $order->total_cost);
    }

    public function test_cannot_add_residue_more_than_used_qty()
    {
        $material = Material::create([
            'name' => 'Papan',
            'code' => 'PK-002',
            'unit' => 'm',
            'current_qty' => 10,
            'avg_price' => 10000,
        ]);

        $order = Order::create([
            'order_number' => 'ORD-004',
            'project_name' => 'Proyek Test 4',
            'customer_id' => $this->customerId,
            'status' => Order::STATUS_IN_PRODUCTION,
            'selling_price' => 500000,
        ]);

        $this->productionService->addMaterialToOrder($order, $material, 5);
        $orderMaterial = $order->materials->first();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Jumlah residu tidak boleh melebihi sisa terpakai.");

        $this->productionService->addResidueToOrder($order, $orderMaterial, [
            'type' => 'REUSABLE',
            'qty' => 6,
        ]);
    }
}
