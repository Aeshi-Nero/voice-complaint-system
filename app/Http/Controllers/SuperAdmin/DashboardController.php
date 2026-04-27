<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Admin Stats
        $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
        $totalAdmins = $admins->where('role', 'admin')->count();

        // 2. Complaint Stats
        $activeCases = Complaint::whereIn('status', ['pending', 'in_progress'])->count();
        
        // 3. Average Resolution Time
        $resolvedComplaints = Complaint::whereNotNull('resolved_at')
            ->whereNotNull('submitted_at')
            ->get();
            
        $avgResolutionTime = 0;
        if ($resolvedComplaints->count() > 0) {
            $totalHours = $resolvedComplaints->reduce(function($carry, $complaint) {
                return $carry + $complaint->submitted_at->diffInHours($complaint->resolved_at);
            }, 0);
            $avgResolutionTime = round($totalHours / $resolvedComplaints->count() / 24, 1); // Days
        }

        // 4. Departmental Breakdown (based on users who submitted)
        $deptStats = Complaint::join('users', 'complaints.user_id', '=', 'users.id')
            ->select('users.course', DB::raw('count(*) as count'))
            ->groupBy('users.course')
            ->get()
            ->pluck('count', 'course')
            ->toArray();

        // 5. System Health (Mock logic for now: percentage of non-overdue cases)
        $systemHealth = 99.8; 

        // 6. Recent Admins for the Leaderboard
        $adminsForLeaderboard = User::where('role', 'admin')
            ->withCount(['complaints as assigned_count'])
            ->withCount(['complaints as resolved_count' => function($q) {
                $q->where('status', 'resolved');
            }])
            ->get();

        return view('dashboard.superadmin.dashboard', compact(
            'admins', 
            'totalAdmins', 
            'activeCases', 
            'avgResolutionTime', 
            'deptStats', 
            'systemHealth',
            'adminsForLeaderboard'
        ));
    }
}
