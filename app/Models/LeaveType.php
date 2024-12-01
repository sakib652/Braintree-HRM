<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'type_name',
        'max_leave_per_month',
        'total_leave',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
