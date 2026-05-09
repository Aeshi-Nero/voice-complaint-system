<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(\Illuminate\Http\Request $request)
    {
        $status = $request->get('status', 'all');

        $statsQuery = Complaint::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        $stats = [
            "pending" => $statsQuery['pending'] ?? 0,
            "in_progress" => $statsQuery['in_progress'] ?? 0,
            "resolved" => $statsQuery['resolved'] ?? 0,
            "rejected" => $statsQuery['rejected'] ?? 0,
        ];
        
        $categoryStats = Complaint::select("category", DB::raw("count(*) as total"))
            ->groupBy("category")
            ->get()
            ->pluck("total", "category")
            ->toArray();

        $departmentStats = Complaint::join('users', 'complaints.user_id', '=', 'users.id')
            ->select("users.course as department", DB::raw("count(*) as total"))
            ->whereNotNull('users.course')
            ->groupBy("users.course")
            ->orderBy('total', 'desc')
            ->get()
            ->pluck("total", "department")
            ->toArray();
            
        $query = Complaint::with("user");

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $recentComplaints = $query->latest()
            ->paginate(10);
            
        return view("dashboard.admin.dashboard", compact("stats", "categoryStats", "departmentStats", "recentComplaints", "status"));
    }
}