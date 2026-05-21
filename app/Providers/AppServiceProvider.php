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
        if (config('app.env') === 'production') {
            \Illuminate\Support\Facades\URL::forceScheme('https');

            // Trust all proxies for Hugging Face/Render load balancers
            $request = request();
            $request->setTrustedProxies([$request->getClientIp()], \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR | \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO | \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT | \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST);
        }

        // Use a static variable to ensure composer logic only runs once per request
        view()->composer(['layouts.app', 'layouts.superadmin', 'dashboard.*', 'welcome', 'auth.*'], function ($view) {
            static $composedData = null;

            if ($composedData === null) {
                // Cache the base64 logo to avoid repeated disk reads
                $logoBase64 = cache()->remember('logo_base64', 3600, function() {
                    $logoPath = resource_path('images/ac_logo.png');
                    return file_exists($logoPath) ? base64_encode(file_get_contents($logoPath)) : '';
                });

                $composedData = ['logoBase64' => $logoBase64];

                if (auth()->check()) {
                    $user = auth()->user();

                    // Poll notifications - Use a single exists query
                    $composedData['hasNewPolls'] = \Illuminate\Support\Facades\Cache::remember("user_{$user->id}_has_new_polls", 60, function() use ($user) {
                        if (!\Illuminate\Support\Facades\Schema::hasTable('polls')) return false;
                        return \App\Models\Poll::where('status', 'active')
                            ->where('created_at', '>', $user->last_poll_viewed_at ?? '2000-01-01 00:00:00')
                            ->exists();
                    });

                    // Admin notifications
                    if ($user->isAdmin()) {
                        $composedData['totalComplaintsCount'] = \Illuminate\Support\Facades\Cache::remember("admin_{$user->id}_total_complaints", 30, function() use ($user) {
                            return \App\Models\Complaint::where('created_at', '>', $user->last_complaints_viewed_at ?? '2000-01-01 00:00:00')->count();
                        });

                        // Optimize department counts using a single aggregate query
                        $composedData['deptComplaintsCount'] = \Illuminate\Support\Facades\Cache::remember("admin_{$user->id}_dept_counts", 30, function() use ($user) {
                            $viewedCats = $user->viewed_categories_at ?? [];
                            $courses = ['BSED', 'BSIT', 'CBMA', 'HM', 'SMS', 'CRIM', 'CET', 'Pre-School', 'Elementary', 'High School', 'Teaching', 'Non-Teaching'];
                            
                            // Initialize all counts to 0
                            $deptCounts = array_fill_keys($courses, 0);
                            
                            // This is still complex because each category has its own 'last_seen'
                            // But we can at least optimize it to be more efficient
                            foreach ($courses as $c) {
                                $lastSeen = $viewedCats[$c] ?? '2000-01-01 00:00:00';
                                $deptCounts[$c] = \App\Models\Complaint::join('users', 'complaints.user_id', '=', 'users.id')
                                    ->where('users.course', 'LIKE', $c . '%') // Use LIKE to match "BSED (A)" etc.
                                    ->where('complaints.created_at', '>', $lastSeen)
                                    ->count();
                            }
                            return $deptCounts;
                        });
                    } else {
                        $composedData['unseenMessagesCount'] = \Illuminate\Support\Facades\Cache::remember("user_{$user->id}_unseen_messages", 30, function() use ($user) {
                            return \App\Models\ComplaintMessage::where('is_admin', true)
                                ->whereHas('complaint', fn($q) => $q->where('user_id', $user->id))
                                ->where('created_at', '>', $user->last_messages_viewed_at ?? '2000-01-01 00:00:00')
                                ->count();
                        });
                    }

                    // Always check for unrated closed complaints (resolved or rejected) for the current user
                    $composedData['resolvedUnrated'] = \App\Models\Complaint::where('user_id', $user->id)
                        ->whereIn('status', ['resolved', 'rejected'])
                        ->whereNull('rating')
                        ->with('resolver')
                        ->get();
                }
            }

            $view->with($composedData);
        });
    }}
