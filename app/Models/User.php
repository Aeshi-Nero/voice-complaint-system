<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'id_number',
        'name',
        'email',
        'password',
        'role',
        'is_blocked',
        'complaints_today',
        'last_complaint_reset',
        'course',
        'profile_image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'last_complaint_reset' => 'datetime',
    ];

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function pollVotes()
    {
        return $this->hasMany(PollVote::class);
    }

    public function canSubmitComplaint(): bool
    {
        if ($this->is_blocked) {
            return false;
        }

        return $this->complaints_today < 6;
    }

    public function getRemainingComplaints(): int
    {
        return max(0, 6 - $this->complaints_today);
    }

    public function hasVotedInPoll($pollId): bool
    {
        return PollVote::where('poll_id', $pollId)
            ->where('user_id', $this->id)
            ->exists();
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}