<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GymPackage extends Model
{
    protected $fillable = [
        'name', 'duration', 'duration_unit', 'price', 'admin_fee',
        'category', 'max_members', 'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_members' => 'integer',
    ];
}
