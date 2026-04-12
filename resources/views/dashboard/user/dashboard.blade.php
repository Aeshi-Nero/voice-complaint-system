@extends("layouts.app")

@section("content")
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-12">
        <div>
            <p class="text-[10px] font-black text-[#163a24]/40 uppercase tracking-[0.3em] mb-2">{{ now()->format('l, F d, Y') }}</p>
            <h2 class="text-5xl font-black text-[#163a24] tracking-tight">Welcome back, {{ auth()->user()->name }}!</h2>
        </div>
        
        <!-- Submissions Widget -->
        <div class="bg-[#f2e19d]/40 rounded-3xl p-6 flex items-center gap-6 border border-[#163a24]/5">
            <div class="relative w-16 h-16 flex items-center justify-center">
                <svg class="w-full h-full transform -rotate-90">
                    <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="transparent" class="text-white/20" />
                    <circle cx="32" cy="32" r="28" stroke="currentColor" stroke-width="4" fill="transparent" stroke-dasharray="175.9" stroke-dashoffset="{{ 175.9 * (1 - auth()->user()->getRemainingComplaints()/6) }}" class="text-[#163a24]" />
                </svg>
                <span class="absolute text-xs font-black text-[#163a24]">{{ auth()->user()->getRemainingComplaints() }}/6</span>
            </div>
            <div>
                <p class="text-sm font-black text-[#163a24] leading-tight">Submissions Available</p>
                <p class="text-[10px] font-bold text-[#163a24]/40 uppercase tracking-tight mt-0.5">You have {{ auth()->user()->getRemainingComplaints() }}/6 submissions left today</p>
            </div>
        </div>
    </div>
    
    <!-- Status Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
        <div class="bg-white rounded-3xl p-8 border-b-8 border-[#f3bc3e] shadow-sm group hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-start mb-10">
                <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-[#f3bc3e] text-xl">
                    <i class="fas fa-comment-dots"></i>
                </div>
                <span class="text-[10px] font-black text-[#f3bc3e] uppercase tracking-widest">Status</span>
            </div>
            <p class="text-4xl font-black text-[#163a24] leading-none mb-1">{{ $stats["pending"] }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Pending Review</p>
        </div>
        
        <div class="bg-white rounded-3xl p-8 border-b-8 border-[#22c55e] shadow-sm group hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-start mb-10">
                <div class="w-12 h-12 bg-green-50 rounded-2xl flex items-center justify-center text-[#22c55e] text-xl">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <span class="text-[10px] font-black text-[#22c55e] uppercase tracking-widest">Active</span>
            </div>
            <p class="text-4xl font-black text-[#163a24] leading-none mb-1">{{ $stats["in_progress"] }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">In Progress</p>
        </div>
        
        <div class="bg-white rounded-3xl p-8 border-b-8 border-[#163a24] shadow-sm group hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-start mb-10">
                <div class="w-12 h-12 bg-gray-50 rounded-2xl flex items-center justify-center text-[#163a24] text-xl">
                    <i class="fas fa-check-circle"></i>
                </div>
                <span class="text-[10px] font-black text-[#163a24] uppercase tracking-widest">Complete</span>
            </div>
            <p class="text-4xl font-black text-[#163a24] leading-none mb-1">{{ $stats["resolved"] }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Resolved Cases</p>
        </div>

        <div class="bg-white rounded-3xl p-8 border-b-8 border-[#ef4444] shadow-sm group hover:shadow-xl transition-all duration-300">
            <div class="flex justify-between items-start mb-10">
                <div class="w-12 h-12 bg-red-50 rounded-2xl flex items-center justify-center text-[#ef4444] text-xl">
                    <i class="fas fa-times-circle"></i>
                </div>
                <span class="text-[10px] font-black text-[#ef4444] uppercase tracking-widest">Declined</span>
            </div>
            <p class="text-4xl font-black text-[#163a24] leading-none mb-1">{{ $stats["rejected"] }}</p>
            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Rejected/Closed</p>
        </div>
    </div>
    
    <!-- Recent Complaints Section -->
    <div class="bg-[#f2e19d]/30 rounded-[3rem] p-10 mb-12 border border-[#163a24]/5">
        <div class="flex items-center justify-between mb-10">
            <h3 class="text-2xl font-black text-[#163a24] flex items-center gap-4">
                <span class="w-1.5 h-8 bg-[#f3bc3e] rounded-full"></span>
                My Recent Complaints
            </h3>
            <a href="{{ route('user.complaints.index') }}" class="text-[10px] font-black text-[#163a24] hover:text-[#f3bc3e] uppercase tracking-widest transition flex items-center gap-2">
                View All Submissions <i class="fas fa-chevron-right text-[8px]"></i>
            </a>
        </div>

        <div class="space-y-4">
            @forelse($complaints->take(4) as $complaint)
            <div class="bg-white rounded-3xl p-8 flex flex-col md:flex-row md:items-center justify-between gap-6 hover:shadow-lg transition-all duration-300 group cursor-pointer"
                 onclick="window.location='{{ route("user.complaints.show", $complaint) }}'">
                
                <div class="flex items-start gap-8">
                    <div class="min-w-[120px]">
                        <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1">Case ID</p>
                        <p class="text-xs font-black text-[#163a24]">{{ $complaint->complaint_number }}</p>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-[#163a24] leading-tight mb-1 group-hover:text-[#f3bc3e] transition">{{ $complaint->title }}</h4>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-tight">
                            Submitted via Online Portal • {{ $complaint->category }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-12">
                    <div class="text-right hidden sm:block">
                        <p class="text-[8px] font-black text-gray-300 uppercase tracking-widest mb-1">Submitted On</p>
                        <p class="text-xs font-black text-[#163a24]">{{ $complaint->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="min-w-[120px] text-center">
                        <span class="px-6 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest 
                            @if($complaint->status === 'pending') bg-[#163a24] text-[#f3bc3e] @elseif($complaint->status === 'in_progress') bg-green-100 text-green-700 @elseif($complaint->status === 'resolved') bg-blue-100 text-blue-700 @else bg-red-100 text-red-700 @endif">
                            {{ $complaint->status === 'pending' ? 'PENDING' : ($complaint->status === 'in_progress' ? 'IN PROGRESS' : ($complaint->status === 'resolved' ? 'RESOLVED' : 'REJECTED')) }}
                        </span>
                    </div>
                    <i class="fas fa-chevron-right text-gray-200 group-hover:text-[#163a24] transition-colors"></i>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-3xl p-20 text-center">
                <p class="text-gray-400 font-black uppercase tracking-widest">No recent submissions found</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Bottom Widgets -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pb-20">
        <!-- Banner Widget -->
        <div class="lg:col-span-2 relative h-64 rounded-[3rem] overflow-hidden group shadow-xl">
            <img src="https://images.unsplash.com/photo-1541339907198-e08756ebafe3?auto=format&fit=crop&q=80&w=1000" class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
            <div class="absolute inset-0 bg-gradient-to-t from-[#163a24] via-[#163a24]/40 to-transparent"></div>
            <div class="absolute bottom-0 left-0 p-10">
                <h3 class="text-4xl font-black text-white mb-2">Student Advocacy Program 2024</h3>
                <p class="text-white/70 text-sm font-bold max-w-xl">Learn how Aldersgate College is empowering student voices through our new digital feedback ecosystem and policy reforms.</p>
            </div>
        </div>

        <!-- Pro-Tip Widget -->
        <div class="bg-[#f3bc3e]/20 rounded-[3rem] p-10 border border-[#f3bc3e]/20 flex flex-col items-center justify-center text-center shadow-sm">
            <div class="bg-[#f3bc3e] w-12 h-12 rounded-full flex items-center justify-center text-[#163a24] text-xl mb-6 shadow-lg">
                <i class="fas fa-lightbulb"></i>
            </div>
            <h4 class="text-xl font-black text-[#163a24] mb-3 uppercase tracking-tight">Pro-Tip</h4>
            <p class="text-sm font-bold text-[#163a24]/60 italic">"Detailed descriptions with photos often lead to 40% faster resolution times."</p>
        </div>
    </div>
</div>

<!-- Floating Action Button -->
<a href="{{ route('user.complaints.create') }}" class="fixed bottom-10 right-10 w-20 h-20 bg-[#163a24] text-white rounded-3xl flex items-center justify-center text-3xl shadow-[0_15px_30px_rgba(22,58,36,0.3)] hover:bg-[#1a442a] transition-all hover:-translate-y-2 active:scale-95 z-50">
    <i class="fas fa-plus"></i>
</a>
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
