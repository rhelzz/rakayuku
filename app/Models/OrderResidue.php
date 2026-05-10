<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderResidue extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'qty' => 'decimal:2',
        'price_snapshot' => 'decimal:2',
        'reduction_value' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function orderMaterial()
    {
        return $this->belongsTo(OrderMaterial::class);
    }
}
