<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Poll extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'expires_at',
        'status',
        'created_by',
    ];

    protected $casts = [
        'expires_at' => 'date',
    ];

    public function options()
    {
        return $this->hasMany(PollOption::class);
    }

    public function votes()
    {
        return $this->hasMany(PollVote::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->expires_at->isFuture();
    }

    public function getTotalVotes(): int
    {
        return $this->votes()->count();
    }
}