<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class ProductionCost extends Model
{
    use Searchable;

    protected $guarded = ['id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
