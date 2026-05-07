<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use Searchable;

    protected $guarded = ['id'];

    protected $casts = [
        'deadline' => 'date',
        'selling_price' => 'decimal:2',
        'dp_amount' => 'decimal:2',
        'total_cost' => 'decimal:2',
        'profit' => 'decimal:2',
        'cancelled_at' => 'datetime',
    ];

    const STATUS_PENDING = 'PENDING';
    const STATUS_IN_PRODUCTION = 'IN_PRODUCTION';
    const STATUS_DELIVERING = 'DELIVERING';
    const STATUS_UNPAID_DELIVERED = 'UNPAID_DELIVERED';
    const STATUS_FINISHED = 'FINISHED';
    const STATUS_CANCELLED = 'CANCELLED';

    const PAYMENT_UNPAID = 'UNPAID';
    const PAYMENT_PARTIAL = 'PARTIAL';
    const PAYMENT_PAID = 'PAID';

    public static function activeStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_IN_PRODUCTION,
            self::STATUS_DELIVERING,
            self::STATUS_UNPAID_DELIVERED,
        ];
    }

    public static function terminalStatuses(): array
    {
        return [
            self::STATUS_FINISHED,
            self::STATUS_CANCELLED,
        ];
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isCancellable(): bool
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_IN_PRODUCTION,
        ]);
    }

    public function isPayable(): bool
    {
        return $this->payment_status !== self::PAYMENT_PAID
            && $this->status !== self::STATUS_CANCELLED;
    }

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
        return max(0, $this->selling_price - $this->total_paid);
    }
}
