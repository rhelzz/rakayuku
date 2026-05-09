<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use Searchable;

    protected $guarded = ['id'];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    const TYPE_DP = 'DP';
    const TYPE_FINAL = 'FINAL';
    const TYPE_INSTALLMENT = 'INSTALLMENT';

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            self::TYPE_DP => 'Uang Muka (DP)',
            self::TYPE_FINAL => 'Pelunasan',
            self::TYPE_INSTALLMENT => 'Cicilan',
            default => $this->type,
        };
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function cashflow()
    {
        return $this->morphOne(Cashflow::class, 'reference');
    }
}
