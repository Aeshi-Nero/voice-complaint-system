<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Notifications\NewMessageNotification;
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
            MessageSent::dispatch($message);

            $sender = Auth::user();
            if ($sender->isAdmin()) {
                $recipient = $complaint->user;
            } else {
                $recipient = $complaint->assignedTo ?: User::whereIn('role', ['admin', 'superadmin'])->first();
            }

            if ($recipient && $recipient->id !== $sender->id) {
                $recipient->notify(new NewMessageNotification($message));
            }
        } catch (\Exception $e) {
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
        $user = Auth::user();
        if ($message->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $request->validate([
            'message' => 'nullable|string|max:5000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'audio.*' => 'nullable|mimes:webm,mp3,wav,ogg,bin|max:10240',
        ]);

        // Optional: Add profanity check
        if ($request->message) {
            $profanityService = app(\App\Services\ProfanityService::class);
            if ($profanityService->containsProfanity($request->message)) {
                $profanityUser = Auth::user();
                $profanityUser->profanity_count += 1;
                $profanityUser->save();

                return response()->json([
                    'success' => false,
                    'error' => "Your message contains inappropriate language. Strike {$profanityUser->profanity_count}/3."
                ], 422);
            }
        }

        $data = [];
        if ($request->has('message')) {
            $data['message'] = $request->message ?? '';
        }
        $data['is_edited'] = true;

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
        if ($request->boolean('delete_audio') || $request->input('delete_audio') === '1') {
            $remainingAudio = [];
        } else {
            $remainingAudio = $message->audio_paths ?? [];
        }

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

        try {
            MessageSent::dispatch($message->fresh()->load('user'));
        } catch (\Exception $e) {
            \Log::error('Broadcasting update failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    public function destroy(ComplaintMessage $message)
    {
        $user = Auth::user();
        if ($message->user_id !== $user->id && !$user->isAdmin()) {
            abort(403);
        }

        $message->delete();

        try {
            MessageSent::dispatch($message);
        } catch (\Exception $e) {
            \Log::error('Broadcasting delete failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
        ]);
    }

    public function getMessages(Request $request, Complaint $complaint)
    {
        $messages = $complaint->messages()->with('user')->withTrashed()->get();

        if ($request->query('html')) {
            $view = Auth::user()->isAdmin()
                ? 'dashboard.admin.complaints.partials.messages'
                : 'dashboard.user.partials.messages';
            return view($view, compact('complaint', 'messages'))->render();
        }

        return response()->json($messages);
    }
}
