<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Material extends Model
{
    use Searchable;

    protected $guarded = ['id'];

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function getStockStatusAttribute(): string
    {
        if ($this->current_qty == 0) {
            return 'KOSONG';
        } elseif ($this->current_qty < 10) {
            return 'KRITIS';
        } elseif ($this->current_qty < 50) {
            return 'RENDAH';
        }
        return 'TERSEDIA';
    }

    public function getDisplayNameAttribute(): string
    {
        $name = $this->name;
        if ($this->type) {
            $name .= ' (' . $this->type . ')';
        }
        if ($this->is_dimension) {
            $name .= ' [' . $this->dimension_string . ']';
        }
        return $name;
    }

    public function getDimensionStringAttribute(): string
    {
        if (!$this->is_dimension) return '';
        
        $parts = [];
        if ($this->length > 0) $parts[] = $this->length . 'm';
        if ($this->width > 0) $parts[] = $this->width . 'm';
        if ($this->thickness > 0) $parts[] = $this->thickness . 'm';
        
        return implode(' x ', $parts);
    }


    // Use Searchable trait for search, sort, and dateRange scopes
}
