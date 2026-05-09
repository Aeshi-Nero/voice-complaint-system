<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class ClaimAccountController extends Controller
{
    public function check(Request $request)
    {
        $request->validate(['id_number' => 'required']);

        $user = User::where('id_number', $request->id_number)->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'ID Number not found in institutional registry.']);
        }

        if (!$user->is_first_login) {
            return response()->json(['success' => false, 'message' => 'This account has already been claimed. Please log in normally.']);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'name' => $user->name,
                'id_number' => $user->id_number
            ]
        ]);
    }

    public function verify(Request $request)
    {
        $request->validate([
            'id_number' => 'required|exists:users,id_number',
            'contact_info' => 'required' // Email or Phone
        ]);

        $user = User::where('id_number', $request->id_number)->first();

        // In a real scenario, we would send an SMS or Email here.
        // For now, we will simulate the "Delivery" by showing the PIN in the success message
        // and updating the user's email/phone if provided.

        if (filter_var($request->contact_info, FILTER_VALIDATE_EMAIL)) {
            $user->email = $request->contact_info;
        } else {
            $user->phone_number = $request->contact_info;
        }
        
        $user->save();

        return response()->json([
            'success' => true,
            'message' => "INSTITUTIONAL ALERT: Registration synchronized. For testing, your access PIN is displayed below:",
            'pin' => $user->temporary_pin
        ]);
    }
}
