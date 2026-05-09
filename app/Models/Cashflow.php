<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cashflow extends Model
{
    protected $guarded = ['id'];

    public function reference()
    {
        return $this->morphTo();
    }

    public static function currentBalance()
    {
        $in = self::whereIn('type', ['IN', 'INITIAL'])->sum('amount');
        $out = self::where('type', 'OUT')->sum('amount');
        return $in - $out;
    }
}
