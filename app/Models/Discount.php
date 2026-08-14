<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'percentage',
        'is_active',
    ];

    /**
     * Get the gym packages that belong to this discount.
     */
    public function gymPackages(): BelongsToMany
    {
        return $this->belongsToMany(GymPackage::class, 'discount_gym_package');
    }
}
