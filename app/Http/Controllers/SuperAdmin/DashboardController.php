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
        
        // 3. Average Resolution Time (Calculated in DB)
        $avgResolutionTime = Complaint::whereNotNull('resolved_at')
            ->whereNotNull('submitted_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, submitted_at, resolved_at)) as avg_hours'))
            ->first()
            ->avg_hours ?? 0;
            
        $avgResolutionTime = round($avgResolutionTime / 24, 1); // Convert hours to days

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
