<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'complaint_id',
        'user_id',
        'message',
        'is_admin',
        'images',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'images' => 'array',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
