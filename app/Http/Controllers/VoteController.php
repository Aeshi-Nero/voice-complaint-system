<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VoteController extends Controller
{
    public function vote(Request $request, Poll $poll)
    {
        $request->validate([
            'option_id' => 'required|exists:poll_options,id',
        ]);
        
        if (!$poll->isActive()) {
            return back()->with('error', 'This poll is no longer active.');
        }
        
        if (Auth::user()->hasVotedInPoll($poll->id)) {
            return back()->with('error', 'You have already voted in this poll.');
        }
        
        $option = $poll->options()->findOrFail($request->option_id);
        
        PollVote::create([
            'poll_id' => $poll->id,
            'user_id' => Auth::id(),
            'poll_option_id' => $option->id,
        ]);
        
        $option->increment('votes_count');
        
        return back()->with('success', 'Your vote has been recorded.');
    }
}