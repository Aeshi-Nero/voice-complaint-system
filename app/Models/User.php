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
        'is_first_login',
        'temporary_pin',
        'is_blocked',
        'complaints_today',
        'profanity_count',
        'last_complaint_reset',
        'course',
        'profile_image',
        'banned_until',
        'last_poll_viewed_at',
        'last_complaints_viewed_at',
        'viewed_categories_at',
        'last_messages_viewed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_blocked' => 'boolean',
        'last_complaint_reset' => 'datetime',
        'banned_until' => 'datetime',
        'last_poll_viewed_at' => 'datetime',
        'last_complaints_viewed_at' => 'datetime',
        'viewed_categories_at' => 'array',
        'last_messages_viewed_at' => 'datetime',
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
        $this->ensureComplaintsReset();
        
        if ($this->is_blocked || ($this->banned_until && $this->banned_until->isFuture())) {
            return false;
        }

        return $this->complaints_today < 6;
    }

    public function getRemainingComplaints(): int
    {
        $this->ensureComplaintsReset();
        return max(0, 6 - $this->complaints_today);
    }

    protected function ensureComplaintsReset(): void
    {
        $now = now();
        
        if (!$this->last_complaint_reset || !$this->last_complaint_reset->isToday()) {
            $this->update([
                'complaints_today' => 0,
                'last_complaint_reset' => $now
            ]);
        }
    }

    public function hasVotedInPoll($pollId): bool
    {
        return PollVote::where('poll_id', $pollId)
            ->where('user_id', $this->id)
            ->exists();
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['admin', 'superadmin']);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }
}