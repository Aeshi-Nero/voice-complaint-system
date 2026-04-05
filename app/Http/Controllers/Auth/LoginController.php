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

        if (Auth::attempt(["id_number" => $request->id_number, "password" => $request->password])) {
            $request->session()->regenerate();
            
            if (Auth::user()->role === "admin") {
                return redirect("/admin/dashboard");
            }
            
            return redirect("/user/dashboard");
        }

        return back()->withErrors([
            "id_number" => "Invalid credentials",
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
