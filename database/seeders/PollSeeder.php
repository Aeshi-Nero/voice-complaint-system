<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Poll;
use App\Models\PollOption;
use App\Models\User;
use Carbon\Carbon;

class PollSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        // Active Poll 1
        $poll1 = Poll::create([
            'title' => 'New Cafeteria Menu Options',
            'description' => 'Choose the primary focus for next semester\'s culinary rotation at the Central Hub.',
            'expires_at' => Carbon::now()->addDays(10),
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $poll1->options()->createMany([
            ['option_text' => 'Healthy Bowls & Salads'],
            ['option_text' => 'Authentic Pasta Bar'],
            ['option_text' => 'Gourmet Street Food'],
        ]);

        // Active Poll 2
        $poll2 = Poll::create([
            'title' => 'Campus Wi-Fi Upgrade Priority',
            'description' => 'Which area should receive the Wi-Fi 6 upgrade first?',
            'expires_at' => Carbon::now()->addDays(5),
            'status' => 'active',
            'created_by' => $admin->id,
        ]);

        $poll2->options()->createMany([
            ['option_text' => 'Library'],
            ['option_text' => 'Dormitories'],
            ['option_text' => 'Academic Buildings'],
        ]);

        // Closed Poll
        $poll3 = Poll::create([
            'title' => 'Library 24/7 Access Policy',
            'description' => 'Should the library be open 24/7 during finals week?',
            'expires_at' => Carbon::now()->subDays(2),
            'status' => 'closed',
            'created_by' => $admin->id,
        ]);

        $poll3->options()->createMany([
            ['option_text' => 'Support 24/7 access', 'votes_count' => 820],
            ['option_text' => 'Keep current hours', 'votes_count' => 180],
        ]);
    }
}
