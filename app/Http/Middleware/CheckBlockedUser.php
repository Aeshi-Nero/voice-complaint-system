<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBlockedUser
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            if ($user->is_blocked) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Your account has been blocked. Please contact administrator.');
            }
            
            if ($user->banned_until && $user->banned_until->isFuture()) {
                $timeleft = $user->banned_until->diffForHumans();
                Auth::logout();
                return redirect()->route('login')->with('error', "Your account is temporarily banned. Access will be restored {$timeleft}.");
            }
        }
        
        return $next($request);
    }
}