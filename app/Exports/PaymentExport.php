<?php

namespace App\Exports;

use App\Models\Payment;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class PaymentExport extends BaseExport implements FromArray, WithTitle
{
    public function __construct()
    {
        $payments = Payment::with('order.customer')->get();

        $this->rows = [
            ['LAPORAN PEMBAYARAN - RAKAYUKU ERP'],
            ['No', 'Tanggal Bayar', 'Nomor Pesanan', 'Nama Pelanggan', 'Jumlah Bayar (IDR)', 'Metode Bayar', 'Status', 'Keterangan'],
        ];

        $totalPayment = 0;

        foreach ($payments as $index => $payment) {
            $this->rows[] = [
                $index + 1,
                $payment->payment_date->format('d/m/Y'),
                $payment->order->order_number,
                $payment->order->customer->name,
                $payment->amount,
                ucfirst($payment->payment_method),
                ucfirst($payment->status),
                $payment->notes ?? '-',
            ];

            $totalPayment += $payment->amount;
        }

        $this->rows[] = [];
        $this->rows[] = ['Total Pembayaran: ' . $payments->count() . ' | Total Nilai: ' . number_format($totalPayment, 0, ',', '.') . ' | Export: ' . Carbon::now()->format('d F Y H:i')];
    }

    public function title(): string
    {
        return 'Data Pembayaran';
    }
}
