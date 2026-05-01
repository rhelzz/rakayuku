<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Customer;
use App\Services\OrderService;

class OrderSeeder extends Seeder
{
    public function run(OrderService $orderService): void
    {
        $customers = Customer::all();
        
        if ($customers->isEmpty()) {
            return;
        }

        $projects = [
            [
                'customer_id' => $customers[0]->id,
                'project_name' => 'Kitchen Set Minimalis',
                'selling_price' => 15000000,
                'dp_amount' => 5000000,
                'deadline' => now()->addDays(30),
            ],
            [
                'customer_id' => $customers[1]->id,
                'project_name' => 'Meja Kantor Industrial',
                'selling_price' => 3500000,
                'dp_amount' => 1000000,
                'deadline' => now()->addDays(14),
            ],
            [
                'customer_id' => $customers[2]->id,
                'project_name' => 'Lemari Pakaian 3 Pintu',
                'selling_price' => 7500000,
                'dp_amount' => 2000000,
                'deadline' => now()->addDays(21),
            ],
        ];

        foreach ($projects as $project) {
            $orderService->createOrder($project);
        }
    }
}
