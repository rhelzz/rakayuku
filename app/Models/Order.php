<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = ['id'];

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
}
