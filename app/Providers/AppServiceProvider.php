<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('*', function ($view) {
            if (auth()->check()) {
                // Poll notifications - Safe check if table exists
                if (\Illuminate\Support\Facades\Schema::hasTable('polls')) {
                    $hasNewPolls = \App\Models\Poll::where('status', 'active')
                        ->where('created_at', '>', auth()->user()->last_poll_viewed_at ?? '2000-01-01')
                        ->exists();
                    $view->with('hasNewPolls', $hasNewPolls);
                }

                // Admin notifications - Safe check if table exists
                if (auth()->user()->isAdmin() && \Illuminate\Support\Facades\Schema::hasTable('complaints')) {
                    $user = auth()->user();
                    
                    // Total unseen complaints
                    $totalComplaints = \App\Models\Complaint::where('created_at', '>', $user->last_complaints_viewed_at ?? '2000-01-01')->count();
                    $view->with('totalComplaintsCount', $totalComplaints);

                    // Get counts per department/course for unseen complaints
                    $courses = ['BSED', 'BSIT', 'CBMA', 'HM', 'SMS', 'CRIM', 'CET', 'Pre-School', 'Elementary', 'High School', 'Teaching', 'Non-Teaching'];
                    $deptCounts = [];
                    $viewedCats = $user->viewed_categories_at ?? [];
                    
                    foreach ($courses as $c) {
                        $lastSeen = $viewedCats[$c] ?? '2000-01-01 00:00:00';
                        $count = \App\Models\Complaint::whereHas('user', function($q) use ($c) {
                                $q->where('course', $c);
                            })
                            ->where('created_at', '>', $lastSeen)
                            ->count();
                        $deptCounts[$c] = $count;
                    }
                    
                    $view->with('deptComplaintsCount', $deptCounts);
                } elseif (!auth()->user()->isAdmin() && \Illuminate\Support\Facades\Schema::hasTable('complaint_messages')) {
                    // Student notifications
                    $user = auth()->user();
                    
                    // Count admin replies created after the user last viewed their complaints list
                    $unseenMessages = \App\Models\ComplaintMessage::where('is_admin', true)
                        ->whereHas('complaint', function($q) use ($user) {
                            $q->where('user_id', $user->id);
                        })
                        ->where('created_at', '>', $user->last_messages_viewed_at ?? '2000-01-01 00:00:00')
                        ->count();
                        
                    $view->with('unseenMessagesCount', $unseenMessages);
                }
            }
        });
    }
}
