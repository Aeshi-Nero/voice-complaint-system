@extends("layouts.app")

@section("content")
<div class="max-w-7xl mx-auto">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">My Complaints</h2>
            <p class="text-gray-500 font-medium">{{ $stats["total"] }} complaints submitted</p>
        </div>
        
        <a href="{{ route("user.complaints.create") }}" class="px-6 py-3 bg-[#00a651] text-white rounded-xl hover:bg-[#008d44] font-bold shadow-lg transition duration-200 flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> New Complaint
        </a>
    </div>
    
    <!-- Status Filter Pills -->
    <div class="flex flex-wrap items-center gap-3 mb-10">
        <div class="px-4 py-2 bg-orange-50 border border-orange-100 rounded-full flex items-center gap-2">
            <i class="fas fa-clock text-orange-400 text-xs"></i>
            <span class="text-xs font-bold text-orange-600 uppercase tracking-tight">Pending</span>
            <span class="text-xs font-black text-gray-500 ml-1">{{ $stats["pending"] }}</span>
        </div>
        
        <div class="px-4 py-2 bg-blue-50 border border-blue-100 rounded-full flex items-center gap-2">
            <i class="fas fa-spinner text-blue-400 text-xs"></i>
            <span class="text-xs font-bold text-blue-600 uppercase tracking-tight">In Progress</span>
            <span class="text-xs font-black text-gray-500 ml-1">{{ $stats["in_progress"] }}</span>
        </div>
        
        <div class="px-4 py-2 bg-green-50 border border-green-100 rounded-full flex items-center gap-2">
            <i class="fas fa-check-circle text-green-400 text-xs"></i>
            <span class="text-xs font-bold text-green-600 uppercase tracking-tight">Resolved</span>
            <span class="text-xs font-black text-gray-500 ml-1">{{ $stats["resolved"] }}</span>
        </div>
        
        <div class="px-4 py-2 bg-red-50 border border-red-100 rounded-full flex items-center gap-2">
            <i class="fas fa-times-circle text-red-400 text-xs"></i>
            <span class="text-xs font-bold text-red-600 uppercase tracking-tight">Rejected</span>
            <span class="text-xs font-black text-gray-500 ml-1">{{ $stats["rejected"] }}</span>
        </div>
    </div>
    
    <!-- Complaints Cards List -->
    <div class="space-y-6">
        @forelse($complaints as $complaint)
        <div class="bg-white rounded-2xl shadow-sm border-y border-r border-gray-100 overflow-hidden flex cursor-pointer hover:shadow-md transition group relative"
             onclick="window.location='{{ route("user.complaints.show", $complaint) }}'">
            
            <!-- Left status border -->
            <div class="w-1.5 self-stretch @if($complaint->status === "pending") bg-orange-400 @elseif($complaint->status === "in_progress") bg-blue-400 @elseif($complaint->status === "resolved") bg-green-400 @else bg-red-400 @endif"></div>
            
            <div class="flex-1 p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-gray-300 tracking-widest uppercase">{{ $complaint->complaint_number }}</span>
                        <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-black uppercase rounded-lg border border-green-100">
                            {{ $complaint->category }}
                        </span>
                        <span class="px-3 py-1 @if($complaint->priority === 'High') bg-red-50 text-red-500 @elseif($complaint->priority === 'Medium') bg-orange-50 text-orange-500 @else bg-gray-50 text-gray-500 @endif text-[10px] font-black uppercase rounded-lg border @if($complaint->priority === 'High') border-red-100 @elseif($complaint->priority === 'Medium') border-orange-100 @else border-gray-100 @endif">
                            {{ $complaint->priority }}
                        </span>
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
                            <span class="px-4 py-1.5 rounded-full bg-green-50 text-green-500 text-[11px] font-bold border border-green-100 flex items-center gap-2">
                                <i class="fas fa-check-circle text-[10px]"></i> Resolved
                            </span>
                        @else
                            <span class="px-4 py-1.5 rounded-full bg-red-50 text-red-500 text-[11px] font-bold border border-red-100 flex items-center gap-2">
                                <i class="fas fa-times-circle text-[10px]"></i> Rejected
                            </span>
                        @endif
                    </div>
                </div>
                
                <div class="flex items-end justify-between">
                    <div>
                        <h3 class="text-xl font-black text-gray-800 leading-tight mb-2 group-hover:text-[#00a651] transition">
                            {{ $complaint->title }}
                        </h3>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">
                            Submitted on {{ $complaint->created_at->format("Y-m-d") }}
                        </p>
                    </div>
                    
                    <div class="text-[#00a651] opacity-30 group-hover:opacity-100 transition translate-x-2 group-hover:translate-x-0">
                        <i class="fas fa-chevron-right text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="bg-white rounded-3xl p-16 text-center border-2 border-dashed border-gray-100">
            <div class="bg-gray-50 w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-clipboard-list text-gray-200 text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-800 mb-2">No complaints yet</h3>
            <p class="text-gray-400 font-medium mb-8">You haven't submitted any complaints. Your submissions will appear here.</p>
            <a href="{{ route("user.complaints.create") }}" class="px-8 py-3 bg-[#00a651] text-white rounded-xl font-bold shadow-lg inline-flex items-center gap-2 hover:bg-[#008d44] transition">
                <i class="fas fa-plus"></i> Submit Your First Complaint
            </a>
        </div>
        @endforelse
    </div>
    
    <div class="mt-10">
        {{ $complaints->links() }}
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
