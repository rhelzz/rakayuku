<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name' => 'Budi Santoso',
                'email' => 'budi.santoso@email.com',
                'phone' => '081234567890',
                'address' => 'Jl. Merdeka No. 10, Jakarta Pusat',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'siti.aminah@email.com',
                'phone' => '085678901234',
                'address' => 'Perum Indah Permai Blok C/15, Tangerang',
            ],
            [
                'name' => 'Andi Wijaya',
                'email' => 'andi.wijaya@outlook.com',
                'phone' => '087712345678',
                'address' => 'Kavling Hijau No. 5, Bandung',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@gmail.com',
                'phone' => '082198765432',
                'address' => 'Apartemen Gading Nias, Kelapa Gading, Jakarta Utara',
            ],
            [
                'name' => 'Eko Prasetyo',
                'email' => 'eko.pras@perusahaan.com',
                'phone' => '089988776655',
                'address' => 'Jl. Kebon Jeruk No. 88, Jakarta Barat',
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
        }
    }
}
