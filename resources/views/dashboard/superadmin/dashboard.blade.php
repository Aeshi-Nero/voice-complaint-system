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
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-outline-variant/10 group hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-6">
                <div class="p-3 bg-primary-container/10 rounded-lg text-primary">
                    <span class="material-symbols-outlined">group</span>
                </div>
                <span class="text-secondary text-[10px] font-bold bg-secondary-container/30 px-2 py-1 rounded">Active</span>
            </div>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-wider">Total Admins</p>
            <h3 class="text-3xl md:text-4xl font-black text-primary mt-1">{{ $totalAdmins }}</h3>
            <div class="mt-6 h-1.5 w-full bg-zinc-100 rounded-full overflow-hidden">
                <div class="h-full bg-primary" style="width: 100%"></div>
            </div>
        </div>
        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-outline-variant/10 group hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-6">
                <div class="p-3 bg-tertiary-container/10 rounded-lg text-tertiary-fixed-dim">
                    <span class="material-symbols-outlined">schedule</span>
                </div>
                <span class="text-secondary text-[10px] font-bold bg-secondary-container/30 px-2 py-1 rounded">Real-time</span>
            </div>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-wider">Avg. Resolution Time</p>
            <h3 class="text-3xl md:text-4xl font-black text-primary mt-1">{{ $avgResolutionTime }} <span class="text-sm font-normal text-zinc-400">Days</span></h3>
            <div class="mt-6 h-1.5 w-full bg-zinc-100 rounded-full overflow-hidden">
                <div class="h-full bg-tertiary-fixed-dim" style="width: 88%"></div>
            </div>
        </div>
        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-outline-variant/10 group hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-6">
                <div class="p-3 bg-secondary-container/20 rounded-lg text-secondary">
                    <span class="material-symbols-outlined">health_metrics</span>
                </div>
                <span class="text-secondary text-[10px] font-bold bg-secondary-container/30 px-2 py-1 rounded">Optimal</span>
            </div>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-wider">System Health</p>
            <h3 class="text-3xl md:text-4xl font-black text-primary mt-1">{{ $systemHealth }}<span class="text-sm font-normal text-zinc-400">%</span></h3>
            <div class="mt-6 h-1.5 w-full bg-zinc-100 rounded-full overflow-hidden">
                <div class="h-full bg-secondary" style="width: {{ $systemHealth }}%"></div>
            </div>
        </div>
        <div class="bg-white p-6 md:p-8 rounded-2xl shadow-sm border border-outline-variant/10 group hover:shadow-md transition-shadow">
            <div class="flex justify-between items-start mb-6">
                <div class="p-3 bg-error-container/20 rounded-lg text-error">
                    <span class="material-symbols-outlined">bolt</span>
                </div>
                <span class="text-error text-[10px] font-bold bg-error-container/30 px-2 py-1 rounded">{{ $activeCases > 50 ? 'High Load' : 'Stable' }}</span>
            </div>
            <p class="text-zinc-500 text-[10px] font-bold uppercase tracking-wider">Active Cases</p>
            <h3 class="text-3xl md:text-4xl font-black text-primary mt-1">{{ $activeCases }}</h3>
            <div class="mt-6 h-1.5 w-full bg-zinc-100 rounded-full overflow-hidden">
                <div class="h-full bg-error" style="width: {{ min(100, $activeCases) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Breakdown & Trends -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Complaint Resolution Trends -->
        <div class="lg:col-span-2 bg-white p-6 md:p-10 rounded-[2rem] shadow-sm border border-outline-variant/5">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6 mb-10">
                <h3 class="text-xl font-black text-primary flex items-center gap-3">
                    <span class="material-symbols-outlined">trending_up</span>
                    Resolution Trends
                </h3>
                <div class="flex bg-gray-50 p-1 rounded-xl w-full sm:w-auto">
                    <button class="flex-1 sm:flex-none px-4 py-1.5 text-[10px] font-bold rounded-lg uppercase tracking-widest text-primary/40 hover:text-primary transition-colors">Weekly</button>
                    <button class="flex-1 sm:flex-none bg-primary text-on-primary px-4 py-1.5 text-[10px] font-bold rounded-lg uppercase tracking-widest shadow-md">Monthly</button>
                </div>
            </div>
            <!-- CSS Chart Mockup -->
            <div class="h-[250px] md:h-[300px] flex items-end gap-2 md:gap-4 px-2 pb-4 relative">
                <div class="absolute inset-0 flex flex-col justify-between opacity-5 pointer-events-none text-primary">
                    <div class="border-b border-current w-full"></div>
                    <div class="border-b border-current w-full"></div>
                    <div class="border-b border-current w-full"></div>
                    <div class="border-b border-current w-full"></div>
                </div>
                <div class="flex-1 bg-primary/20 hover:bg-primary/40 transition-all rounded-t-xl h-[40%]" title="Jan"></div>
                <div class="flex-1 bg-primary/30 hover:bg-primary/50 transition-all rounded-t-xl h-[60%]" title="Feb"></div>
                <div class="flex-1 bg-primary/40 hover:bg-primary/60 transition-all rounded-t-xl h-[55%]" title="Mar"></div>
                <div class="flex-1 bg-primary/50 hover:bg-primary/70 transition-all rounded-t-xl h-[85%]" title="Apr"></div>
                <div class="flex-1 bg-primary/60 hover:bg-primary/80 transition-all rounded-t-xl h-[75%]" title="May"></div>
                <div class="flex-1 bg-primary/70 hover:bg-primary/90 transition-all rounded-t-xl h-[95%]" title="Jun"></div>
                <div class="flex-1 bg-primary hover:bg-primary/90 transition-all rounded-t-xl h-[80%]" title="Jul"></div>
            </div>
            <div class="flex justify-between px-2 mt-4 text-[9px] md:text-[10px] font-black text-zinc-400 uppercase tracking-widest">
                <span>Jan</span><span>Feb</span><span>Mar</span><span>Apr</span><span>May</span><span>Jun</span><span>Jul</span>
            </div>
        </div>

        <!-- Departmental Breakdown -->
        <div class="bg-white p-6 md:p-10 rounded-[2rem] shadow-sm border border-outline-variant/5">
            <h3 class="text-xl font-black text-primary mb-8 uppercase tracking-tight">Departmental Hubs</h3>
            <div class="space-y-8 overflow-y-auto max-h-[350px] pr-2 custom-scrollbar">
                @php
                    $totalComplaints = array_sum($deptStats);
                @endphp
                @forelse($deptStats as $dept => $count)
                <div class="group">
                    <div class="flex justify-between mb-2">
                        <span class="text-xs font-black text-primary uppercase tracking-tight truncate pr-4">{{ $dept }}</span>
                        <span class="text-[10px] font-black text-primary/40">{{ $totalComplaints > 0 ? round(($count / $totalComplaints) * 100) : 0 }}%</span>
                    </div>
                    <div class="h-1.5 w-full bg-primary/5 rounded-full overflow-hidden shadow-inner group-hover:bg-primary/10 transition-colors">
                        <div class="h-full bg-primary transition-all duration-1000" style="width: {{ $totalComplaints > 0 ? ($count / $totalComplaints) * 100 : 0 }}%"></div>
                    </div>
                </div>
                @empty
                <p class="text-xs text-zinc-400 italic font-bold">No departmental data available.</p>
                @endforelse
            </div>
            <button class="w-full mt-10 py-4 text-[10px] font-black uppercase tracking-widest text-primary-container border-t-2 border-dashed border-primary-container/10 hover:bg-primary-container/5 transition-all text-center">
                View Full Hierarchy
            </button>
        </div>
    </div>

    <!-- Admin Performance Leaderboard -->
    <section class="bg-white rounded-[2rem] shadow-sm border border-outline-variant/10 overflow-hidden">
        <div class="px-6 md:px-10 py-8 border-b border-outline-variant/10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
            <h3 class="text-2xl font-black text-primary uppercase tracking-tighter">Admin Performance</h3>
            <div class="w-full sm:w-auto">
                <select class="w-full sm:w-auto text-[10px] border-none bg-gray-50 rounded-xl focus:ring-primary-container font-black uppercase tracking-widest px-6 py-3">
                    <option>Sort by: Score</option>
                    <option>Sort by: Resolution Time</option>
                </select>
            </div>
        </div>

        <!-- Mobile Leaderboard View -->
        <div class="lg:hidden divide-y divide-outline-variant/5">
            @forelse($adminsForLeaderboard as $admin)
            @php
                $efficiency = $admin->assigned_count > 0 ? round(($admin->resolved_count / $admin->assigned_count) * 100) : 0;
            @endphp
            <div class="p-6 hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('superadmin.admins.performance', $admin) }}'">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center font-black text-primary text-sm shadow-inner overflow-hidden">
                            @if($admin->profile_image)
                                <img src="{{ asset('storage/' . $admin->profile_image) }}" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                            @endif
                        </div>
                        <div>
                            <p class="text-sm font-black text-primary uppercase tracking-tight">{{ $admin->name }}</p>
                            <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">{{ $admin->course ?: 'General Admin' }}</p>
                        </div>
                    </div>
                    @if($efficiency >= 90)
                        <span class="bg-green-100 text-green-700 p-1.5 rounded-lg">
                            <i class="fas fa-trophy text-[10px]"></i>
                        </span>
                    @endif
                </div>
                
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="text-center p-3 bg-gray-50 rounded-2xl">
                        <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest mb-1">Resolved</p>
                        <p class="text-sm font-black text-primary">{{ $admin->resolved_count }}</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-2xl">
                        <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest mb-1">Efficiency</p>
                        <p class="text-sm font-black text-primary">{{ $efficiency }}%</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-2xl">
                        <p class="text-[8px] font-black text-zinc-400 uppercase tracking-widest mb-1">Rating</p>
                        <p class="text-sm font-black text-primary">4.9</p>
                    </div>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-dashed border-outline-variant/10">
                    <span class="text-[9px] font-black uppercase tracking-widest {{ $efficiency >= 50 ? 'text-secondary' : 'text-orange-500' }}">
                        {{ $efficiency >= 90 ? 'Top Performer' : ($efficiency >= 50 ? 'Active' : 'Under Review') }}
                    </span>
                    <i class="fas fa-chevron-right text-zinc-200 text-xs"></i>
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-zinc-400 font-black uppercase tracking-widest text-[10px] italic">No admin activity recorded.</div>
            @endforelse
        </div>

        <!-- Desktop Leaderboard View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50">
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-primary/60">Administrator</th>
                        <th class="px-6 py-6 text-[10px] font-black uppercase tracking-widest text-primary/60 text-center">Assigned</th>
                        <th class="px-6 py-6 text-[10px] font-black uppercase tracking-widest text-primary/60 text-center">Resolved</th>
                        <th class="px-6 py-6 text-[10px] font-black uppercase tracking-widest text-primary/60 text-center">Efficiency</th>
                        <th class="px-6 py-6 text-[10px] font-black uppercase tracking-widest text-primary/60 text-center">Rating</th>
                        <th class="px-10 py-6 text-[10px] font-black uppercase tracking-widest text-primary/60 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/5">
                    @forelse($adminsForLeaderboard as $admin)
                    <tr class="hover:bg-gray-50 transition-colors group cursor-pointer" onclick="window.location='{{ route('superadmin.admins.performance', $admin) }}'">
                        <td class="px-10 py-6">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-2xl bg-primary/5 flex items-center justify-center font-black text-primary text-xs border border-primary/20 overflow-hidden shadow-inner">
                                    @if($admin->profile_image)
                                        <img src="{{ asset('storage/' . $admin->profile_image) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ strtoupper(substr($admin->name, 0, 2)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-black text-primary uppercase tracking-tight">{{ $admin->name }}</p>
                                    <p class="text-[9px] font-bold text-zinc-400 uppercase tracking-widest">{{ $admin->course ?: 'General Admin' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-6 text-sm font-black text-primary text-center">{{ $admin->assigned_count }}</td>
                        <td class="px-6 py-6 text-sm font-black text-primary text-center">{{ $admin->resolved_count }}</td>
                        <td class="px-6 py-6 text-sm font-black text-primary text-center">
                            @php
                                $efficiency = $admin->assigned_count > 0 ? round(($admin->resolved_count / $admin->assigned_count) * 100) : 0;
                            @endphp
                            {{ $efficiency }}%
                        </td>
                        <td class="px-6 py-6">
                            <div class="flex items-center justify-center gap-1.5">
                                <span class="text-sm font-black text-primary">4.9</span>
                                <i class="fas fa-star text-accent text-[10px]"></i>
                            </div>
                        </td>
                        <td class="px-10 py-6 text-right">
                            @if($efficiency >= 90)
                                <span class="bg-green-50 text-green-700 px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest">Top Performer</span>
                            @elseif($efficiency >= 50)
                                <span class="bg-blue-50 text-blue-700 px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest">Active</span>
                            @else
                                <span class="bg-orange-50 text-orange-700 px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest">Review</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-10 py-12 text-center text-zinc-400 font-bold italic uppercase tracking-widest text-xs">No activity records.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-10 py-6 bg-gray-50/30 border-t border-outline-variant/10 text-center">
            <a href="{{ route('superadmin.admins.index') }}" class="text-[10px] font-black uppercase tracking-[0.2em] text-primary hover:tracking-[0.4em] transition-all">Audit All Administrators <i class="fas fa-chevron-right ml-2 text-[8px]"></i></a>
        </div>
    </section>
</div>

<!-- FAB -->
<button class="fixed bottom-8 right-8 w-14 h-14 bg-primary text-on-primary rounded-full shadow-2xl flex items-center justify-center academic-gradient group hover:scale-110 transition-all z-50">
    <span class="material-symbols-outlined text-2xl group-hover:rotate-45 transition-transform">add_chart</span>
</button>
@endsection
