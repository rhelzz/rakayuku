<?php

namespace Database\Seeders;

use App\Models\Material;
use Illuminate\Database\Seeder;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'name' => 'Papan Kayu Jati',
                'code' => 'KAYU-001',
                'type' => 'A Grade',
                'unit' => 'meter',
                'is_dimension' => true,
                'length' => 4.00,
                'width' => 0.20,
                'thickness' => 0.02,
                'current_qty' => 0,
                'avg_price' => 0,
            ],
            [
                'name' => 'Balok Kayu Meranti',
                'code' => 'KAYU-002',
                'type' => 'Standard',
                'unit' => 'meter',
                'is_dimension' => true,
                'length' => 3.00,
                'width' => 0.10,
                'thickness' => 0.05,
                'current_qty' => 0,
                'avg_price' => 0,
            ],
            [
                'name' => 'Tali Rapia',
                'code' => 'ACC-001',
                'type' => 'Besar',
                'unit' => 'roll',
                'is_dimension' => false,
                'current_qty' => 0,
                'avg_price' => 0,
            ],
            [
                'name' => 'Lem Kayu Presto',
                'code' => 'ACC-002',
                'type' => 'DN',
                'unit' => 'kg',
                'is_dimension' => false,
                'current_qty' => 0,
                'avg_price' => 0,
            ],
            [
                'name' => 'Sekrup 2 inch',
                'code' => 'ACC-003',
                'type' => 'Black',
                'unit' => 'box',
                'is_dimension' => false,
                'current_qty' => 0,
                'avg_price' => 0,
            ],
        ];

        foreach ($materials as $material) {
            Material::create($material);
        }
    }
}
