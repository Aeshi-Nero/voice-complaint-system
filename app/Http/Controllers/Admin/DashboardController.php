<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            "pending" => Complaint::where("status", "pending")->count(),
            "in_progress" => Complaint::where("status", "in_progress")->count(),
            "resolved" => Complaint::where("status", "resolved")->count(),
            "rejected" => Complaint::where("status", "rejected")->count(),
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
            
        $recentComplaints = Complaint::with("user")
            ->latest()
            ->limit(10)
            ->get();
            
        return view("dashboard.admin.dashboard", compact("stats", "categoryStats", "departmentStats", "recentComplaints"));
    }
}