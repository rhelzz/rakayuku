<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    protected $guarded = ['id'];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get difference status label: MATCH / SURPLUS / DEFICIT.
     * Named explicitly to avoid collision if a 'status' column is added later.
     */
    public function getDifferenceStatusAttribute(): string
    {
        if ($this->difference == 0) return 'MATCH';
        if ($this->difference > 0) return 'SURPLUS';
        return 'DEFICIT';
    }
}
