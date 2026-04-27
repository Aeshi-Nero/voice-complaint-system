<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $department = $request->get('department');
        $status = $request->get('status', 'all');

        // Online users (last 5 minutes activity in sessions table)
        $onlineUsersCount = DB::table('sessions')
            ->where('last_activity', '>=', now()->subMinutes(5)->getTimestamp())
            ->whereNotNull('user_id')
            ->distinct('user_id')
            ->count();

        // Users per department (course)
        $usersPerDepartment = User::select('course', DB::raw('count(*) as count'))
            ->where('role', 'student')
            ->groupBy('course')
            ->get();

        // Base query for all users
        $query = User::where('role', 'student');

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('id_number', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($department) {
            $query->where('course', $department);
        }

        if ($status === 'banned') {
            $query->where(function($q) {
                $q->where('is_blocked', true)
                  ->orWhere(function($sq) {
                      $sq->whereNotNull('banned_until')
                        ->where('banned_until', '>', now());
                  });
            });
        } elseif ($status === 'active') {
            $query->where('is_blocked', false)
                  ->where(function($q) {
                      $q->whereNull('banned_until')
                        ->orWhere('banned_until', '<=', now());
                  });
        }

        $allUsers = $query->latest()->paginate(15)->withQueryString();

        // Keep banned users for the stats/sidebar if needed, but the list will be allUsers
        $bannedUsersCount = User::where('is_blocked', true)
            ->orWhere(function($query) {
                $query->whereNotNull('banned_until')
                      ->where('banned_until', '>', now());
            })
            ->count();

        return view('users-management', compact(
            'onlineUsersCount', 
            'usersPerDepartment', 
            'allUsers', 
            'bannedUsersCount',
            'search',
            'department',
            'status'
        ));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $imported = 0;

        if ($extension === 'json') {
            $data = json_decode(file_get_contents($file->getRealPath()), true);
            if ($data) {
                foreach ($data as $userData) {
                    $this->processUser($userData);
                    $imported++;
                }
            }
        } elseif ($extension === 'csv') {
            if (($handle = fopen($file->getRealPath(), "r")) !== FALSE) {
                $headers = fgetcsv($handle, 1000, ","); 
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    $this->processUser([
                        'id_number' => $data[0] ?? null,
                        'name' => $data[1] ?? 'Unknown',
                        'email' => $data[2] ?? (($data[0] ?? uniqid()) . '@example.com'),
                        'course' => $data[3] ?? null,
                        'role' => $data[4] ?? 'student',
                    ]);
                    $imported++;
                }
                fclose($handle);
            }
        } else {
            return back()->with('error', 'Unsupported file format. Please use JSON or CSV.');
        }

        return back()->with('success', "Successfully imported $imported users.");
    }

    private function processUser($data)
    {
        if (isset($data['id_number'])) {
            User::updateOrCreate(
                ['id_number' => $data['id_number']],
                [
                    'name' => $data['name'] ?? 'Unknown',
                    'email' => $data['email'] ?? ($data['id_number'] . '@example.com'),
                    'password' => isset($data['password']) ? Hash::make($data['password']) : Hash::make($data['id_number']),
                    'role' => $data['role'] ?? 'student',
                    'course' => $data['course'] ?? null,
                    'is_blocked' => false,
                    'is_first_login' => true,
                    'temporary_pin' => $data['password'] ?? substr($data['id_number'], -4),
                ]
            );
        }
    }

    public function blockUser(User $user)
    {
        $user->update(['is_blocked' => true]);
        return back()->with('success', 'User account permanently blocked.');
    }

    public function unblockUser(User $user)
    {
        $user->update([
            'is_blocked' => false,
            'banned_until' => null
        ]);
        return back()->with('success', 'User account restored.');
    }
}
