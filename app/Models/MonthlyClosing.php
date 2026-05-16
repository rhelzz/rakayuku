<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MonthlyClosing extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'period' => 'date',
        'closed_at' => 'datetime',
        'snapshot' => 'array',
    ];

    const STATUS_OPEN = 'OPEN';
    const STATUS_CLOSED = 'CLOSED';

    /**
     * Check if a given date falls within a closed period.
     */
    public static function isPeriodClosed(Carbon $date): bool
    {
        $period = $date->copy()->startOfMonth()->format('Y-m-d');
        return self::where('period', $period)
            ->where('status', self::STATUS_CLOSED)
            ->exists();
    }

    /**
     * Get the closing record for a specific month.
     */
    public static function forPeriod(Carbon $date): ?self
    {
        $period = $date->copy()->startOfMonth()->format('Y-m-d');
        return self::where('period', $period)->first();
    }

    /**
     * Get the latest closed period.
     */
    public static function getLatestClosed(): ?self
    {
        return self::where('status', self::STATUS_CLOSED)
            ->orderBy('period', 'desc')
            ->first();
    }

    /**
     * Check if this closing is for the current month.
     */
    public function isCurrentMonth(): bool
    {
        return $this->period->format('Y-m') === now()->format('Y-m');
    }

    /**
     * Get formatted period label (e.g., "Mei 2026").
     */
    public function getPeriodLabelAttribute(): string
    {
        return $this->period->translatedFormat('F Y');
    }
}
