<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
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

        // Banned users
        $bannedUsers = User::where('is_blocked', true)
            ->orWhere(function($query) {
                $query->whereNotNull('banned_until')
                      ->where('banned_until', '>', now());
            })
            ->get();

        return view('users-management', compact('onlineUsersCount', 'usersPerDepartment', 'bannedUsers'));
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
                // Check if it's the 25-1(1).xlsx style row structure (headers start late)
                // or standard simple CSV (headers at row 1)
                $headers = fgetcsv($handle, 1000, ","); 
                
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                    // Mapping columns: 0:id, 1:name, 2:email, 3:course, 4:role
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
                ]
            );
        }
    }
}
