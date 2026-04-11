@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto pb-20">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-10">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <h2 class="text-4xl font-black text-[#163a24] tracking-tight uppercase">My Complaints</h2>
                <span class="bg-[#f3bc3e]/20 text-[#163a24] px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">{{ $totalCount }} Total</span>
            </div>
            <p class="text-gray-500 font-bold text-sm leading-relaxed max-w-2xl">Review, track, and manage your formal submissions to the university administration. Records are maintained for academic integrity.</p>
        </div>
        
        <a href="{{ route('user.complaints.create') }}" class="px-8 py-4 bg-[#163a24] text-white rounded-2xl hover:bg-[#1a442a] font-black uppercase tracking-widest shadow-xl transition-all flex items-center gap-3">
            <i class="fas fa-plus"></i>
            <span>Create New Complaint</span>
        </a>
    </div>

    <!-- Filter Bar -->
    <form action="{{ route('user.complaints.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 mb-8">
        <div class="flex-1 relative">
            <span class="absolute inset-y-0 left-6 flex items-center text-gray-400">
                <i class="fas fa-search text-sm"></i>
            </span>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Complaint # or Keyword..." class="w-full bg-[#fef9e1] border-none rounded-2xl py-5 pl-14 pr-6 text-sm font-bold text-[#163a24] placeholder-gray-400 focus:ring-2 focus:ring-[#f3bc3e] transition">
        </div>
        
        <div class="relative min-w-[200px]">
            <select name="status" onchange="this.form.submit()" class="w-full h-full bg-[#fef9e1] border-none rounded-2xl px-6 py-5 appearance-none font-bold text-[#163a24] text-sm outline-none focus:ring-2 focus:ring-[#f3bc3e] transition cursor-pointer">
                <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
            <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-[#163a24]/30">
                <i class="fas fa-wave-square text-xs"></i>
            </div>
        </div>

        <button type="submit" class="bg-[#f2e19d] p-5 rounded-2xl text-[#163a24] hover:bg-[#f3bc3e] transition shadow-sm">
            <i class="fas fa-filter text-lg"></i>
        </button>
    </form>

    <!-- Table Container -->
    <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-[#163a24]/5">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-gray-50">
                        <th class="px-10 py-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Complaint ID</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Subject & Title</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Category</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Priority</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Submitted</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="px-10 py-8"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($complaints as $complaint)
                    <tr class="group hover:bg-gray-50/50 transition-colors cursor-pointer" onclick="window.location='{{ route('user.complaints.show', $complaint) }}'">
                        <td class="px-10 py-10">
                            <p class="text-[10px] font-black text-[#163a24] tracking-widest uppercase">{{ $complaint->complaint_number }}</p>
                        </td>
                        <td class="px-10 py-10 max-w-md">
                            <h4 class="text-lg font-black text-[#163a24] leading-tight mb-1 group-hover:text-[#f3bc3e] transition">{{ $complaint->title }}</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Ref: {{ $complaint->category }} - General Submission</p>
                        </td>
                        <td class="px-10 py-10">
                            <p class="text-sm font-bold text-[#163a24]/60">{{ $complaint->category }}</p>
                        </td>
                        <td class="px-10 py-10">
                            <div class="flex items-center gap-3">
                                <span class="w-2 h-2 rounded-full 
                                    @if($complaint->priority === 'High') bg-red-500 @elseif($complaint->priority === 'Medium') bg-[#f3bc3e] @else bg-green-500 @endif">
                                </span>
                                <span class="text-sm font-black text-[#163a24]">{{ $complaint->priority }}</span>
                            </div>
                        </td>
                        <td class="px-10 py-10">
                            <p class="text-sm font-bold text-[#163a24]/60">{{ $complaint->created_at->format('M d, Y') }}</p>
                        </td>
                        <td class="px-10 py-10 text-center">
                            <span class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest 
                                @if($complaint->status === 'pending') bg-[#163a24] text-[#f3bc3e] @elseif($complaint->status === 'in_progress') bg-green-100 text-green-700 @elseif($complaint->status === 'resolved') bg-blue-100 text-blue-700 @else bg-red-100 text-red-700 @endif">
                                {{ $complaint->status === 'pending' ? 'PENDING' : ($complaint->status === 'in_progress' ? 'IN PROGRESS' : ($complaint->status === 'resolved' ? 'RESOLVED' : 'REJECTED')) }}
                            </span>
                        </td>
                        <td class="px-10 py-10 text-right">
                            <i class="fas fa-chevron-right text-gray-200 group-hover:text-[#163a24] transition-colors"></i>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-20 text-center">
                            <p class="text-gray-400 font-black uppercase tracking-widest">No submissions found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Info -->
        <div class="px-10 py-8 bg-gray-50/50 flex items-center justify-between border-t border-gray-50">
            <p class="text-xs font-bold text-gray-400">Showing <span class="text-[#163a24]">1-{{ count($complaints) }}</span> of <span class="text-[#163a24]">{{ $totalCount }}</span> complaints</p>
            <div class="flex items-center gap-2">
                {{ $complaints->links() }}
            </div>
        </div>
    </div>

    
</div>

<!-- Chat Bubble FAB -->
<button class="fixed bottom-10 right-10 w-16 h-16 bg-[#f3bc3e] text-[#163a24] rounded-[1.5rem] flex items-center justify-center text-2xl shadow-2xl hover:scale-110 transition-all z-50 rotate-3">
    <i class="fas fa-comment-alt"></i>
</button>
@endsection
