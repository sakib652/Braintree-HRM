<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLeave extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'company_id',
        'type_id',
        'other_type',
        'start_date',
        'end_date',
        'number_of_days_off',
        'reason',
        'mark',
        'leave_status',
        'checked_by',
        'remark',
        'status',
        'created_time',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class, 'type_id');
    }
}
