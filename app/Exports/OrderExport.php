<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class OrderExport extends BaseExport implements FromArray, WithTitle
{
    public function __construct()
    {
        $orders = Order::with('customer')->get();

        $this->rows = [
            ['DAFTAR PESANAN/PROYEK - RAKAYUKU ERP'],
            ['No', 'Nomor Pesanan', 'Nama Proyek', 'Nama Pelanggan', 'Tanggal Pesanan', 'Status', 'Nilai Kontrak (IDR)', 'Harga Jual (IDR)', 'Margin Keuntungan'],
        ];

        $totalRevenue = 0;
        $totalMargin = 0;

        foreach ($orders as $index => $order) {
            $margin = $order->selling_price - $order->contract_price;
            $marginPercent = $order->contract_price > 0 ? ($margin / $order->contract_price * 100) : 0;

            $this->rows[] = [
                $index + 1,
                $order->order_number,
                $order->project_name,
                $order->customer->name,
                $order->created_at->format('d/m/Y'),
                strtoupper($order->status),
                $order->contract_price,
                $order->selling_price,
                round($marginPercent, 2) . '%',
            ];

            $totalRevenue += $order->selling_price;
            $totalMargin += $margin;
        }

        $this->rows[] = [];
        $this->rows[] = ['Total Pesanan: ' . $orders->count() . ' | Total Pendapatan: ' . number_format($totalRevenue, 0, ',', '.') . ' | Total Margin: ' . number_format($totalMargin, 0, ',', '.') . ' | Export: ' . Carbon::now()->format('d F Y H:i')];
    }

    public function title(): string
    {
        return 'Data Pesanan';
    }
}
