<?php

namespace App\Exports;

use App\Models\StockOpname;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;
use Carbon\Carbon;

class StockOpnameExport extends BaseExport implements FromArray, WithTitle
{
    public function __construct(StockOpname $stockOpname)
    {
        $stockOpname->load(['items.material']);

        $this->rows = [
            ['LAPORAN STOCK OPNAME - ' . $stockOpname->opname_number . ' - RAKAYUKU ERP'],
            ['No', 'Kode Barang', 'Nama Bahan', 'Satuan', 'Stok Sistem', 'Stok Aktual', 'Selisih', 'Catatan'],
        ];

        $totalSurplus = 0;
        $totalDeficit = 0;

        foreach ($stockOpname->items as $index => $item) {
            $this->rows[] = [
                $index + 1,
                $item->material->code,
                $item->material->display_name,
                $item->material->unit,
                $item->system_qty,
                $item->actual_qty,
                $item->difference,
                $item->notes ?? '-',
            ];

            if ($item->difference > 0) {
                $totalSurplus += $item->difference;
            } else {
                $totalDeficit += abs($item->difference);
            }
        }

        $this->rows[] = [];
        $this->rows[] = [
            'Tanggal Opname: ' . $stockOpname->opname_date->format('d/m/Y'),
            'Status: ' . $stockOpname->status,
            'Total Item: ' . $stockOpname->items->count(),
            'Total Surplus: +' . number_format($totalSurplus, 2),
            'Total Defisit: -' . number_format($totalDeficit, 2),
            'Catatan: ' . ($stockOpname->notes ?? '-'),
            '',
            'Export: ' . Carbon::now()->format('d F Y H:i'),
        ];
    }

    public function title(): string
    {
        return 'Stock Opname';
    }
}
