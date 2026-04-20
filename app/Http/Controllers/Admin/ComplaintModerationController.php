<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ComplaintModerationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $course = $request->get('course', 'all');
        
        $user = auth()->user();
        
        // Mark as viewed
        if ($course === 'all') {
            $user->last_complaints_viewed_at = now();
        } else {
            $viewedCats = $user->viewed_categories_at ?? [];
            $viewedCats[$course] = now()->toDateTimeString();
            $user->viewed_categories_at = $viewedCats;
        }
        $user->save();
        
        $query = Complaint::with('user');
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($course !== 'all') {
            $query->whereHas('user', function($q) use ($course) {
                $q->where('course', $course);
            });
        }
        
        $complaints = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('dashboard.admin.complaints.index', compact('complaints', 'status', 'course'));
    }

    public function show(Complaint $complaint)
    {
        $complaint->load('messages.user');
        $userComplaints = Complaint::where('user_id', $complaint->user_id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        $rejectionCount = Complaint::where('user_id', $complaint->user_id)
            ->where('status', 'rejected')
            ->count();
            
        return view('dashboard.admin.complaints.show', compact('complaint', 'userComplaints', 'rejectionCount'));
    }

    public function update(Request $request, Complaint $complaint)
    {
        $request->validate([
            'action' => 'required|in:accept,reject',
            'admin_notes' => 'nullable|string',
        ]);
        
        if ($request->action === 'accept') {
            $complaint->update([
                'status' => 'in_progress',
                'admin_notes' => $request->admin_notes,
            ]);
            
            $message = 'Complaint accepted and marked as in progress.';
        } else {
            $complaint->update([
                'status' => 'rejected',
                'admin_notes' => $request->admin_notes,
                'resolved_at' => Carbon::now('Asia/Manila'),
            ]);
            
            // Check if user should be blocked after 3 rejections
            $rejectionCount = Complaint::where('user_id', $complaint->user_id)
                ->where('status', 'rejected')
                ->count();
                
            if ($rejectionCount >= 3) {
                $complaint->user->update(['is_blocked' => true]);
                $message = 'Complaint rejected. User has been blocked due to 3 rejections.';
            } else {
                $message = 'Complaint rejected.';
            }
        }
        
        return redirect()->route('admin.complaints.show', $complaint)->with('success', $message);
    }

    public function resolve(Complaint $complaint)
    {
        $complaint->update([
            'status' => 'resolved',
            'resolved_at' => Carbon::now('Asia/Manila'),
        ]);
        
        return redirect()->route('admin.complaints.show', $complaint)->with('success', 'Complaint marked as resolved.');
    }

    public function blockUser(User $user)
    {
        $user->update(['is_blocked' => true]);
        
        return back()->with('success', 'User has been blocked.');
    }

    public function unblockUser(User $user)
    {
        $user->update([
            'is_blocked' => false,
            'banned_until' => null,
            'profanity_count' => 0
        ]);
        
        return back()->with('success', 'User access has been restored.');
    }

    public function quickBan(User $user)
    {
        $user->update([
            'banned_until' => Carbon::now()->addDay(), // 24 hours from now
        ]);
        
        return back()->with('success', "User has been banned for 24 hours.");
    }
}