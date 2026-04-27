<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view("auth.login");
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            "id_number" => "required|string",
            "password" => "required|string",
        ]);

        $remember = $request->has('remember');

        if (Auth::attempt(["id_number" => $request->id_number, "password" => $request->password], $remember)) {
            $request->session()->regenerate();
            
            if (Auth::user()->role === "superadmin") {
                return redirect("/superadmin/dashboard");
            }

            if (Auth::user()->role === "admin") {
                return redirect("/admin/dashboard");
            }
            
            return redirect("/user/dashboard");
        }

        return back()->withErrors([
            "id_number" => "Invalid credentials",
        ]);
    }

    public function forgotPassword(Request $request)
    {
        $request->validate([
            'contact' => 'required'
        ]);

        $user = \App\Models\User::where('email', $request->contact)
            ->orWhere('phone_number', $request->contact)
            ->first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'No account found with this contact information.']);
        }

        // Generate a random 6-digit password
        $newPassword = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $user->password = \Illuminate\Support\Facades\Hash::make($newPassword);
        $user->save();

        // In a production environment, you would send an actual email/SMS here.
        // For this system, we will simulate it.

        return response()->json([
            'success' => true, 
            'message' => "INSTITUTIONAL ALERT: Identity verified. For testing, your new password is displayed below:",
            'new_password' => $newPassword
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect("/login");
    }
}
