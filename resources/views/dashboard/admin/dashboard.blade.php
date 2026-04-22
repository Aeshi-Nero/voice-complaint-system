@extends("layouts.app")

@section("content")
<div class="max-w-full mx-auto">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-12">
        <div>
            <h2 class="text-4xl font-black text-[#163a24] tracking-tight uppercase">Dashboard Overview</h2>
            <p class="text-gray-500 font-bold text-sm mt-1">Real-time complaint analytics and system metrics.</p>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-12">
        <!-- Pending Review -->
        <div class="bg-white rounded-[2.5rem] p-10 shadow-xl border border-[#163a24]/5 relative overflow-hidden group">
            <div class="flex justify-between items-start mb-10">
                <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center text-[#163a24] text-2xl group-hover:rotate-6 transition-transform">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <span class="bg-[#f3bc3e]/20 text-[#163a24] px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest">Review Queue</span>
            </div>
            <p class="text-6xl font-black text-[#163a24] leading-none mb-2">{{ $stats["pending"] ?? 0 }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pending Review</p>
            <div class="absolute bottom-0 left-0 right-0 h-2 bg-gray-50">
                <div class="bg-[#f3bc3e] h-full transition-all duration-1000" style="width: 40%"></div>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white rounded-[2.5rem] p-10 shadow-xl border border-[#163a24]/5 relative overflow-hidden group">
            <div class="flex justify-between items-start mb-10">
                <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-blue-600 text-2xl group-hover:-rotate-6 transition-transform">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <span class="bg-blue-500/10 text-blue-600 px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest">Active Cases</span>
            </div>
            <p class="text-6xl font-black text-[#163a24] leading-none mb-2">{{ $stats["in_progress"] ?? 0 }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">In Progress</p>
            <div class="absolute bottom-0 left-0 right-0 h-2 bg-gray-50">
                <div class="bg-blue-500 h-full transition-all duration-1000" style="width: 25%"></div>
            </div>
        </div>

        <!-- Total Resolved -->
        <div class="bg-white rounded-[2.5rem] p-10 shadow-xl border border-[#163a24]/5 relative overflow-hidden group">
            <div class="flex justify-between items-start mb-10">
                <div class="w-14 h-14 bg-green-50 rounded-2xl flex items-center justify-center text-green-600 text-2xl group-hover:scale-110 transition-transform">
                    <i class="fas fa-check-circle"></i>
                </div>
                <span class="bg-green-500/10 text-green-600 px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest">Resolved</span>
            </div>
            <p class="text-6xl font-black text-[#163a24] leading-none mb-2">{{ $stats["resolved"] ?? 0 }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Total Resolved</p>
            <div class="absolute bottom-0 left-0 right-0 h-2 bg-gray-50">
                <div class="bg-green-600 h-full transition-all duration-1000" style="width: 85%"></div>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Categories Progress -->
        <div class="lg:col-span-1 space-y-10">
            <!-- Category Chart -->
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-[#163a24]/5 p-10">
                <div class="flex items-center justify-between mb-12">
                    <h3 class="text-lg font-black text-[#163a24] uppercase tracking-tight">Complaints by Category</h3>
                    <i class="fas fa-chart-pie text-gray-300"></i>
                </div>
                
                <div class="relative flex justify-center mb-12">
                    <div class="w-48 h-48 rounded-full border-[1.2rem] border-[#163a24] flex flex-col items-center justify-center">
                        <p class="text-3xl font-black text-[#163a24] leading-none">{{ array_sum($categoryStats) }}</p>
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mt-1">Total Files</p>
                    </div>
                    <div class="absolute inset-0 w-48 h-48 mx-auto rounded-full border-[1.2rem] border-transparent border-t-[#f3bc3e] border-r-transparent rotate-[45deg]"></div>
                </div>

                <div class="space-y-6">
                    @php
                        $totalComplaints = array_sum($categoryStats);
                        $colors = [
                            'Academic' => 'bg-[#163a24]',
                            'Faculty' => 'bg-green-600',
                            'Administrative' => 'bg-[#f3bc3e]',
                            'IT/Technical' => 'bg-blue-600',
                            'Health & Safety' => 'bg-red-600',
                        ];
                    @endphp
                    @forelse($categoryStats as $category => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full {{ $colors[$category] ?? 'bg-gray-400' }}"></span>
                            <span class="text-sm font-bold text-[#163a24]">{{ $category }}</span>
                        </div>
                        <span class="text-sm font-black text-[#163a24]">{{ $totalComplaints > 0 ? round(($count / $totalComplaints) * 100) : 0 }}%</span>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 font-bold italic">No data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Department Chart -->
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-[#163a24]/5 p-10">
                <div class="flex items-center justify-between mb-12">
                    <h3 class="text-lg font-black text-[#163a24] uppercase tracking-tight">Complaints by Dept</h3>
                    <i class="fas fa-university text-gray-300"></i>
                </div>
                
                <div class="relative flex justify-center mb-12">
                    <div class="w-48 h-48 rounded-full border-[1.2rem] border-[#163a24] flex flex-col items-center justify-center">
                        <p class="text-3xl font-black text-[#163a24] leading-none">{{ array_sum($departmentStats) }}</p>
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mt-1">Total Depts</p>
                    </div>
                    <div class="absolute inset-0 w-48 h-48 mx-auto rounded-full border-[1.2rem] border-transparent border-t-green-500 border-l-transparent -rotate-[120deg]"></div>
                </div>

                <div class="space-y-6 overflow-y-auto max-h-[300px] pr-2 custom-scrollbar">
                    @php
                        $totalDeptComplaints = array_sum($departmentStats);
                        $deptColors = ['bg-[#163a24]', 'bg-[#f3bc3e]', 'bg-green-600', 'bg-blue-600', 'bg-red-600', 'bg-purple-600', 'bg-orange-600'];
                    @endphp
                    @forelse($departmentStats as $dept => $count)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="w-3 h-3 rounded-full {{ $deptColors[$loop->index % count($deptColors)] }}"></span>
                            <span class="text-xs font-bold text-[#163a24] truncate max-w-[120px]">{{ $dept }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-bold text-gray-400">({{ $count }})</span>
                            <span class="text-sm font-black text-[#163a24]">{{ $totalDeptComplaints > 0 ? round(($count / $totalDeptComplaints) * 100) : 0 }}%</span>
                        </div>
                    </div>
                    @empty
                    <p class="text-xs text-gray-400 font-bold italic text-center py-4">No department data</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Complaints Registry -->
        <div class="lg:col-span-2 bg-white rounded-[2.5rem] shadow-xl border border-[#163a24]/5 overflow-hidden">
            <div class="p-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-6">
                <h3 class="text-2xl font-black text-[#163a24]">Recent Complaints</h3>
                
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('admin.dashboard', ['status' => 'all']) }}" 
                       class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition {{ $status === 'all' ? 'bg-[#112d1c] text-[#f3bc3e]' : 'text-gray-400 hover:text-[#163a24]' }}">All</a>
                    <a href="{{ route('admin.dashboard', ['status' => 'pending']) }}" 
                       class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition {{ $status === 'pending' ? 'bg-[#112d1c] text-[#f3bc3e]' : 'text-gray-400 hover:text-[#163a24]' }}">Pending</a>
                    <a href="{{ route('admin.dashboard', ['status' => 'in_progress']) }}" 
                       class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition {{ $status === 'in_progress' ? 'bg-[#112d1c] text-[#f3bc3e]' : 'text-gray-400 hover:text-[#163a24]' }}">In Progress</a>
                    <a href="{{ route('admin.dashboard', ['status' => 'resolved']) }}" 
                       class="px-5 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition {{ $status === 'resolved' ? 'bg-[#112d1c] text-[#f3bc3e]' : 'text-gray-400 hover:text-[#163a24]' }}">Resolved</a>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-[#fef9e1]/50 border-y border-gray-50">
                        <tr class="text-left">
                            <th class="px-10 py-6 text-[8px] font-black text-gray-400 uppercase tracking-widest">Complaint</th>
                            <th class="px-10 py-6 text-[8px] font-black text-gray-400 uppercase tracking-widest">Submitter</th>
                            <th class="px-10 py-6 text-[8px] font-black text-gray-400 uppercase tracking-widest">Category</th>
                            <th class="px-10 py-6 text-[8px] font-black text-gray-400 uppercase tracking-widest">Date</th>
                            <th class="px-10 py-6"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @forelse($recentComplaints as $complaint)
                        <tr class="group hover:bg-gray-50 transition-colors cursor-pointer" onclick="window.location='{{ route('admin.complaints.show', $complaint) }}'">
                            <td class="px-10 py-8">
                                <div class="flex items-start gap-6">
                                    <div class="w-12 h-12 rounded-xl bg-gray-50 flex items-center justify-center font-black text-[#163a24] text-[10px] shrink-0">
                                        {{ strtoupper(substr($complaint->category, 0, 1)) }}{{ strtoupper(substr($complaint->category, -1, 1)) }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-black text-[#163a24] mb-1 group-hover:text-[#f3bc3e] transition">{{ $complaint->title }}</h4>
                                        <p class="text-[8px] font-bold text-gray-300 uppercase tracking-widest">ID: {{ $complaint->complaint_number }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-full bg-[#163a24] overflow-hidden">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($complaint->user->name) }}&background=163a24&color=fff" class="w-full h-full object-cover">
                                    </div>
                                    <span class="text-xs font-black text-[#163a24] whitespace-nowrap">{{ $complaint->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-10 py-8">
                                <span class="text-xs font-bold text-[#163a24]/60">{{ $complaint->category }}</span>
                            </td>
                            <td class="px-10 py-8">
                                <p class="text-[10px] font-bold text-[#163a24]">{{ $complaint->created_at->format('M d,') }}</p>
                                <p class="text-[10px] font-bold text-[#163a24]">{{ $complaint->created_at->format('Y') }}</p>
                            </td>
                            <td class="px-10 py-8 text-right">
                                <i class="fas fa-chevron-right text-gray-200 group-hover:text-[#163a24] transition-colors"></i>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-20 text-center text-gray-400 font-black uppercase tracking-widest text-xs">No Recent Activity</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="px-10 py-8 bg-[#fef9e1]/20 flex flex-col sm:flex-row justify-between items-center gap-6 border-t border-gray-50">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">
                    Showing {{ $recentComplaints->firstItem() }} to {{ $recentComplaints->lastItem() }} of {{ $recentComplaints->total() }} records
                </p>
                <div class="flex items-center gap-2 dashboard-pagination">
                    {{ $recentComplaints->appends(['status' => $status])->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .dashboard-pagination nav svg {
        width: 1rem;
        height: 1rem;
    }
    .dashboard-pagination nav div div p {
        display: none;
    }
    .fa-spin-slow {
        animation: spin-slow 3s linear infinite;
    }
</style>
@endsection
