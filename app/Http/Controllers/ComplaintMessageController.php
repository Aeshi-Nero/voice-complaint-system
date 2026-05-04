<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\NewComplaintMessage;
use App\Events\MessageSent;

class ComplaintMessageController extends Controller
{
    public function store(Request $request, Complaint $complaint)
    {
        $request->validate([
            'message' => 'nullable|string|max:5000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'audio.*' => 'nullable|mimes:webm,mp3,wav,ogg,bin|max:10240',
        ]);

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('complaints/messages', 'public');
                $imagePaths[] = $path;
            }
        }

        $audioPaths = [];
        if ($request->hasFile('audio')) {
            $audioFiles = is_array($request->file('audio')) ? $request->file('audio') : [$request->file('audio')];
            foreach ($audioFiles as $audio) {
                $audioPaths[] = $audio->store('complaints/messages/audio', 'public');
            }
        }

        $message = $complaint->messages()->create([
            'user_id' => Auth::id(),
            'message' => $request->message ?? '',
            'is_admin' => Auth::user()->isAdmin(),
            'images' => !empty($imagePaths) ? $imagePaths : null,
            'audio_paths' => !empty($audioPaths) ? $audioPaths : null,
        ]);

        try {
            // Broadcast the message
            MessageSent::dispatch($message);

            // Notify the appropriate party
            if (Auth::user()->isAdmin()) {
                // Notify the student/user
                $complaint->user->notify(new NewComplaintMessage($message));
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the request
            \Log::error('Broadcasting/Notification failed: ' . $e->getMessage());
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => $message->load('user'),
            ]);
        }

        return back()->with('success', 'Message sent.');
    }

    public function update(Request $request, ComplaintMessage $message)
    {
        if ($message->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'message' => 'nullable|string|max:5000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'audio.*' => 'nullable|mimes:webm,mp3,wav,ogg,bin|max:10240',
        ]);

        // Optional: Add profanity check
        $profanityService = app(\App\Services\ProfanityService::class);
        if ($request->message && $profanityService->containsProfanity($request->message)) {
            $user = Auth::user();
            $user->profanity_count += 1;
            $user->save();
            
            return response()->json([
                'success' => false,
                'error' => "Your message contains inappropriate language. Strike {$user->profanity_count}/3."
            ], 422);
        }

        $data = [];
        if ($request->has('message')) {
            $data['message'] = $request->message ?? '';
        }

        // Handle image deletions
        $deletedImages = $request->deleted_images;
        if (is_string($deletedImages)) {
            $deletedImages = json_decode($deletedImages, true) ?? [];
        }
        $deletedImages = $deletedImages ?? [];

        $currentImages = $message->images ?? [];
        $remainingImages = array_values(array_filter($currentImages, function($img) use ($deletedImages) {
            return !in_array($img, $deletedImages);
        }));

        // Handle audio deletions
        $deletedAudio = $request->deleted_audio;
        if (is_string($deletedAudio)) {
            $deletedAudio = json_decode($deletedAudio, true) ?? [];
        }
        $deletedAudio = $deletedAudio ?? [];

        $currentAudio = $message->audio_paths ?? [];
        $remainingAudio = array_values(array_filter($currentAudio, function($audio) use ($deletedAudio) {
            return !in_array($audio, $deletedAudio);
        }));

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $remainingImages[] = $image->store('complaints/messages', 'public');
            }
        }
        $data['images'] = !empty($remainingImages) ? $remainingImages : null;

        // Handle new audio
        if ($request->hasFile('audio')) {
            $audioFiles = is_array($request->file('audio')) ? $request->file('audio') : [$request->file('audio')];
            foreach ($audioFiles as $audio) {
                $remainingAudio[] = $audio->store('complaints/messages/audio', 'public');
            }
        }
        $data['audio_paths'] = !empty($remainingAudio) ? $remainingAudio : null;

        $message->update($data);

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function getMessages(Complaint $complaint)
    {
        $messages = $complaint->messages()->with('user')->get();
        return response()->json($messages);
    }
}
