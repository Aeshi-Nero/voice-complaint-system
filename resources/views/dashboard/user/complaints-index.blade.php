@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto pb-20">
    <!-- Header Section -->
    <div class="flex flex-col gap-4 mb-8">
        <div>
            <h2 class="text-3xl md:text-4xl font-black text-[#163a24] tracking-tight uppercase mb-1">My Complaints</h2>
            <p class="text-gray-500 font-bold text-xs md:text-sm leading-relaxed max-w-2xl">Track and manage your formal submissions. Records are maintained for academic integrity.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="bg-[#163a24] text-[#f3bc3e] px-4 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-[#163a24]/20">
                {{ $totalCount }} Submissions
            </span>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white p-6 md:p-8 rounded-[2rem] md:rounded-[2.5rem] shadow-sm border border-gray-100 mb-8 md:mb-12">
        <form id="filterForm" action="{{ route('user.complaints.index') }}" method="GET" class="space-y-6">
            <div class="relative group">
                <span class="absolute inset-y-0 left-5 flex items-center text-gray-400 group-focus-within:text-[#00a651] transition-colors">
                    <i class="fas fa-search text-sm"></i>
                </span>
                <input type="text" name="search" id="searchInput" value="{{ request('search') }}" 
                       placeholder="Search by ID or Title..." 
                       class="w-full bg-gray-50 border-none rounded-2xl py-4 pl-14 pr-6 text-sm font-bold text-[#163a24] placeholder-gray-300 focus:ring-2 focus:ring-[#00a651] transition-all">
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Status Filter -->
                <div class="relative">
                    <select name="status" onchange="this.form.submit()" 
                            class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 appearance-none font-bold text-[#163a24] text-xs outline-none focus:ring-2 focus:ring-[#00a651] transition cursor-pointer">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Statuses</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-6 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none text-[10px]"></i>
                </div>

                <!-- Category Filter -->
                <div class="relative">
                    <select name="category" onchange="this.form.submit()" 
                            class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 appearance-none font-bold text-[#163a24] text-xs outline-none focus:ring-2 focus:ring-[#00a651] transition cursor-pointer">
                        <option value="all" {{ request('category') == 'all' ? 'selected' : '' }}>All Categories</option>
                        @foreach(['Academic', 'Faculty', 'Administrative', 'IT/Technical', 'Health & Safety'] as $cat)
                            <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                    <i class="fas fa-list absolute right-6 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none text-[10px]"></i>
                </div>

                <!-- Sort Filter -->
                <div class="relative">
                    <select name="sort" onchange="this.form.submit()" 
                            class="w-full bg-gray-50 border-none rounded-2xl px-6 py-4 appearance-none font-bold text-[#163a24] text-xs outline-none focus:ring-2 focus:ring-[#00a651] transition cursor-pointer">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Recently Added</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                    </select>
                    <i class="fas fa-sort-amount-down absolute right-6 top-1/2 -translate-y-1/2 text-gray-300 pointer-events-none text-[10px]"></i>
                </div>

                <button type="submit" class="bg-[#00a651] text-white px-8 py-4 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lg shadow-[#00a651]/20 hover:bg-emerald-600 transition-all active:scale-[0.98]">
                    <div class="flex items-center justify-center gap-3">
                        <i class="fas fa-filter"></i>
                        <span>Refresh View</span>
                    </div>
                </button>
            </div>
        </form>
    </div>

    <!-- Mobile View (Cards) -->
    <div class="lg:hidden space-y-4">
        @forelse($complaints as $complaint)
        <div class="bg-white rounded-[2rem] p-6 shadow-sm border border-gray-100 active:scale-[0.98] transition-all cursor-pointer" onclick="window.location='{{ route('user.complaints.show', $complaint) }}'">
            <div class="flex justify-between items-start mb-4">
                <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">{{ $complaint->complaint_number }}</span>
                <span class="px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest 
                    @if($complaint->status === 'pending') bg-orange-50 text-orange-500 @elseif($complaint->status === 'in_progress') bg-blue-50 text-blue-500 @elseif($complaint->status === 'resolved') bg-green-50 text-green-500 @else bg-red-50 text-red-500 @endif">
                    {{ str_replace('_', ' ', $complaint->status) }}
                </span>
            </div>
            
            <div class="mb-6">
                <h4 class="text-lg font-black text-[#163a24] leading-tight mb-2">{{ $complaint->title }}</h4>
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $complaint->category }}</span>
                    <span class="w-1 h-1 rounded-full bg-gray-200"></span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $complaint->created_at->format('M d, Y') }}</span>
                </div>
            </div>
            
            <div class="flex items-center justify-between pt-4 border-t border-gray-50">
                <div class="flex gap-2">
                    <a href="{{ route('user.complaints.show', $complaint) }}" class="w-10 h-10 bg-gray-50 text-[#163a24] rounded-xl flex items-center justify-center">
                        <i class="fas fa-eye text-xs"></i>
                    </a>
                    @if($complaint->status === 'pending')
                    <form action="{{ route('user.complaints.destroy', $complaint) }}" method="POST" onsubmit="return confirm('Delete this complaint?');" onclick="event.stopPropagation();">
                        @csrf @method('DELETE')
                        <button type="submit" class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center">
                            <i class="fas fa-trash-alt text-xs"></i>
                        </button>
                    </form>
                    @endif
                </div>
                <i class="fas fa-chevron-right text-gray-200"></i>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-[2rem] p-12 text-center border border-dashed border-gray-200">
            <i class="fas fa-folder-open text-gray-200 text-4xl mb-4"></i>
            <p class="text-gray-400 font-black uppercase tracking-widest text-[10px]">No submissions recorded</p>
        </div>
        @endforelse
    </div>

    <!-- Desktop View (Table) -->
    <div class="hidden lg:block bg-white rounded-[2.5rem] shadow-sm overflow-hidden border border-gray-100">
        <table class="w-full">
            <thead>
                <tr class="text-left bg-gray-50/50 border-b border-gray-100">
                    <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">ID</th>
                    <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Complaint Details</th>
                    <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest">Category</th>
                    <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Status</th>
                    <th class="px-10 py-6 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($complaints as $complaint)
                <tr class="group hover:bg-gray-50/50 transition-colors cursor-pointer" onclick="window.location='{{ route('user.complaints.show', $complaint) }}'">
                    <td class="px-10 py-8">
                        <span class="text-[10px] font-black text-gray-300 group-hover:text-[#163a24] transition-colors">{{ $complaint->complaint_number }}</span>
                    </td>
                    <td class="px-10 py-8">
                        <h4 class="text-sm font-black text-[#163a24] mb-1 uppercase tracking-tight">{{ $complaint->title }}</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase">{{ $complaint->created_at->format('F d, Y | h:i A') }}</p>
                    </td>
                    <td class="px-10 py-8">
                        <span class="text-[10px] font-black text-gray-500 uppercase tracking-widest">{{ $complaint->category }}</span>
                    </td>
                    <td class="px-10 py-8 text-center">
                        <span class="px-5 py-2 rounded-xl text-[9px] font-black uppercase tracking-widest 
                            @if($complaint->status === 'pending') bg-orange-100 text-orange-600 @elseif($complaint->status === 'in_progress') bg-blue-100 text-blue-600 @elseif($complaint->status === 'resolved') bg-green-100 text-green-600 @else bg-red-100 text-red-600 @endif">
                            {{ str_replace('_', ' ', $complaint->status) }}
                        </span>
                    </td>
                    <td class="px-10 py-8">
                        <div class="flex items-center justify-end gap-2" onclick="event.stopPropagation();">
                            <a href="{{ route('user.complaints.show', $complaint) }}" class="w-9 h-9 bg-gray-50 text-gray-400 hover:bg-[#163a24] hover:text-[#f3bc3e] rounded-xl flex items-center justify-center transition-all">
                                <i class="fas fa-eye text-xs"></i>
                            </a>
                            @if($complaint->status === 'pending')
                            <form action="{{ route('user.complaints.destroy', $complaint) }}" method="POST" onsubmit="return confirm('Delete record?');" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="w-9 h-9 bg-gray-50 text-gray-400 hover:bg-red-500 hover:text-white rounded-xl flex items-center justify-center transition-all">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-20 text-center">
                        <p class="text-gray-300 font-black uppercase tracking-widest text-xs">Registry is currently empty</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $complaints->appends(request()->query())->links() }}
    </div>
</div>

<script>
    let debounceTimer;
    const searchInput = document.getElementById('searchInput');
    const filterForm = document.getElementById('filterForm');

    searchInput.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            filterForm.submit();
        }, 600);
    });

    searchInput.addEventListener('focus', function() {
        const val = this.value;
        this.value = '';
        this.value = val;
    });
    
    if ("{{ request('search') }}") {
        searchInput.focus();
    }
</script>
@endsection
