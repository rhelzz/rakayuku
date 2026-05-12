<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use Searchable;

    protected $guarded = ['id'];

    const PAYMENT_UNPAID = 'UNPAID';
    const PAYMENT_PARTIAL = 'PARTIAL';
    const PAYMENT_PAID = 'PAID';

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function cashflow()
    {
        return $this->morphOne(Cashflow::class, 'reference');
    }
}
