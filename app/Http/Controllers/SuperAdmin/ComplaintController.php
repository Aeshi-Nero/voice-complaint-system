<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\User;
use Illuminate\Http\Request;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = Complaint::with(['user', 'assignedTo']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('complaint_number', 'like', "%$search%")
                  ->orWhere('title', 'like', "%$search%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('name', 'like', "%$search%");
                  });
            });
        }

        $complaints = $query->latest()->paginate(15);
        $admins = User::where('role', 'admin')->get();

        return view('dashboard.superadmin.complaints.index', compact('complaints', 'admins', 'status'));
    }

    public function assign(Request $request, Complaint $complaint)
    {
        $request->validate([
            'admin_id' => 'required|exists:users,id'
        ]);

        $admin = User::find($request->admin_id);
        if ($admin->role !== 'admin') {
            return back()->with('error', 'Selected user is not an administrator.');
        }

        $complaint->update([
            'assigned_to' => $admin->id,
            'status' => $complaint->status === 'pending' ? 'in_progress' : $complaint->status
        ]);

        return back()->with('success', "Complaint assigned to {$admin->name} successfully.");
    }

    public function autoAssign()
    {
        $unassigned = Complaint::whereNull('assigned_to')->whereIn('status', ['pending', 'in_progress'])->get();
        $admins = User::where('role', 'admin')->get();

        if ($admins->isEmpty()) {
            return back()->with('error', 'No administrators available for assignment.');
        }

        $assignedCount = 0;
        foreach ($unassigned as $complaint) {
            // Simple load balancing: find admin with least active complaints
            $targetAdmin = User::where('role', 'admin')
                ->withCount(['complaints as active_count' => function($query) {
                    $query->whereIn('status', ['pending', 'in_progress']);
                }])
                ->orderBy('active_count', 'asc')
                ->first();

            if ($targetAdmin) {
                $complaint->update([
                    'assigned_to' => $targetAdmin->id,
                    'status' => $complaint->status === 'pending' ? 'in_progress' : $complaint->status
                ]);
                $assignedCount++;
            }
        }

        return back()->with('success', "Automatically assigned $assignedCount complaints.");
    }
}
