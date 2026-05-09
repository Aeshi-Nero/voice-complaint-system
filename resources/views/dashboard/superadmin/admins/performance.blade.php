@extends('layouts.superadmin')

@section('title', 'Admin Performance Report | V.O.I.C.E.')

@section('content')
<div class="max-w-7xl mx-auto pb-16">
    <!-- Page Header -->
    <div class="mb-12">
        <a href="{{ route('superadmin.admins.index') }}" class="flex items-center gap-3 text-primary-container mb-4 group">
            <span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
            <span class="label-md font-bold uppercase tracking-widest text-[11px]">Administrator Directory</span>
        </a>
        <h1 class="text-3xl lg:text-5xl font-black tracking-tighter text-primary">Performance Report: {{ $admin->name }}</h1>
        <p class="text-base lg:text-xl font-medium text-outline mt-2 italic">Individual Productivity & Metric Analysis</p>
    </div>

    <!-- KPI Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6 mb-12">
        <!-- Total Cases -->
        <div class="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div>
                <span class="label-md font-bold text-outline uppercase tracking-widest text-[10px]">Total Cases Managed</span>
                <div class="text-3xl lg:text-4xl font-black text-primary mt-2">{{ number_format($totalManaged) }}</div>
            </div>
            <div class="mt-4 flex items-center text-secondary gap-1">
                <span class="material-symbols-outlined text-sm">trending_up</span>
                <span class="text-xs font-bold">Lifetime assignment</span>
            </div>
        </div>
        <!-- Resolved -->
        <div class="bg-primary p-6 rounded-xl shadow-lg flex flex-col justify-between text-on-primary">
            <div>
                <span class="label-md font-bold text-[#ffdf95] uppercase tracking-widest text-[10px]">Resolved this Month</span>
                <div class="text-3xl lg:text-4xl font-black mt-2">{{ number_format($resolvedMonth) }}</div>
            </div>
            <div class="mt-4 text-[#ffdf95]/80 text-xs font-medium">{{ $efficiencyRate }}% Efficiency Rate</div>
        </div>
        <!-- Missed Deadlines -->
        <div class="bg-error-container p-6 rounded-xl shadow-sm flex flex-col justify-between">
            <div>
                <span class="label-md font-bold text-on-error-container uppercase tracking-widest text-[10px]">Active Workload</span>
                @php 
                    $activeCount = \App\Models\Complaint::where('assigned_to', $admin->id)->whereIn('status', ['pending', 'in_progress'])->count();
                @endphp
                <div class="text-3xl lg:text-4xl font-black text-on-error-container mt-2">{{ $activeCount }}</div>
            </div>
            <div class="mt-4 text-on-error-container/60 text-xs font-medium">Currently in-queue</div>
        </div>
        <!-- Average Rating -->
        <div class="bg-surface-container p-6 rounded-xl shadow-sm border border-outline-variant/10 flex flex-col justify-between">
            <div>
                <span class="label-md font-bold text-outline uppercase tracking-widest text-[10px]">Average Rating</span>
                <div class="flex items-end gap-2 mt-2">
                    @php
                        $avgRating = \App\Models\Complaint::where('assigned_to', $admin->id)->whereNotNull('rating')->avg('rating') ?: 0;
                    @endphp
                    <div class="text-3xl lg:text-4xl font-black text-primary">{{ number_format($avgRating, 1) }}</div>
                    <div class="flex mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <span class="material-symbols-outlined text-[#f5c542] text-sm {{ $i <= round($avgRating) ? 'active-pill' : '' }}">star</span>
                        @endfor
                    </div>
                </div>
            </div>
            <div class="mt-4 text-outline text-xs font-medium">Based on feedback entries</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Chart Section -->
        <div class="lg:col-span-2 bg-surface-container-low rounded-xl p-6 lg:p-8 shadow-sm relative overflow-hidden border border-outline-variant/10">
            <div class="flex justify-between items-start mb-10">
                <div>
                    <h3 class="text-2xl font-bold text-primary tracking-tight">Case Volume Distribution</h3>
                    <p class="text-sm text-outline">Historical monthly intake analysis</p>
                </div>
                <div class="flex gap-2">
                    <button class="px-3 py-1 bg-surface-container-highest rounded text-[10px] font-bold uppercase tracking-widest text-primary-container">Monthly</button>
                </div>
            </div>
            <!-- Chart Mockup -->
            <div class="relative h-64 w-full flex items-end justify-between px-2 lg:px-4">
                <div class="absolute inset-0 flex flex-col justify-between py-2 pointer-events-none opacity-20">
                    <div class="border-t border-outline h-0"></div>
                    <div class="border-t border-outline h-0"></div>
                    <div class="border-t border-outline h-0"></div>
                    <div class="border-t border-outline h-0"></div>
                </div>
                
                @php
                    $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
                    $maxCount = count($monthlyTrends) > 0 ? max($monthlyTrends) : 10;
                @endphp

                @foreach($months as $month)
                @php
                    $count = $monthlyTrends[$month] ?? 0;
                    $height = $count > 0 ? ($count / $maxCount) * 100 : 5;
                @endphp
                <div class="w-6 lg:w-8 group relative flex flex-col justify-end items-center gap-1">
                    <div class="w-full bg-primary-container rounded-t-sm opacity-80 group-hover:opacity-100 transition-all shadow-sm" style="height: {{ $height }}%"></div>
                    <span class="text-[8px] lg:text-[10px] font-bold text-outline mt-2 uppercase">{{ $month }}</span>
                </div>
                @endforeach
            </div>
            <div class="mt-10 flex flex-wrap gap-6 px-4">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-primary-container rounded-full"></div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-outline">Cases Handled</span>
                </div>
            </div>
        </div>

        <!-- Recent Activity Feed -->
        <div class="lg:col-span-1 bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant/10">
            <h3 class="text-xl font-bold text-primary mb-6 flex items-center gap-2">
                <span class="material-symbols-outlined text-secondary">history</span>
                Recent Actions
            </h3>
            <div class="space-y-6 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                @php
                    $recentActions = \App\Models\Complaint::where('assigned_to', $admin->id)
                        ->orderBy('updated_at', 'desc')
                        ->limit(10)
                        ->get();
                @endphp
                
                @forelse($recentActions as $action)
                <div class="flex gap-4 group">
                    <div class="flex flex-col items-center">
                        <div class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center">
                            @if($action->status === 'resolved')
                                <span class="material-symbols-outlined text-on-secondary-container text-xs">check</span>
                            @else
                                <span class="material-symbols-outlined text-on-secondary-container text-xs">edit_note</span>
                            @endif
                        </div>
                        @if(!$loop->last)
                            <div class="w-px h-full bg-outline-variant/30 mt-2"></div>
                        @endif
                    </div>
                    <div class="pb-6">
                        <div class="text-xs font-bold text-primary mb-1 uppercase tracking-wide">
                            {{ ucfirst($action->status) }} Case #{{ $action->complaint_number }}
                        </div>
                        <p class="text-sm text-outline leading-tight truncate max-w-[200px]">{{ $action->title }}</p>
                        <span class="text-[10px] font-medium text-outline-variant mt-2 block italic">{{ $action->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
                @empty
                <p class="text-sm text-zinc-400 italic text-center">No recent activity recorded.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Profile Summary Card -->
    <div class="mt-12 bg-white/50 border border-outline-variant/10 rounded-xl p-6 lg:p-8 ivory-glass shadow-sm flex flex-col md:flex-row items-center gap-10">
        <div class="relative shrink-0">
            <div class="h-32 w-32 rounded-full border-4 border-primary overflow-hidden bg-primary/10 flex items-center justify-center font-black text-4xl text-primary shadow-inner">
                @if($admin->profile_image)
                    <img src="{{ asset('storage/' . $admin->profile_image) }}" class="h-full w-full object-cover">
                @else
                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                @endif
            </div>
            <div class="absolute -bottom-2 -right-2 bg-secondary text-on-secondary px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest shadow-lg">
                Verified
            </div>
        </div>
        <div class="flex-1 text-center md:text-left">
            <div class="flex flex-col md:flex-row md:items-end gap-4 mb-4">
                <h2 class="text-3xl font-black text-primary">{{ $admin->name }}</h2>
                <span class="label-md bg-primary-container text-[#ffdf95] px-3 py-1 rounded text-[10px] font-bold uppercase tracking-widest">Administrator</span>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <span class="text-[10px] font-bold text-outline uppercase tracking-widest block mb-1">Affiliation</span>
                    <span class="text-sm font-semibold text-primary">{{ $admin->course ?: 'General Admin' }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-outline uppercase tracking-widest block mb-1">Account Created</span>
                    <span class="text-sm font-semibold text-primary">{{ $admin->created_at->format('M Y') }}</span>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-outline uppercase tracking-widest block mb-1">Status</span>
                    <span class="text-sm font-semibold text-primary uppercase">{{ $admin->is_blocked ? 'Suspended' : 'Active' }}</span>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-3 min-w-[200px] w-full md:w-auto">
            <button class="academic-gradient text-[#ffdf95] py-3 px-6 rounded-lg font-bold text-xs uppercase tracking-widest shadow-lg transition-transform active:scale-95">
                Download PDF Report
            </button>
        </div>
    </div>
</div>
@endsection
