<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('complaint.{complaintId}', function ($user, $complaintId) {
    $complaint = \App\Models\Complaint::find($complaintId);
    if (!$complaint) return false;
    
    // Admin can see all, user can only see their own
    return $user->isAdmin() || $user->id === $complaint->user_id;
});

Broadcast::channel('poll.{pollId}', function ($user, $pollId) {
    return true; // Public channel for poll results
});
