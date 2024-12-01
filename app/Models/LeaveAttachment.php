<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_leaves_id',
        'attachment_title',
        'attachment_file_name',
        'status',
    ];

    /**
     * Get the leave that the attachment belongs to.
     */
    public function leave()
    {
        return $this->belongsTo(UserLeave::class, 'users_leave_id');
    }
}
