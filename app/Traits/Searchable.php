<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

trait Searchable
{
    /**
     * Scope for multi-column search
     */
    public function scopeSearch(Builder $query, $search = null, array $columns = []): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search, $columns) {
            foreach ($columns as $column) {
                if (str_contains($column, '.')) {
                    $parts = explode('.', $column);
                    $relation = $parts[0];
                    $relColumn = $parts[1];
                    
                    $q->orWhereHas($relation, function ($relQ) use ($relColumn, $search) {
                        $relQ->where($relColumn, 'LIKE', "%{$search}%");
                    });
                } else {
                    $q->orWhere($column, 'LIKE', "%{$search}%");
                }
            }
        });
    }

    /**
     * Scope for date range filtering
     */
    public function scopeDateRange(Builder $query, $range = null, $startDate = null, $endDate = null, $column = 'created_at'): Builder
    {
        if (!$range && !$startDate) {
            return $query;
        }

        return match ($range) {
            'today' => $query->whereDate($column, Carbon::today()),
            'yesterday' => $query->whereDate($column, Carbon::yesterday()),
            '7_days' => $query->where($column, '>=', Carbon::now()->subDays(7)),
            '30_days' => $query->where($column, '>=', Carbon::now()->subDays(30)),
            'this_month' => $query->whereMonth($column, Carbon::now()->month)
                                 ->whereYear($column, Carbon::now()->year),
            'this_quarter' => $query->where($column, '>=', Carbon::now()->startOfQuarter()),
            '6_months' => $query->where($column, '>=', Carbon::now()->subMonths(6)),
            'this_year' => $query->whereYear($column, Carbon::now()->year),
            'custom' => $query->when($startDate, fn($q) => $q->whereDate($column, '>=', $startDate))
                              ->when($endDate, fn($q) => $q->whereDate($column, '<=', $endDate)),
            default => $query,
        };
    }

    /**
     * Scope for dynamic sorting
     */
    public function scopeSort(Builder $query, $field, $direction = 'desc'): Builder
    {
        $direction = in_array(strtolower($direction), ['asc', 'desc']) ? $direction : 'desc';

        if (!$field) {
            return $query->latest();
        }

        return $query->orderBy($field, $direction);
    }
}
