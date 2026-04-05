@extends("layouts.app")

@section("content")
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Dashboard Overview</h2>
        <p class="text-gray-500 font-medium">Monitor and manage all submitted complaints.</p>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-10">
        <!-- Total -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 transition-transform hover:scale-[1.02]">
            <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-green-600">
                <i class="fas fa-file-alt text-xl"></i>
            </div>
            <div>
                <p class="text-[32px] font-black text-gray-900 leading-none mb-1">{{ array_sum($stats) }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Complaints</p>
            </div>
        </div>

        <!-- Pending -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 transition-transform hover:scale-[1.02]">
            <div class="w-12 h-12 bg-orange-50 rounded-xl flex items-center justify-center text-orange-500">
                <i class="fas fa-clock text-xl"></i>
            </div>
            <div>
                <p class="text-[32px] font-black text-gray-900 leading-none mb-1">{{ $stats["pending"] ?? 0 }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Pending</p>
            </div>
        </div>

        <!-- In Progress -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 transition-transform hover:scale-[1.02]">
            <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-500">
                <i class="fas fa-spinner text-xl fa-spin-slow"></i>
            </div>
            <div>
                <p class="text-[32px] font-black text-gray-900 leading-none mb-1">{{ $stats["in_progress"] ?? 0 }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">In Progress</p>
            </div>
        </div>

        <!-- Resolved -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 transition-transform hover:scale-[1.02]">
            <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-500">
                <i class="fas fa-check-circle text-xl"></i>
            </div>
            <div>
                <p class="text-[32px] font-black text-gray-900 leading-none mb-1">{{ $stats["resolved"] ?? 0 }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Resolved</p>
            </div>
        </div>

        <!-- Rejected -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 flex items-center gap-4 transition-transform hover:scale-[1.02]">
            <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center text-red-500">
                <i class="fas fa-times-circle text-xl"></i>
            </div>
            <div>
                <p class="text-[32px] font-black text-gray-900 leading-none mb-1">{{ $stats["rejected"] ?? 0 }}</p>
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Rejected</p>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Categories Progress -->
        <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center gap-2 mb-8">
                <i class="fas fa-chart-line text-[#00a651]"></i>
                <h3 class="text-xl font-bold text-gray-800">Complaints by Category</h3>
            </div>
            
            <div class="space-y-6">
                @php
                    $categories = ["Academic", "Facility", "Administrative", "IT/Technical", "Health & Safety"];
                    $total = array_sum($categoryStats);
                @endphp
                @foreach($categories as $category)
                    @php
                        $count = $categoryStats[$category] ?? 0;
                        $percentage = $total > 0 ? round(($count / $total) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-sm font-semibold text-gray-600">{{ $category }}</span>
                            <span class="text-xs font-bold text-gray-400">{{ $count }} ({{ $percentage }}%)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-[#00a651] h-full rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Complaints -->
        <div class="lg:col-span-3 bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-xl font-bold text-gray-800">Recent Complaints</h3>
                <a href="{{ route('admin.complaints') }}" class="text-[#00a651] font-bold text-sm flex items-center gap-1 hover:underline">
                    View all <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
            
            <div class="space-y-4">
                @forelse($recentComplaints as $complaint)
                    <div class="flex items-center justify-between p-4 rounded-2xl hover:bg-gray-50 transition border border-transparent hover:border-gray-100 cursor-pointer"
                         onclick="window.location='{{ route('admin.complaints.show', $complaint) }}'">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full flex items-center justify-center font-bold text-white shadow-md
                                @if($loop->index % 4 == 0) bg-emerald-400 @elseif($loop->index % 4 == 1) bg-blue-400 @elseif($loop->index % 4 == 2) bg-cyan-400 @else bg-emerald-400 @endif">
                                {{ strtoupper(substr($complaint->user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $complaint->user->name)[1] ?? '', 0, 1)) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 leading-tight">{{ $complaint->title }}</h4>
                                <p class="text-xs text-gray-400 font-medium mt-1">
                                    {{ $complaint->user->name }} • {{ $complaint->created_at->format('Y-m-d') }}
                                </p>
                            </div>
                        </div>
                        <div>
                            @if($complaint->status === 'pending')
                                <span class="px-4 py-1.5 rounded-full bg-orange-50 text-orange-500 text-[11px] font-bold border border-orange-100 flex items-center gap-2">
                                    <i class="fas fa-clock text-[10px]"></i> Pending
                                </span>
                            @elseif($complaint->status === 'in_progress')
                                <span class="px-4 py-1.5 rounded-full bg-blue-50 text-blue-500 text-[11px] font-bold border border-blue-100 flex items-center gap-2">
                                    <i class="fas fa-spinner text-[10px] fa-spin-slow"></i> In Progress
                                </span>
                            @elseif($complaint->status === 'resolved')
                                <span class="px-4 py-1.5 rounded-full bg-emerald-50 text-emerald-500 text-[11px] font-bold border border-emerald-100 flex items-center gap-2">
                                    <i class="fas fa-check-circle text-[10px]"></i> Resolved
                                </span>
                            @else
                                <span class="px-4 py-1.5 rounded-full bg-red-50 text-red-500 text-[11px] font-bold border border-red-100 flex items-center gap-2">
                                    <i class="fas fa-times-circle text-[10px]"></i> Rejected
                                </span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-folder-open text-gray-300 text-2xl"></i>
                        </div>
                        <p class="text-gray-400 font-medium">No recent complaints found.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .fa-spin-slow {
        animation: spin-slow 3s linear infinite;
    }
</style>
@endsection
