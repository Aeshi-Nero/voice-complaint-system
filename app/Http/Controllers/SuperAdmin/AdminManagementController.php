<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminManagementController extends Controller
{
    public function index()
    {
        $admins = User::whereIn('role', ['admin', 'superadmin'])->paginate(10);
        return view('dashboard.superadmin.admins.index', compact('admins'));
    }

    public function create()
    {
        return view('dashboard.superadmin.admins.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'id_number' => 'required|string|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'course' => 'nullable|string',
        ]);

        User::create([
            'name' => $request->name,
            'id_number' => $request->id_number,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'course' => $request->course,
        ]);

        return redirect()->route('superadmin.admins.index')->with('success', 'Administrator account created successfully.');
    }

    public function performance(User $admin)
    {
        // Calculate metrics for this specific admin
        $totalManaged = Complaint::where('assigned_to', $admin->id)->count();
        $resolvedMonth = Complaint::where('assigned_to', $admin->id)
            ->where('status', 'resolved')
            ->whereMonth('resolved_at', now()->month)
            ->count();
            
        $totalResolved = Complaint::where('assigned_to', $admin->id)
            ->where('status', 'resolved')
            ->count();
            
        $efficiencyRate = $totalManaged > 0 ? round(($totalResolved / $totalManaged) * 100) : 0;

        // Mock monthly trend data for the chart
        $monthlyTrends = Complaint::where('assigned_to', $admin->id)
            ->select(DB::raw('count(*) as count'), DB::raw("DATE_FORMAT(created_at, '%b') as month"))
            ->groupBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        return view('dashboard.superadmin.admins.performance', compact(
            'admin', 
            'totalManaged', 
            'resolvedMonth', 
            'efficiencyRate',
            'monthlyTrends'
        ));
    }

    public function block(User $admin)
    {
        if ($admin->role === 'superadmin') {
            return back()->with('error', 'Cannot block a superadmin.');
        }
        $admin->update(['is_blocked' => true]);
        return back()->with('success', 'Administrator account suspended.');
    }

    public function unblock(User $admin)
    {
        $admin->update(['is_blocked' => false]);
        return back()->with('success', 'Administrator account restored.');
    }
}
