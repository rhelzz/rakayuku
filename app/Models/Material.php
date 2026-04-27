<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $guarded = ['id'];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
