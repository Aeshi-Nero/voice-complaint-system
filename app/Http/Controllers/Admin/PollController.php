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
            'option_images' => 'nullable|array',
            'option_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);
        
        $poll = Poll::create([
            'title' => $request->title,
            'description' => $request->description,
            'expires_at' => $request->expires_at,
            'status' => 'active',
            'created_by' => auth()->id(),
        ]);
        
        foreach ($request->options as $index => $optionText) {
            $imagePath = null;
            if ($request->hasFile("option_images.$index")) {
                $imagePath = $request->file("option_images.$index")->store("polls/options", "public");
            }

            PollOption::create([
                'poll_id' => $poll->id,
                'option_text' => $optionText,
                'votes_count' => 0,
                'image_path' => $imagePath,
            ]);
        }

        // Notify users through email or phone number (if both provided)
        $users = \App\Models\User::whereNotNull('email')
            ->whereNotNull('phone_number')
            ->get();
            
        foreach ($users as $user) {
            $user->notify(new \App\Notifications\NewPollNotification($poll));
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