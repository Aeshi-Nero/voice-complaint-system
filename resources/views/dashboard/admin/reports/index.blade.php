@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto pb-20" x-data="reportManager()">
    <!-- Header -->
    <div class="mb-8 lg:mb-12 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
        <div>
            <h2 class="text-2xl lg:text-4xl font-black text-[#163a24] tracking-tight uppercase mb-2">Reports & Analytics</h2>
            <p class="text-gray-500 font-bold text-xs lg:text-sm leading-relaxed max-w-2xl">Visualized data insights and institutional metrics for complaints and polls.</p>
        </div>
        <div class="flex flex-wrap gap-3 lg:gap-4 w-full lg:w-auto">
            <a href="{{ route('admin.reports.export.csv', request()->all()) }}" class="flex-1 lg:flex-none justify-center bg-[#163a24] text-white px-4 lg:px-6 py-3 rounded-xl font-black uppercase tracking-widest text-[9px] lg:text-[10px] shadow-lg hover:bg-[#1a4d2a] transition-all flex items-center gap-2">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>
            <a href="{{ route('admin.reports.export.pdf', request()->all()) }}" target="_blank" class="flex-1 lg:flex-none justify-center bg-[#f3bc3e] text-[#163a24] px-4 lg:px-6 py-3 rounded-xl font-black uppercase tracking-widest text-[9px] lg:text-[10px] shadow-lg hover:bg-yellow-400 transition-all flex items-center gap-2">
                <i class="fas fa-file-pdf"></i> Generate PDF
            </a>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-3xl lg:rounded-[2.5rem] shadow-xl p-6 lg:p-8 border border-[#163a24]/5 mb-8 lg:mb-12">
        <form action="{{ route('admin.reports.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4 lg:gap-6">
            <div>
                <label class="text-[9px] lg:text-[10px] font-black text-[#163a24] uppercase tracking-widest mb-2 block">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full bg-[#fef9e1] border-none rounded-xl px-4 py-3 text-sm font-bold text-[#163a24] focus:ring-2 focus:ring-[#f3bc3e]">
            </div>
            <div>
                <label class="text-[9px] lg:text-[10px] font-black text-[#163a24] uppercase tracking-widest mb-2 block">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full bg-[#fef9e1] border-none rounded-xl px-4 py-3 text-sm font-bold text-[#163a24] focus:ring-2 focus:ring-[#f3bc3e]">
            </div>
            <div>
                <label class="text-[9px] lg:text-[10px] font-black text-[#163a24] uppercase tracking-widest mb-2 block">Category</label>
                <select name="category" class="w-full bg-[#fef9e1] border-none rounded-xl px-4 py-3 text-sm font-bold text-[#163a24] focus:ring-2 focus:ring-[#f3bc3e]">
                    <option value="">All Categories</option>
                    @php 
                        $cats = ['Academic', 'Faculty', 'Administrative', 'IT/Technical', 'Health & Safety'];
                    @endphp
                    @foreach($cats as $cat)
                        <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-[#163a24] text-white py-3 lg:py-4 rounded-xl font-black uppercase tracking-widest text-[9px] lg:text-[10px] shadow-lg hover:bg-[#1a4d2a] transition-all">
                    Generate Data View
                </button>
            </div>
        </form>
    </div>

    <!-- Analytics Charts -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-10 mb-12 lg:mb-20">
        <!-- 1. Category Chart (Replaced Status Chart) -->
        <div class="bg-white rounded-3xl lg:rounded-[2.5rem] shadow-xl border border-[#163a24]/5 p-6 lg:p-10">
            <h3 class="text-base lg:text-lg font-black text-[#163a24] uppercase tracking-tight mb-8 lg:mb-10">Complaints by Category</h3>
            
            <div class="relative flex justify-center mb-8 lg:mb-10">
                <div class="w-32 h-32 lg:w-48 lg:h-48 rounded-full border-[0.8rem] lg:border-[1.2rem] border-[#163a24] flex flex-col items-center justify-center">
                    <p class="text-xl lg:text-3xl font-black text-[#163a24] leading-none">{{ array_sum($categoryStats) }}</p>
                    <p class="text-[7px] lg:text-[8px] font-black text-gray-400 uppercase tracking-widest mt-1">Total Files</p>
                </div>
                <div class="absolute inset-0 w-32 h-32 lg:w-48 lg:h-48 mx-auto rounded-full border-[0.8rem] lg:border-[1.2rem] border-transparent border-t-[#f3bc3e] border-r-transparent rotate-[45deg]"></div>
            </div>

            <div class="space-y-4">
                @php
                    $totalComplaints = array_sum($categoryStats);
                    $catColors = [
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
                        <span class="w-2.5 h-2.5 lg:w-3 lg:h-3 rounded-full {{ $catColors[$category] ?? 'bg-gray-400' }}"></span>
                        <span class="text-[10px] lg:text-xs font-bold text-[#163a24]">{{ $category }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] lg:text-xs font-black text-[#163a24]">{{ $totalComplaints > 0 ? round(($count / $totalComplaints) * 100) : 0 }}%</span>
                        <p class="text-[8px] lg:text-[9px] font-bold text-gray-300 tracking-tighter">{{ $count }} items</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 font-bold italic">No data available</p>
                @endforelse
            </div>
        </div>

        <!-- 2. Department Chart -->
        <div class="bg-white rounded-3xl lg:rounded-[2.5rem] shadow-xl border border-[#163a24]/5 p-6 lg:p-10">
            <h3 class="text-base lg:text-lg font-black text-[#163a24] uppercase tracking-tight mb-8 lg:mb-10">Department Analytics</h3>
            
            <div class="relative flex justify-center mb-8 lg:mb-10">
                <div class="w-32 h-32 lg:w-48 lg:h-48 rounded-full border-[0.8rem] lg:border-[1.2rem] border-[#163a24] flex flex-col items-center justify-center">
                    <p class="text-xl lg:text-3xl font-black text-[#163a24] leading-none">{{ count($departmentStats) }}</p>
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mt-1">Departments</p>
                </div>
                <div class="absolute inset-0 w-48 h-48 mx-auto rounded-full border-[1.2rem] border-transparent border-t-green-400 border-l-transparent -rotate-[120deg]"></div>
            </div>

            <div class="space-y-4 overflow-y-auto max-h-[220px] custom-scrollbar pr-2">
                @php
                    $totalDept = array_sum($departmentStats);
                    $deptColors = ['bg-[#163a24]', 'bg-[#f3bc3e]', 'bg-green-600', 'bg-blue-600', 'bg-red-600', 'bg-purple-600', 'bg-orange-600'];
                @endphp
                @forelse($departmentStats as $dept => $count)
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="w-3 h-3 rounded-full {{ $deptColors[$loop->index % count($deptColors)] }}"></span>
                        <span class="text-xs font-bold text-[#163a24] truncate max-w-[100px]">{{ $dept }}</span>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-black text-[#163a24]">{{ $totalDept > 0 ? round(($count / $totalDept) * 100) : 0 }}%</span>
                        <p class="text-[9px] font-bold text-gray-300 tracking-tighter">{{ $count }} items</p>
                    </div>
                </div>
                @empty
                <p class="text-xs text-gray-400 font-bold italic text-center py-4">No department data available</p>
                @endforelse
            </div>
        </div>

        <!-- 3. Resolution Efficiency -->
        <div class="bg-[#163a24] rounded-[2.5rem] shadow-xl p-10 text-white relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-48 h-48 bg-white/5 rounded-full blur-3xl"></div>
            
            <h3 class="text-lg font-black uppercase tracking-tight mb-10 relative z-10">Resolution Efficiency</h3>
            
            @php
                $resolved = $stats['resolved'];
                $total = $stats['total'];
                $rate = $total > 0 ? round(($resolved / $total) * 100) : 0;
            @endphp

            <div class="relative flex justify-center mb-10">
                <div class="w-48 h-48 rounded-full border-[1.2rem] border-white/10 flex flex-col items-center justify-center">
                    <p class="text-5xl font-black text-[#f3bc3e] leading-none">{{ $rate }}%</p>
                    <p class="text-[8px] font-black text-white/40 uppercase tracking-widest mt-1">Success Rate</p>
                </div>
                <svg class="absolute inset-0 w-48 h-48 mx-auto -rotate-90">
                    <circle cx="96" cy="96" r="82.5" fill="transparent" stroke="#f3bc3e" stroke-width="24" 
                            stroke-dasharray="518" 
                            stroke-dashoffset="{{ 518 - ($rate / 100 * 518) }}" 
                            stroke-linecap="round" class="transition-all duration-1000 ease-out"></circle>
                </svg>
            </div>

            <div class="p-6 bg-white/5 rounded-3xl border border-white/10 relative z-10">
                <p class="text-[10px] font-bold text-white/60 mb-2 uppercase tracking-widest">Resolution Summary</p>
                <div class="flex justify-between items-end">
                    <div>
                        <p class="text-2xl font-black text-white">{{ $resolved }}</p>
                        <p class="text-[9px] font-bold text-green-400 uppercase tracking-tighter">Solved Issues</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-black text-white">{{ $total - $resolved }}</p>
                        <p class="text-[9px] font-bold text-yellow-400 uppercase tracking-tighter">Open or Closed</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Polls Report Section -->
    <div class="mt-20">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-[#163a24] rounded-2xl flex items-center justify-center text-[#f3bc3e]">
                    <i class="fas fa-poll text-xl"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-[#163a24] uppercase tracking-tighter">Polls Engagement Report</h3>
                    <p class="text-gray-400 font-bold text-xs uppercase tracking-widest">Tracking institutional feedback and student sentiment</p>
                </div>
            </div>

            <!-- Bulk Poll Actions -->
            <div x-show="selectedPolls.length > 0" x-cloak
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="flex items-center gap-4 bg-[#fef9e1] p-3 rounded-2xl border border-[#163a24]/10">
                <span class="text-[10px] font-black text-[#163a24] uppercase tracking-widest ml-2">
                    <span x-text="selectedPolls.length"></span> Selected
                </span>
                <button @click="exportPolls('csv')" class="bg-[#163a24] text-white px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-black transition flex items-center gap-2">
                    <i class="fas fa-file-csv"></i> CSV
                </button>
                <button @click="exportPolls('pdf')" class="bg-[#f3bc3e] text-[#163a24] px-4 py-2 rounded-lg text-[9px] font-black uppercase tracking-widest hover:bg-yellow-400 transition flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i> PDF
                </button>
                <button @click="selectedPolls = []" class="text-[#163a24]/40 hover:text-red-500 px-2 transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            @forelse($polls as $poll)
            <div class="bg-white rounded-[2.5rem] shadow-xl p-8 border-4 transition-all"
                 :class="selectedPolls.includes('{{ $poll->id }}') ? 'border-[#f3bc3e] scale-[0.98]' : 'border-transparent'">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex gap-4">
                        <!-- Checkbox -->
                        <div class="mt-1">
                            <input type="checkbox" 
                                   value="{{ $poll->id }}" 
                                   x-model="selectedPolls"
                                   class="w-5 h-5 rounded-lg border-[#163a24]/20 text-[#f3bc3e] focus:ring-[#f3bc3e] transition-all cursor-pointer">
                        </div>
                        <div>
                            <span class="text-[8px] font-black uppercase tracking-[0.2em] {{ $poll->status === 'active' ? 'text-green-500' : 'text-gray-400' }}">
                                {{ $poll->status }} &bull; {{ $poll->votes->count() }} Votes
                            </span>
                            <h4 class="text-lg font-black text-[#163a24] mt-1">{{ $poll->title }}</h4>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    @php $totalVotes = $poll->votes->count(); @endphp
                    @foreach($poll->options as $option)
                    <div class="relative">
                        <div class="flex justify-between items-center mb-1 px-1">
                            <span class="text-xs font-bold text-[#163a24]">{{ $option->option_text }}</span>
                            <span class="text-[10px] font-black text-[#163a24]">{{ $option->getPercentage($totalVotes) }}%</span>
                        </div>
                        <div class="h-3 w-full bg-[#fef9e1] rounded-full overflow-hidden border border-[#163a24]/5">
                            <div class="h-full bg-[#163a24] rounded-full transition-all duration-1000" 
                                 style="width: {{ $option->getPercentage($totalVotes) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-8 pt-6 border-t border-gray-50 flex items-center justify-between">
                    <p class="text-[9px] font-bold text-gray-400 uppercase">Created: {{ $poll->created_at->format('M d, Y') }}</p>
                    <span class="text-[9px] font-black text-[#163a24]/30 uppercase tracking-widest">ID: #{{ str_pad($poll->id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
            </div>
            @empty
            <div class="col-span-full py-20 text-center bg-white rounded-[2.5rem] border-2 border-dashed border-gray-100">
                <p class="text-gray-400 font-black uppercase tracking-widest">No polls found for reporting</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<script>
function reportManager() {
    return {
        selectedPolls: [],
        
        exportPolls(format) {
            if (this.selectedPolls.length === 0) return;
            
            const ids = this.selectedPolls.join(',');
            const baseUrl = format === 'csv' 
                ? "{{ route('admin.reports.polls.csv') }}" 
                : "{{ route('admin.reports.polls.pdf') }}";
            
            window.location.href = `${baseUrl}?ids=${ids}`;
        }
    }
}
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #163a2420;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #163a2440;
    }
</style>
@endsection
