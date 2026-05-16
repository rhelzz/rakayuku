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

    const SORTABLE_FIELDS = ['supplier_name', 'invoice_number', 'purchase_date', 'total_price', 'payment_status', 'created_at'];

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function cashflows()
    {
        return $this->morphMany(Cashflow::class, 'reference');
    }
}
