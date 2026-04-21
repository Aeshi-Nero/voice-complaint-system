<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use App\Services\ComplaintNumberService;
use App\Services\ProfanityService;

class ComplaintController extends Controller
{
    protected $complaintNumberService;
    protected $profanityService;

    public function __construct(ComplaintNumberService $complaintNumberService, ProfanityService $profanityService)
    {
        $this->complaintNumberService = $complaintNumberService;
        $this->profanityService = $profanityService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $user->last_messages_viewed_at = now();
        $user->save();

        $query = $user->complaints();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('complaint_number', 'like', "%{$request->search}%");
            });
        }

        // Sorting
        $sort = $request->get('sort', 'newest');
        if ($sort === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $complaints = $query->paginate(10)->withQueryString();
        $totalCount = Auth::user()->complaints()->count();
        
        return view("dashboard.user.complaints-index", compact("complaints", "totalCount"));
    }

    public function dashboard()
    {
        $complaints = Auth::user()->complaints()->latest()->take(4)->get();
        $stats = [
            "total" => Auth::user()->complaints()->count(),
            "pending" => Auth::user()->complaints()->where("status", "pending")->count(),
            "in_progress" => Auth::user()->complaints()->where("status", "in_progress")->count(),
            "resolved" => Auth::user()->complaints()->where("status", "resolved")->count(),
            "rejected" => Auth::user()->complaints()->where("status", "rejected")->count(),
        ];
        
        return view("dashboard.user.dashboard", compact("complaints", "stats"));
    }

    public function polls()
    {
        $active_polls = \App\Models\Poll::where('status', 'active')->with('options')->latest()->get();
        $closed_polls = \App\Models\Poll::where('status', 'closed')->with('options')->latest()->get();
        
        $user = Auth::user();
        $user->last_poll_viewed_at = now();
        $user->save();

        return view("dashboard.user.polls", compact("active_polls", "closed_polls"));
    }

    public function pollReport(\App\Models\Poll $poll)
    {
        $poll->load('options');

        return view("dashboard.user.poll-report", compact("poll"));
    }

    public function livePollResults(\App\Models\Poll $poll)
    {
        $poll->load(['options' => function($query) {
            $query->orderBy('id');
        }]);

        return response()->json([
            'total_votes' => $poll->getTotalVotes(),
            'options' => $poll->options->map(function($option) use ($poll) {
                return [
                    'id' => $option->id,
                    'votes_count' => $option->votes_count,
                    'percentage' => $poll->getTotalVotes() > 0 ? round(($option->votes_count / $poll->getTotalVotes()) * 100, 1) : 0,
                ];
            })
        ]);
    }

    public function create()
    {
        if (!Auth::user()->canSubmitComplaint()) {
            return redirect()->route("user.dashboard")->with("error", "You have reached your submission limit for today (6).");
        }
        
        return view("dashboard.user.submit-complaint");
    }

    public function store(Request $request)
    {
        if (!Auth::user()->canSubmitComplaint()) {
            return redirect()->route("user.dashboard")->with("error", "You have reached your submission limit for today (6).");
        }

        $request->validate([
            "category" => "required|string",
            "title" => "required|string|max:255",
            "description" => "required|string",
            "images.*" => "nullable|image|mimes:jpeg,png,jpg,gif|max:5120", // 5MB per image
            "audio" => "nullable|mimes:webm,mp3,wav,ogg|max:10240", // 10MB
        ]);

        // Check for profanity
        if ($this->profanityService->containsProfanity($request->description) || 
            $this->profanityService->containsProfanity($request->title)) {
            
            $user = Auth::user();
            $user->profanity_count += 1;
            
            if ($user->profanity_count >= 3) {
                $user->banned_until = now()->addHours(24);
                $user->profanity_count = 0; // Reset after ban
                $user->save();
                
                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account has been banned for 24 hours due to multiple violations of our community standards (Profanity).');
            }
            
            $user->save();
            $remainingStrikes = 3 - $user->profanity_count;
            
            $strikeMessage = ($remainingStrikes === 1) 
                ? "One more violation and you will be banned for 24 hours." 
                : "{$remainingStrikes} more violations until you are banned for 24 hours.";
            
            return back()->withErrors([
                "profanity" => "Your complaint contains inappropriate language. Strike {$user->profanity_count}/3. {$strikeMessage}"
            ])->withInput();
        }

        $imagePaths = [];
        if ($request->hasFile("images")) {
            foreach ($request->file("images") as $image) {
                $imagePaths[] = $image->store("complaints/images", "public");
            }
        }

        $audioPath = null;
        if ($request->hasFile("audio")) {
            $audioPath = $request->file("audio")->store("complaints/audio", "public");
        }

        $complaint = Complaint::create([
            "user_id" => Auth::id(),
            "complaint_number" => $this->complaintNumberService->generate(),
            "category" => $request->category,
            "priority" => "Medium", // Default priority
            "title" => $request->title,
            "description" => $request->description,
            "audio_path" => $audioPath,
            "image_path" => $imagePaths[0] ?? null,
            "extra_images" => count($imagePaths) > 1 ? array_slice($imagePaths, 1) : null,
            "status" => "pending",
            "submitted_at" => now(),
        ]);

        // Increment user's daily count
        $user = Auth::user();
        $user->complaints_today += 1;
        $user->save();

        return redirect()->route("user.dashboard")->with("success", "Complaint #{$complaint->complaint_number} submitted successfully!");
    }

    public function show(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }
        $complaint->load('messages.user');
        return view("dashboard.user.complaint-detail", compact("complaint"));
    }

    public function edit(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id() || $complaint->status !== 'pending') {
            abort(403, "You can only edit pending complaints.");
        }

        return view("dashboard.user.edit-complaint", compact("complaint"));
    }

    public function update(Request $request, Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id() || $complaint->status !== 'pending') {
            abort(403, "You can only update pending complaints.");
        }

        $request->validate([
            "category" => "required|string",
            "title" => "required|string|max:255",
            "description" => "required|string",
            "images.*" => "nullable|image|mimes:jpeg,png,jpg,gif|max:5120",
            "audio" => "nullable|mimes:webm,mp3,wav,ogg,bin|max:10240",
        ]);

        // Check for profanity
        if ($this->profanityService->containsProfanity($request->description) || 
            $this->profanityService->containsProfanity($request->title)) {
            
            $user = Auth::user();
            $user->profanity_count += 1;
            $user->save();
            
            return back()->withErrors([
                "profanity" => "Your update contains inappropriate language. Strike {$user->profanity_count}/3."
            ])->withInput();
        }

        $data = [
            "category" => $request->category,
            "title" => $request->title,
            "description" => $request->description,
        ];

        // Handle new images
        if ($request->hasFile("images")) {
            $imagePaths = [];
            foreach ($request->file("images") as $image) {
                $imagePaths[] = $image->store("complaints/images", "public");
            }
            $data["image_path"] = $imagePaths[0];
            $data["extra_images"] = count($imagePaths) > 1 ? array_slice($imagePaths, 1) : null;
        }

        // Handle new audio
        if ($request->hasFile("audio")) {
            $data["audio_path"] = $request->file("audio")->store("complaints/audio", "public");
        }

        $complaint->update($data);

        return redirect()->route("user.complaints.index")->with("success", "Complaint updated successfully!");
    }

    public function destroy(Complaint $complaint)
    {
        if ($complaint->user_id !== Auth::id() || $complaint->status !== 'pending') {
            abort(403, "You can only delete pending complaints.");
        }

        $complaint->delete();

        return redirect()->route("user.complaints.index")->with("success", "Complaint deleted successfully!");
    }
}
