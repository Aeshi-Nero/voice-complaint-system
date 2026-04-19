@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto pb-20">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-10">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <h2 class="text-4xl font-black text-[#163a24] tracking-tight uppercase">My Complaints</h2>
                <span class="bg-[#f3bc3e]/20 text-[#163a24] px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest">{{ $totalCount }} Total</span>
            </div>
            <p class="text-gray-500 font-bold text-sm leading-relaxed max-w-2xl">Review, track, and manage your formal submissions to the university administration. Records are maintained for academic integrity.</p>
        </div>
    </div>

    <!-- Filter Bar Box -->
    <div class="bg-[#163a24] p-8 rounded-[3rem] shadow-xl mb-12 relative overflow-hidden">
        <!-- Abstract background pattern -->
        <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
        
        <form id="filterForm" action="{{ route('user.complaints.index') }}" method="GET" class="space-y-6 relative z-10">
            <!-- Row 1: Search -->
            <div class="relative">
                <span class="absolute inset-y-0 left-6 flex items-center text-[#f3bc3e]">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                       placeholder="Search by Complaint # or Keyword..." 
                       class="w-full bg-white/10 border-none rounded-2xl py-5 pl-14 pr-6 text-sm font-bold text-white placeholder-white/30 focus:ring-2 focus:ring-[#f3bc3e] transition backdrop-blur-sm">
            </div>
            
            <!-- Row 2: Filters -->
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                <!-- Status Filter -->
                <div class="relative">
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full bg-white/10 border-none rounded-2xl px-6 py-5 appearance-none font-bold text-white text-sm outline-none focus:ring-2 focus:ring-[#f3bc3e] transition cursor-pointer backdrop-blur-sm">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }} class="text-[#163a24]">All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }} class="text-[#163a24]">Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }} class="text-[#163a24]">In Progress</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }} class="text-[#163a24]">Resolved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }} class="text-[#163a24]">Rejected</option>
                    </select>
                    <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none">
                        <i class="fas fa-chevron-down text-xs text-[#f3bc3e]"></i>
                    </div>
                </div>

                <!-- Category Filter -->
                <div class="relative">
                    <select name="category" onchange="this.form.submit()" 
                            class="w-full bg-white/10 border-none rounded-2xl px-6 py-5 appearance-none font-bold text-white text-sm outline-none focus:ring-2 focus:ring-[#f3bc3e] transition cursor-pointer backdrop-blur-sm">
                        <option value="all" {{ request('category') == 'all' ? 'selected' : '' }} class="text-[#163a24]">All Categories</option>
                        <option value="Academic" {{ request('category') == 'Academic' ? 'selected' : '' }} class="text-[#163a24]">Academic</option>
                        <option value="Faculty" {{ request('category') == 'Faculty' ? 'selected' : '' }} class="text-[#163a24]">Faculty</option>
                        <option value="Administrative" {{ request('category') == 'Administrative' ? 'selected' : '' }} class="text-[#163a24]">Administrative</option>
                        <option value="IT/Technical" {{ request('category') == 'IT/Technical' ? 'selected' : '' }} class="text-[#163a24]">IT/Technical</option>
                        <option value="Health & Safety" {{ request('category') == 'Health & Safety' ? 'selected' : '' }} class="text-[#163a24]">Health & Safety</option>
                    </select>
                    <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none">
                        <i class="fas fa-list text-xs text-[#f3bc3e]"></i>
                    </div>
                </div>

                <!-- Sort Filter -->
                <div class="relative">
                    <select name="sort" onchange="this.form.submit()" 
                            class="w-full bg-white/10 border-none rounded-2xl px-6 py-5 appearance-none font-bold text-white text-sm outline-none focus:ring-2 focus:ring-[#f3bc3e] transition cursor-pointer backdrop-blur-sm">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }} class="text-[#163a24]">Recently Added</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }} class="text-[#163a24]">Oldest Complaints</option>
                    </select>
                    <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none">
                        <i class="fas fa-sort-amount-down text-xs text-[#f3bc3e]"></i>
                    </div>
                </div>

                <button type="submit" class="bg-[#f3bc3e] px-8 py-5 rounded-2xl text-[#163a24] font-black uppercase tracking-widest hover:bg-yellow-400 transition shadow-[0_4px_0_rgb(202,138,4)] active:shadow-none active:translate-y-1">
                    <div class="flex items-center justify-center gap-3">
                        <i class="fas fa-filter"></i>
                        <span>Apply</span>
                    </div>
                </button>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="bg-white rounded-[2.5rem] shadow-xl overflow-hidden border border-[#163a24]/5">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left border-b border-gray-50">
                        <th class="px-10 py-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Complaint ID</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Subject & Title</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Category</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">Submitted</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] text-center">Status</th>
                        <th class="px-10 py-8 text-[10px] font-black text-gray-300 uppercase tracking-[0.2em] text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($complaints as $complaint)
                    <tr class="group hover:bg-gray-50/50 transition-colors">
                        <td class="px-10 py-10">
                            <p class="text-[10px] font-black text-[#163a24] tracking-widest uppercase">{{ $complaint->complaint_number }}</p>
                        </td>
                        <td class="px-10 py-10 max-w-md">
                            <h4 class="text-lg font-black text-[#163a24] leading-tight mb-1 transition">{{ $complaint->title }}</h4>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">Ref: {{ $complaint->category }} - General Submission</p>
                        </td>
                        <td class="px-10 py-10">
                            <p class="text-sm font-bold text-[#163a24]/60">{{ $complaint->category }}</p>
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
                        <td class="px-10 py-10">
                            <div class="flex items-center justify-center gap-3">
                                <a href="{{ route('user.complaints.show', $complaint) }}" 
                                   class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="View Detail">
                                    <i class="fas fa-eye text-sm"></i>
                                </a>
                                @if($complaint->status === 'pending')
                                <a href="{{ route('user.complaints.edit', $complaint) }}" 
                                   class="w-10 h-10 bg-yellow-50 text-yellow-600 rounded-xl flex items-center justify-center hover:bg-yellow-600 hover:text-white transition-all shadow-sm" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </a>
                                <form action="{{ route('user.complaints.destroy', $complaint) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this complaint?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-10 h-10 bg-red-50 text-red-600 rounded-xl flex items-center justify-center hover:bg-red-600 hover:text-white transition-all shadow-sm" title="Delete">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-20 text-center">
                            <p class="text-gray-400 font-black uppercase tracking-widest">No submissions found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination Info -->
        <div class="px-10 py-8 bg-gray-50/50 flex items-center justify-between border-t border-gray-50">
            <p class="text-xs font-bold text-gray-400">Showing <span class="text-[#163a24]">{{ $complaints->firstItem() ?? 0 }}-{{ $complaints->lastItem() ?? 0 }}</span> of <span class="text-[#163a24]">{{ $complaints->total() }}</span> complaints</p>
            <div class="flex items-center gap-2 font-bold">
                {{ $complaints->links() }}
            </div>
        </div>
    </div>
</div>

<script>
    // Live Search with Debounce
    let debounceTimer;
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            filterForm.submit();
        }, 500); // Wait for 500ms after user stops typing
    });

    // Place cursor at the end of the input value on focus
    searchInput.addEventListener('focus', function() {
        const val = this.value;
        this.value = '';
        this.value = val;
    });
    
    // Auto-focus search if it was previously focused
    if ("{{ request('search') }}") {
        searchInput.focus();
    }
</script>
@endsection
