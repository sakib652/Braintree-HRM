<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'country_id',
        'name',
        'email',
        'address',
        'code',
        'phone_number',
        'logo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'company_id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function userLeaves()
    {
        return $this->hasMany(UserLeave::class, 'company_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'company_id');
    }

}
