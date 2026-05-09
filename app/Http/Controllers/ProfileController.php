<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|digits:11',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'current_password' => 'nullable|required_with:password|string',
            'password' => 'nullable|string|min:8|confirmed',
            'course' => 'nullable|string|max:255',
        ]);

        if ($request->filled('password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'The provided password does not match your current password.']);
            }
            $user->password = Hash::make($request->password);
        }

        $user->name = $request->username;
        $user->email = $request->email;
        $user->phone_number = $request->phone_number;

        if (!$user->isAdmin()) {
            $user->course = $request->course;
        }

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->profile_image) {
                Storage::disk('public')->delete($user->profile_image);
            }

            $path = $request->file('profile_image')->store('profiles', 'public');
            $user->profile_image = $path;
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
