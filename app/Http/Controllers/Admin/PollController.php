<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PollController extends Controller
{
    public function index()
    {
        $polls = Poll::with('creator')->orderBy('created_at', 'desc')->paginate(10);
        return view('dashboard.admin.polls.index', compact('polls'));
    }

    public function create()
    {
        return view('dashboard.admin.polls.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'expires_at' => 'required|date|after:today',
            'options' => 'required|array|min:2|max:10',
            'options.*' => 'required|string|max:255',
        ]);
        
        $poll = Poll::create([
            'title' => $request->title,
            'description' => $request->description,
            'expires_at' => $request->expires_at,
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);
        
        foreach ($request->options as $option) {
            PollOption::create([
                'poll_id' => $poll->id,
                'option_text' => $option,
                'votes_count' => 0,
            ]);
        }
        
        return redirect()->route('admin.polls.index')->with('success', 'Poll created successfully.');
    }

    public function close(Poll $poll)
    {
        $poll->update(['status' => 'closed']);
        
        return redirect()->route('admin.polls.index')->with('success', 'Poll closed successfully.');
    }

    public function destroy(Poll $poll)
    {
        $poll->delete();
        
        return redirect()->route('admin.polls.index')->with('success', 'Poll deleted successfully.');
    }
}