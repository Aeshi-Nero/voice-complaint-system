<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class ResetDailyComplaints extends Command
{
    protected $signature = 'complaints:reset';
    protected $description = 'Reset daily complaint counts for all users';

    public function handle()
    {
        $now = Carbon::now('Asia/Manila');
        
        User::query()->update([
            'complaints_today' => 0,
            'last_complaint_reset' => $now
        ]);
        
        $this->info('Daily complaint counts reset successfully at ' . $now->toDateTimeString());
        
        \Log::info('Daily complaint reset executed', ['timestamp' => $now]);
    }
}