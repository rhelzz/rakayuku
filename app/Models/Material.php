<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use Searchable;

    protected $guarded = ['id'];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }
}
