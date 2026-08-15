<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    protected $guarded = [];

    public function transactions()
    {
        return $this->hasMany(MemberTransaction::class);
    }

    public function attendances()
    {
        return $this->hasMany(MemberAttendance::class);
    }

    public function linkedMember()
    {
        return $this->belongsTo(Member::class, 'linked_member_id');
    }
}
