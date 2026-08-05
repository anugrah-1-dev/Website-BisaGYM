<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SnackRestock extends Model
{
    protected $guarded = [];

    protected $casts = [
        'restock_date' => 'datetime',
    ];

    public function snack()
    {
        return $this->belongsTo(Snack::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }
}
