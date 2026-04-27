@extends('layouts.superadmin')

@section('title', 'System Performance Overview | V.O.I.C.E.')

@section('content')
<div class="space-y-10">
    <!-- Header Section -->
    <header class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6">
        <div>
            <p class="text-primary-container font-semibold label-md uppercase tracking-widest mb-2">Internal Analytics</p>
            <h2 class="text-3xl lg:text-4xl font-bold tracking-tight text-primary">System Performance Overview</h2>
        </div>
        <div class="flex gap-3 w-full sm:w-auto">
            <button class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg border border-primary text-primary font-bold text-sm hover:bg-surface-container-high transition-colors">
                Export Report
            </button>
            <button class="flex-1 sm:flex-none px-6 py-2.5 rounded-lg bg-primary text-on-primary font-bold text-sm academic-gradient shadow-md shadow-primary/20">
                Live Monitor
            </button>
        </div>
    </header>

    <!-- Global Metrics Bento Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/10 group hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-primary-container/10 rounded-lg text-primary">
                    <span class="material-symbols-outlined">group</span>
                </div>
                <span class="text-secondary text-xs font-bold bg-secondary-container/30 px-2 py-1 rounded">Active</span>
            </div>
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-wider">Total Admins</p>
            <h3 class="text-3xl font-black text-primary mt-1">{{ $totalAdmins }}</h3>
            <div class="mt-4 h-1 w-full bg-zinc-100 rounded-full overflow-hidden">
                <div class="h-full bg-primary" style="width: 100%"></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/10 group hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-tertiary-container/10 rounded-lg text-tertiary-fixed-dim">
                    <span class="material-symbols-outlined">schedule</span>
                </div>
                <span class="text-secondary text-xs font-bold bg-secondary-container/30 px-2 py-1 rounded">Real-time</span>
            </div>
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-wider">Avg. Resolution Time</p>
            <h3 class="text-3xl font-black text-primary mt-1">{{ $avgResolutionTime }} <span class="text-sm font-normal text-zinc-400">Days</span></h3>
            <div class="mt-4 h-1 w-full bg-zinc-100 rounded-full overflow-hidden">
                <div class="h-full bg-tertiary-fixed-dim" style="width: 88%"></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/10 group hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-secondary-container/20 rounded-lg text-secondary">
                    <span class="material-symbols-outlined">health_metrics</span>
                </div>
                <span class="text-secondary text-xs font-bold bg-secondary-container/30 px-2 py-1 rounded">Optimal</span>
            </div>
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-wider">System Health</p>
            <h3 class="text-3xl font-black text-primary mt-1">{{ $systemHealth }}<span class="text-sm font-normal text-zinc-400">%</span></h3>
            <div class="mt-4 h-1 w-full bg-zinc-100 rounded-full overflow-hidden">
                <div class="h-full bg-secondary" style="width: {{ $systemHealth }}%"></div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-xl shadow-sm border border-outline-variant/10 group hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-4">
                <div class="p-3 bg-error-container/20 rounded-lg text-error">
                    <span class="material-symbols-outlined">bolt</span>
                </div>
                <span class="text-error text-xs font-bold bg-error-container/30 px-2 py-1 rounded">{{ $activeCases > 50 ? 'High Load' : 'Stable' }}</span>
            </div>
            <p class="text-zinc-500 text-xs font-bold uppercase tracking-wider">Active Cases</p>
            <h3 class="text-3xl font-black text-primary mt-1">{{ $activeCases }}</h3>
            <div class="mt-4 h-1 w-full bg-zinc-100 rounded-full overflow-hidden">
                <div class="h-full bg-error" style="width: {{ min(100, $activeCases) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Breakdown & Trends -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Complaint Resolution Trends -->
        <div class="lg:col-span-2 bg-white p-6 lg:p-8 rounded-2xl shadow-sm border border-outline-variant/5">
            <div class="flex justify-between items-center mb-8">
                <h3 class="text-lg lg:text-xl font-bold text-primary flex items-center gap-2">
                    <span class="material-symbols-outlined">trending_up</span>
                    Complaint Resolution Trends
                </h3>
                <div class="flex gap-2">
                    <button class="bg-gray-50 px-3 py-1 text-[10px] font-bold rounded-lg shadow-sm border border-outline-variant/10 uppercase tracking-widest text-primary/40">Weekly</button>
                    <button class="bg-primary text-on-primary px-3 py-1 text-[10px] font-bold rounded-lg uppercase tracking-widest">Monthly</button>
                </div>
            </div>
            <!-- CSS Chart Mockup -->
            <div class="h-[300px] flex items-end gap-2 lg:gap-4 px-2 lg:px-4 pb-4 relative">
                <div class="absolute inset-0 flex flex-col justify-between opacity-5 pointer-events-none text-primary">
                    <div class="border-b border-current w-full"></div>
                    <div class="border-b border-current w-full"></div>
                    <div class="border-b border-current w-full"></div>
                    <div class="border-b border-current w-full"></div>
                </div>
                <div class="flex-1 bg-primary/20 hover:bg-primary/40 transition-colors rounded-t-lg h-[40%]" title="Jan"></div>
                <div class="flex-1 bg-primary/30 hover:bg-primary/50 transition-colors rounded-t-lg h-[60%]" title="Feb"></div>
                <div class="flex-1 bg-primary/40 hover:bg-primary/60 transition-colors rounded-t-lg h-[55%]" title="Mar"></div>
                <div class="flex-1 bg-primary/50 hover:bg-primary/70 transition-colors rounded-t-lg h-[85%]" title="Apr"></div>
                <div class="flex-1 bg-primary/60 hover:bg-primary/80 transition-colors rounded-t-lg h-[75%]" title="May"></div>
                <div class="flex-1 bg-primary/70 hover:bg-primary/90 transition-colors rounded-t-lg h-[95%]" title="Jun"></div>
                <div class="flex-1 bg-primary hover:bg-primary/90 transition-colors rounded-t-lg h-[80%]" title="Jul"></div>
            </div>
            <div class="flex justify-between px-4 mt-4 text-[10px] font-bold text-zinc-400 uppercase tracking-widest">
                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span><span>Jul</span>
            </div>
        </div>

        <!-- Departmental Breakdown -->
        <div class="bg-white p-6 lg:p-8 rounded-2xl shadow-sm border border-outline-variant/5">
            <h3 class="text-xl font-bold text-primary mb-6">Departmental Hubs</h3>
            <div class="space-y-6 overflow-y-auto max-h-[350px] pr-2 custom-scrollbar">
                @php
                    $totalComplaints = array_sum($deptStats);
                @endphp
                @forelse($deptStats as $dept => $count)
                <div>
                    <div class="flex justify-between mb-2">
                        <span class="text-sm font-bold text-primary truncate pr-4">{{ $dept }}</span>
                        <span class="text-xs font-bold text-primary/60 shrink-0">{{ $totalComplaints > 0 ? round(($count / $totalComplaints) * 100) : 0 }}%</span>
                    </div>
                    <div class="h-2 w-full bg-primary/5 rounded-full overflow-hidden shadow-inner">
                        <div class="h-full bg-primary" style="width: {{ $totalComplaints > 0 ? ($count / $totalComplaints) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-sm text-zinc-400 italic">No departmental data available.</p>
                @endforelse
            </div>
            <button class="w-full mt-8 py-3 text-xs font-black uppercase tracking-widest text-primary-container border-b-2 border-primary-container hover:bg-primary-container/5 transition-all text-center">
                View Full Hierarchy
            </button>
        </div>
    </div>

    <!-- Admin Performance Leaderboard -->
    <section class="bg-white rounded-2xl shadow-sm border border-outline-variant/10 overflow-hidden">
        <div class="px-6 lg:px-8 py-6 border-b border-outline-variant/10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h3 class="text-xl font-bold text-primary">Admin Performance Leaderboard</h3>
            <div class="flex items-center gap-4 w-full sm:w-auto">
                <select class="w-full sm:w-auto text-sm border-none bg-gray-50 rounded-lg focus:ring-primary-container font-semibold">
                    <option>Sort by: Score</option>
                    <option>Sort by: Resolution Time</option>
                </select>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left min-w-[800px]">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-primary/60">Admin Name</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-primary/60 text-center">Assigned</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-primary/60 text-center">Resolved</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-primary/60 text-center">Efficiency</th>
                        <th class="px-6 py-4 text-[10px] font-black uppercase tracking-widest text-primary/60 text-center">Satisfaction</th>
                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-primary/60 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/5">
                    @forelse($adminsForLeaderboard as $admin)
                    <tr class="hover:bg-gray-50 transition-colors group cursor-pointer" onclick="window.location='{{ route('superadmin.admins.performance', $admin) }}'">
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-primary/10 flex items-center justify-center font-bold text-primary text-xs border border-primary/20 overflow-hidden shadow-inner">
                                    @if($admin->profile_image)
                                        <img src="{{ asset('storage/' . $admin->profile_image) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($admin->name, 0, 2)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-primary">{{ $admin->name }}</p>
                                    <p class="text-[10px] text-zinc-500">{{ $admin->course ?: 'General Admin' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 text-sm font-semibold text-zinc-700 text-center">{{ $admin->assigned_count }}</td>
                        <td class="px-6 py-5 text-sm font-semibold text-zinc-700 text-center">{{ $admin->resolved_count }}</td>
                        <td class="px-6 py-5 text-sm font-semibold text-zinc-700 text-center">
                            @php
                                $efficiency = $admin->assigned_count > 0 ? round(($admin->resolved_count / $admin->assigned_count) * 100) : 0;
                            @endphp
                            {{ $efficiency }}%
                        </td>
                        <td class="px-6 py-5">
                            <div class="flex items-center justify-center gap-1">
                                <span class="text-sm font-bold text-primary">4.9</span>
                                <span class="material-symbols-outlined text-[14px] text-accent active-pill">star</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                            @if($efficiency >= 90)
                                <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Top Performer</span>
                            @elseif($efficiency >= 50)
                                <span class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Active</span>
                            @else
                                <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">Under Review</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-10 text-center text-zinc-400 font-medium italic">No admin accounts found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-8 py-4 bg-gray-50/30 border-t border-outline-variant/10 text-center">
            <a href="{{ route('superadmin.admins.index') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-primary hover:tracking-[0.3em] transition-all">View All Administrators</a>
        </div>
    </section>
</div>

<!-- FAB -->
<button class="fixed bottom-8 right-8 w-14 h-14 bg-primary text-on-primary rounded-full shadow-2xl flex items-center justify-center academic-gradient group hover:scale-110 transition-all z-50">
    <span class="material-symbols-outlined text-2xl group-hover:rotate-45 transition-transform">add_chart</span>
</button>
@endsection
