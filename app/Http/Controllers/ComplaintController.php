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
        $query = Auth::user()->complaints();

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        if ($request->has('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('complaint_number', 'like', "%{$request->search}%");
            });
        }

        $complaints = $query->latest()->paginate(10);
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
        
        return view("dashboard.user.polls", compact("active_polls", "closed_polls"));
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
            "priority" => "required|string",
            "title" => "required|string|max:255",
            "description" => "required|string",
            "image" => "nullable|image|max:5120", // 5MB
        ]);

        // Check for profanity
        if ($this->profanityService->containsProfanity($request->description) || 
            $this->profanityService->containsProfanity($request->title)) {
            return back()->withErrors(["profanity" => "Your complaint contains inappropriate language."])->withInput();
        }

        $imagePath = null;
        if ($request->hasFile("image")) {
            $imagePath = $request->file("image")->store("complaints", "public");
        }

        $complaint = Complaint::create([
            "user_id" => Auth::id(),
            "complaint_number" => $this->complaintNumberService->generate(),
            "category" => $request->category,
            "priority" => $request->priority,
            "title" => $request->title,
            "description" => $request->description,
            "image_path" => $imagePath,
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
        return view("dashboard.user.complaint-detail", compact("complaint"));
    }
}
