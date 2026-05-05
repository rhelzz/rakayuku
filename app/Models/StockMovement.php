<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use Searchable;

    protected $guarded = ['id'];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function reference()
    {
        return $this->morphTo();
    }
}
