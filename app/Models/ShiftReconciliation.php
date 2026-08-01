<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftReconciliation extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'shift_type',
        'system_cash',
        'system_transfer',
        'real_cash',
        'real_transfer',
        'difference_cash',
        'difference_transfer',
        'notes',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'system_cash' => 'decimal:2',
            'system_transfer' => 'decimal:2',
            'real_cash' => 'decimal:2',
            'real_transfer' => 'decimal:2',
            'difference_cash' => 'decimal:2',
            'difference_transfer' => 'decimal:2',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
