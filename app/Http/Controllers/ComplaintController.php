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

        $query = $user->complaints()->with('messages'); // Eager load to avoid N+1 if needed

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
        $totalCount = $user->complaints()->count();
        
        return view("dashboard.user.complaints-index", compact("complaints", "totalCount"));
    }

    public function dashboard()
    {
        $user = Auth::user();
        $complaints = $user->complaints()->latest()->take(4)->get();
        
        $statsRaw = $user->complaints()
            ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $stats = [
            "total" => array_sum($statsRaw),
            "pending" => $statsRaw['pending'] ?? 0,
            "in_progress" => $statsRaw['in_progress'] ?? 0,
            "resolved" => $statsRaw['resolved'] ?? 0,
            "rejected" => $statsRaw['rejected'] ?? 0,
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
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'You have reached your submission limit for today (6).'], 403);
            }
            return redirect()->route("user.dashboard")->with("error", "You have reached your submission limit for today (6).");
        }

        $request->validate([
            "category" => "required|string",
            "title" => "required|string|max:255",
            "description" => "required|string",
            "images.*" => "nullable|image|mimes:jpeg,png,jpg,gif|max:5120", // 5MB per image
            "audio" => "nullable", 
            "audio.*" => "nullable|max:10240", // 10MB per audio file
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
                
                if ($request->ajax() || $request->wantsJson()) {
                    Auth::logout();
                    return response()->json(['message' => 'Your account has been banned for 24 hours due to multiple violations of our community standards (Profanity).'], 403);
                }

                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account has been banned for 24 hours due to multiple violations of our community standards (Profanity).');
            }
            
            $user->save();
            $remainingStrikes = 3 - $user->profanity_count;
            
            $strikeMessage = ($remainingStrikes === 1) 
                ? "One more violation and you will be banned for 24 hours." 
                : "{$remainingStrikes} more violations until you are banned for 24 hours.";
            
            $errorMessage = "Your complaint contains inappropriate language. Strike {$user->profanity_count}/3. {$strikeMessage}";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $errorMessage], 422);
            }

            return back()->withErrors([
                "profanity" => $errorMessage
            ])->withInput();
        }

        $imagePaths = [];
        if ($request->hasFile("images")) {
            foreach ($request->file("images") as $image) {
                $imagePaths[] = $image->store("complaints/images", "public");
            }
        }

        $audioPaths = [];
        if ($request->hasFile("audio")) {
            $audioFiles = is_array($request->file("audio")) ? $request->file("audio") : [$request->file("audio")];
            foreach ($audioFiles as $audio) {
                $audioPaths[] = $audio->store("complaints/audio", "public");
            }
        }

        try {
            $complaint = Complaint::create([
                "user_id" => Auth::id(),
                "complaint_number" => $this->complaintNumberService->generate(),
                "category" => $request->category,
                "priority" => "Medium", // Default priority
                "title" => $request->title,
                "description" => $request->description,
                "audio_paths" => !empty($audioPaths) ? $audioPaths : null,
                "image_path" => $imagePaths[0] ?? null,
                "extra_images" => count($imagePaths) > 1 ? array_slice($imagePaths, 1) : null,
                "status" => "pending",
                "submitted_at" => now(),
            ]);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Complaint creation failed: " . $e->getMessage());
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Failed to save complaint. Please ensure the database is up to date by visiting /fix-db'], 500);
            }
            return back()->with("error", "Failed to save complaint. Please try again later.")->withInput();
        }

        // Increment user's daily count
        $user = Auth::user();
        $user->complaints_today += 1;
        $user->save();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Complaint #{$complaint->complaint_number} submitted successfully!",
                'redirect' => route("user.dashboard")
            ]);
        }

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
            "audio" => "nullable|max:10240", // Flexible audio recorded blobs
        ]);

        // Check for profanity
        if ($this->profanityService->containsProfanity($request->description) || 
            $this->profanityService->containsProfanity($request->title)) {
            
            $user = Auth::user();
            $user->profanity_count += 1;
            $user->save();
            
            $errorMessage = "Your update contains inappropriate language. Strike {$user->profanity_count}/3.";

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $errorMessage], 422);
            }

            return back()->withErrors([
                "profanity" => $errorMessage
            ])->withInput();
        }

        $data = [
            "category" => $request->category,
            "title" => $request->title,
            "description" => $request->description,
        ];

        // Handle deletions
        $deletedImages = $request->deleted_images;
        if (is_string($deletedImages)) {
            $deletedImages = json_decode($deletedImages, true) ?? [];
        }
        $deletedImages = $deletedImages ?? [];

        $currentImages = $complaint->extra_images ? array_merge([$complaint->image_path], $complaint->extra_images) : ($complaint->image_path ? [$complaint->image_path] : []);
        
        // Remove deleted images from the list
        $remainingImages = array_values(array_filter($currentImages, function($img) use ($deletedImages) {
            return !in_array($img, $deletedImages);
        }));

        // Handle audio deletions
        $deletedAudio = $request->deleted_audio;
        if (is_string($deletedAudio)) {
            $deletedAudio = json_decode($deletedAudio, true) ?? [];
        }
        $deletedAudio = $deletedAudio ?? [];

        $currentAudio = $complaint->audio_paths ?? [];
        $remainingAudio = array_values(array_filter($currentAudio, function($audio) use ($deletedAudio) {
            return !in_array($audio, $deletedAudio);
        }));

        if ($request->delete_audio) {
            $remainingAudio = [];
        }

        // Handle new images
        if ($request->hasFile("images")) {
            $newImagePaths = [];
            foreach ($request->file("images") as $image) {
                $newImagePaths[] = $image->store("complaints/images", "public");
            }
            $remainingImages = array_merge($remainingImages, $newImagePaths);
        }

        // Re-assign images
        $data["image_path"] = !empty($remainingImages) ? $remainingImages[0] : null;
        $data["extra_images"] = count($remainingImages) > 1 ? array_slice($remainingImages, 1) : null;

        // Handle new audio
        if ($request->hasFile("audio")) {
            $audioFiles = is_array($request->file("audio")) ? $request->file("audio") : [$request->file("audio")];
            foreach ($audioFiles as $audio) {
                $remainingAudio[] = $audio->store("complaints/audio", "public");
            }
        }
        $data["audio_paths"] = !empty($remainingAudio) ? $remainingAudio : null;

        $complaint->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Complaint updated successfully!',
                'complaint' => $complaint
            ]);
        }

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
