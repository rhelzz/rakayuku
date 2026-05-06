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
        if ($this->type) {
            return $this->name . ' (' . $this->type . ')';
        }
        return $this->name;
    }


    public function scopeSearch(Builder $query, $search = null, $columns = ['name']): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('code', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%");
        });
    }

    public function scopeSort(Builder $query, $field = 'code', $direction = 'asc'): Builder
    {
        $allowed = ['code', 'name', 'type', 'current_qty', 'avg_price', 'created_at'];
        
        if (in_array($field, $allowed)) {
            return $query->orderBy($field, $direction);
        }

        return $query->orderBy('code', 'asc');
    }

    public function scopeDateRange(Builder $query, $range = null, $startDate = null, $endDate = null, $column = 'created_at'): Builder
    {
        return $query;
    }
}
