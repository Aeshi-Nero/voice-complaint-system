<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewComplaintMessage;

class ComplaintMessageController extends Controller
{
    public function store(Request $request, Complaint $complaint)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // Max 5MB per image
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('complaints/messages', 'public');
                $imagePaths[] = $path;
            }
        }

        $message = $complaint->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message,
            'is_admin' => Auth::user()->isAdmin(),
            'images' => !empty($imagePaths) ? $imagePaths : null,
        ]);

        // Notify the appropriate party
        if (Auth::user()->isAdmin()) {
            // Notify the student/user
            $complaint->user->notify(new NewComplaintMessage($message));
        } else {
            // Notify admin? In a real system, you might notify a group of admins
            // For now, we'll focus on notifying the user as requested
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('user'),
            ]);
        }

        return back()->with('success', 'Message sent.');
    }

    public function getMessages(Complaint $complaint)
    {
        $messages = $complaint->messages()->with('user')->get();
        return response()->json($messages);
    }
}
