<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use Searchable;

    protected $guarded = ['id'];

    protected $casts = [
        'opname_date' => 'date',
        'completed_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'DRAFT';
    const STATUS_COMPLETED = 'COMPLETED';

    const SORTABLE_FIELDS = ['opname_number', 'opname_date', 'status', 'created_at'];

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    /**
     * Auto-generate opname number: SO-YYYY-MM-NNN
     * MUST be called inside DB::transaction for atomicity.
     */
    public static function generateOpnameNumber(): string
    {
        $prefix = 'SO-' . now()->format('Y-m') . '-';
        $lastOpname = self::where('opname_number', 'like', $prefix . '%')
            ->lockForUpdate()
            ->orderBy('opname_number', 'desc')
            ->first();

        if ($lastOpname) {
            $lastNumber = (int) substr($lastOpname->opname_number, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Generate a preview number (non-locked, for display only).
     */
    public static function previewOpnameNumber(): string
    {
        $prefix = 'SO-' . now()->format('Y-m') . '-';
        $lastOpname = self::where('opname_number', 'like', $prefix . '%')
            ->orderBy('opname_number', 'desc')
            ->first();

        if ($lastOpname) {
            $lastNumber = (int) substr($lastOpname->opname_number, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        return $prefix . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Get total items with differences (non-zero).
     */
    public function getTotalDifferencesAttribute(): int
    {
        return $this->items()->where('difference', '!=', 0)->count();
    }

    /**
     * Check if opname is still a draft.
     */
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Check if opname is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === self::STATUS_COMPLETED;
    }
}
