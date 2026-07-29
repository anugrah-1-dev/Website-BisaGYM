<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberTransaction extends Model
{
    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function package()
    {
        return $this->belongsTo(GymPackage::class, 'gym_package_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
