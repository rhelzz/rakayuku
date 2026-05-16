<?php

namespace App\Traits;

use App\Models\MonthlyClosing;
use Carbon\Carbon;

trait CheckClosingPeriod
{
    /**
     * Check if a transaction date falls within a closed period.
     * Returns a redirect response with error if closed, null if open.
     */
    protected function checkClosingPeriod(?Carbon $date = null)
    {
        $date = $date ?? now();

        if (MonthlyClosing::isPeriodClosed($date)) {
            $periodLabel = $date->translatedFormat('F Y');
            return back()->withInput()->with('error', 
                "Periode $periodLabel sudah ditutup buku. Tidak bisa menambah/mengubah transaksi pada periode ini. Silakan buka kembali periode tersebut terlebih dahulu."
            );
        }

        return null;
    }
}
