<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class CustomerExport extends BaseExport implements FromArray, WithTitle
{
    public function __construct($startDate = null, $endDate = null)
    {
        $query = Customer::withCount('orders');
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate . ' 00:00:00');
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate . ' 23:59:59');
        }
        $customers = $query->get();

        $this->rows = [
            ['DAFTAR PELANGGAN - RAKAYUKU ERP'],
            ['No', 'Nama Klien', 'Email', 'Telepon', 'Alamat', 'Kota', 'Total Proyek', 'Tanggal Terdaftar'],
        ];

        foreach ($customers as $index => $customer) {
            $this->rows[] = [
                $index + 1,
                $customer->name,
                $customer->email ?? '-',
                $customer->phone,
                $customer->address,
                $customer->city ?? '-',
                $customer->orders_count,
                $customer->created_at->format('d/m/Y H:i'),
            ];
        }

        $this->rows[] = [];
        $this->rows[] = ['Total Pelanggan: ' . $customers->count() . ' | Tanggal Export: ' . Carbon::now()->format('d F Y H:i')];
    }

    public function title(): string
    {
        return 'Data Pelanggan';
    }
}
