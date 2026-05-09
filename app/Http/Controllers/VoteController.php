<?php

namespace App\Http\Controllers;

use App\Models\Poll;
use App\Models\PollVote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\PollVoteCast;

class VoteController extends Controller
{
    public function vote(Request $request, Poll $poll)
    {
        $request->validate([
            'option_id' => 'required|exists:poll_options,id',
        ]);
        
        if (!$poll->isActive()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'This poll is no longer active.'], 422);
            }
            return back()->with('error', 'This poll is no longer active.');
        }
        
        if (Auth::user()->hasVotedInPoll($poll->id)) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You have already voted in this poll.'], 422);
            }
            return back()->with('error', 'You have already voted in this poll.');
        }
        
        $option = $poll->options()->findOrFail($request->option_id);
        
        PollVote::create([
            'poll_id' => $poll->id,
            'user_id' => Auth::id(),
            'poll_option_id' => $option->id,
        ]);
        
        $option->increment('votes_count');

        // Broadcast the update
        PollVoteCast::dispatch($poll);
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Your vote has been recorded.',
                'results' => $poll->options()->pluck('votes_count', 'id')
            ]);
        }

        return back()->with('success', 'Your vote has been recorded.');
    }

    public function liveUpdate(Poll $poll)
    {
        return response()->json([
            'total_votes' => $poll->getTotalVotes(),
            'options' => $poll->options()->select('id', 'votes_count')->get()
        ]);
    }
}