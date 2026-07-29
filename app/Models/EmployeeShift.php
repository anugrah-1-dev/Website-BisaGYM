<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmployeeShift extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'shift_type',
        'start_time',
        'end_time',
        'is_day_off',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
