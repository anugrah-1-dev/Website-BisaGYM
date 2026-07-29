<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnackTransaction extends Model
{
    protected $guarded = [];

    public function details()
    {
        return $this->hasMany(SnackTransactionDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
