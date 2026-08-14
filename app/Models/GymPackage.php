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

    /**
     * Get the discounts that apply to this gym package.
     */
    public function discounts()
    {
        return $this->belongsToMany(Discount::class, 'discount_gym_package');
    }
}
