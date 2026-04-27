<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'complaint_number',
        'category',
        'priority',
        'title',
        'description',
        'audio_path',
        'image_path',
        'extra_images',
        'status',
        'assigned_to',
        'rating',
        'submitted_at',
        'resolved_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'resolved_at' => 'datetime',
        'extra_images' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages()
    {
        return $this->hasMany(ComplaintMessage::class);
    }

    public function getStatusColor()
    {
        return match($this->status) {
            'pending' => 'yellow',
            'in_progress' => 'blue',
            'resolved' => 'green',
            'rejected' => 'red',
            default => 'gray',
        };
    }
}
