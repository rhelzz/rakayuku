<?php

namespace App\Exports;

use App\Models\Cashflow;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class CashflowExport extends BaseExport implements FromArray, WithTitle
{
    public function __construct($type = null, $startDate = null, $endDate = null)
    {
        $query = Cashflow::query()->latest();

        if ($type) {
            $query->where('type', $type);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } elseif ($startDate) {
            $query->where('created_at', '>=', $startDate . ' 00:00:00');
        } elseif ($endDate) {
            $query->where('created_at', '<=', $endDate . ' 23:59:59');
        }

        $cashflows = $query->get();

        $this->rows = [
            ['LAPORAN ARUS KAS - RAKAYUKU ERP'],
            ['No', 'Tanggal', 'Tipe Transaksi', 'Keterangan', 'Uang Masuk (IDR)', 'Uang Keluar (IDR)'],
        ];

        $totalIn = 0;
        $totalOut = 0;

        foreach ($cashflows as $index => $cf) {
            $typeLabel = $cf->type === 'IN' ? 'Uang Masuk' : ($cf->type === 'OUT' ? 'Uang Keluar' : 'Saldo Awal');
            
            $inAmount = in_array($cf->type, ['IN', 'INITIAL']) ? $cf->amount : 0;
            $outAmount = $cf->type === 'OUT' ? $cf->amount : 0;
            
            $totalIn += $inAmount;
            $totalOut += $outAmount;

            $this->rows[] = [
                $index + 1,
                $cf->created_at->format('d/m/Y H:i'),
                $typeLabel,
                $cf->description,
                $inAmount,
                $outAmount,
            ];
        }

        $this->rows[] = [];
        $this->rows[] = [
            'Total Uang Masuk: ' . number_format($totalIn, 0, ',', '.'),
            'Total Uang Keluar: ' . number_format($totalOut, 0, ',', '.'),
            'Saldo Bersih: ' . number_format($totalIn - $totalOut, 0, ',', '.'),
            '',
            '',
            'Export: ' . Carbon::now()->format('d F Y H:i')
        ];
    }

    public function title(): string
    {
        return 'Data Arus Kas';
    }
}
