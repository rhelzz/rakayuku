<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Material;

class MaterialSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'name' => 'Kayu Jati Grade A (m3)',
                'current_qty' => 15,
                'avg_price' => 4500000,
            ],
            [
                'name' => 'Kayu Mahoni (m3)',
                'current_qty' => 25,
                'avg_price' => 2800000,
            ],
            [
                'name' => 'Papan MDF 18mm (lembar)',
                'current_qty' => 50,
                'avg_price' => 185000,
            ],
            [
                'name' => 'HPL Woodgrain Finish (lembar)',
                'current_qty' => 40,
                'avg_price' => 155000,
            ],
            [
                'name' => 'Lem Kuning Super (kaleng)',
                'current_qty' => 20,
                'avg_price' => 65000,
            ],
            [
                'name' => 'Engsel Sendok Soft Close (set)',
                'current_qty' => 100,
                'avg_price' => 25000,
            ],
            [
                'name' => 'Handle Pintu Minimalis (pcs)',
                'current_qty' => 60,
                'avg_price' => 45000,
            ],
            [
                'name' => 'Cat Duco Putih 5L (pail)',
                'current_qty' => 8,
                'avg_price' => 320000,
            ],
            [
                'name' => 'Paku Tembak (F30) (box)',
                'current_qty' => 12,
                'avg_price' => 35000,
            ],
            [
                'name' => 'Kaca Bening 5mm (m2)',
                'current_qty' => 30,
                'avg_price' => 120000,
            ],
        ];

        foreach ($materials as $material) {
            Material::create($material);
        }
    }
}
