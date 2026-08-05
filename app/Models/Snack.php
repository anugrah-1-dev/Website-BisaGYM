<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Snack extends Model
{
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(SnackTransactionDetail::class);
    }

    public function restocks()
    {
        return $this->hasMany(SnackRestock::class);
    }

    public function getTotalStockAttribute()
    {
        return ($this->stock_gudang ?? 0) + ($this->stock_kulkas ?? 0);
    }
}

