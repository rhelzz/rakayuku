<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Searchable;

    protected $guarded = ['id'];

    // Status Constants
    const STATUS_PENDING = 'PENDING';
    const STATUS_IN_PRODUCTION = 'IN_PRODUCTION';
    const STATUS_DELIVERING = 'DELIVERING';
    const STATUS_UNPAID_DELIVERED = 'UNPAID_DELIVERED';
    const STATUS_FINISHED = 'FINISHED';

    // Payment Status Constants
    const PAYMENT_UNPAID = 'UNPAID';
    const PAYMENT_PARTIAL = 'PARTIAL';
    const PAYMENT_PAID = 'PAID';

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function materials()
    {
        return $this->hasMany(OrderMaterial::class);
    }

    public function productionCosts()
    {
        return $this->hasMany(ProductionCost::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Financial Calculations (Live)
     */
    public function getEstimatedMaterialCostAttribute()
    {
        return $this->materials()->sum('subtotal');
    }

    public function getEstimatedAdditionalCostAttribute()
    {
        return $this->productionCosts()->sum('amount');
    }

    public function getLiveTotalCostAttribute()
    {
        return $this->estimated_material_cost + $this->estimated_additional_cost;
    }

    public function getEstimatedProfitAttribute()
    {
        return $this->selling_price - $this->live_total_cost;
    }

    public function getProfitMarginAttribute()
    {
        if ($this->selling_price <= 0) return 0;
        return ($this->estimated_profit / $this->selling_price) * 100;
    }

    public function getTotalPaidAttribute()
    {
        return $this->payments()->sum('amount');
    }

    public function getRemainingPaymentAttribute()
    {
        return $this->selling_price - $this->total_paid;
    }
}
