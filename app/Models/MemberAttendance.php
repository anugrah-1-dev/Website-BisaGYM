<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MemberAttendance extends Model
{
    protected $guarded = [];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
