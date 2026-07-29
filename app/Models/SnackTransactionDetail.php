<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnackTransactionDetail extends Model
{
    protected $guarded = [];

    public function snack()
    {
        return $this->belongsTo(Snack::class);
    }

    public function snackTransaction()
    {
        return $this->belongsTo(SnackTransaction::class);
    }
}
