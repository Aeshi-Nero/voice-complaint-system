<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplaintMessage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'complaint_id',
        'user_id',
        'message',
        'is_admin',
        'is_edited',
        'images',
        'audio_paths',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'is_edited' => 'boolean',
        'images' => 'array',
        'audio_paths' => 'array',
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
