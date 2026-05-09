<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class ReceivableExport extends BaseExport implements FromArray, WithTitle
{
    public function __construct($startDate = null, $endDate = null)
    {
        $query = Order::with('customer')
            ->where('payment_status', '!=', Order::PAYMENT_PAID)
            ->where('status', '!=', Order::STATUS_CANCELLED);
            
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate . ' 00:00:00');
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate . ' 23:59:59');
        }
        $orders = $query->get();

        $this->rows = [
            ['DAFTAR PIUTANG PELANGGAN - RAKAYUKU ERP'],
            ['No', 'Nomor Pesanan', 'Nama Proyek', 'Nama Pelanggan', 'Tanggal Pesanan', 'Harga Jual (IDR)', 'Sudah Dibayar (IDR)', 'Sisa Piutang (IDR)'],
        ];

        $totalReceivable = 0;

        foreach ($orders as $index => $order) {
            $this->rows[] = [
                $index + 1,
                $order->order_number,
                $order->project_name,
                $order->customer->name,
                $order->created_at->format('d/m/Y'),
                $order->selling_price,
                $order->dp_amount,
                $order->remaining_payment,
            ];

            $totalReceivable += $order->remaining_payment;
        }

        $this->rows[] = [];
        $this->rows[] = ['Total Pesanan Berpiutang: ' . $orders->count() . ' | Total Piutang: ' . number_format($totalReceivable, 0, ',', '.') . ' | Export: ' . Carbon::now()->format('d F Y H:i')];
    }

    public function title(): string
    {
        return 'Data Piutang';
    }
}
