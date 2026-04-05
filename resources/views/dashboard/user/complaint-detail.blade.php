@extends("layouts.app")

@section("content")
<div class="max-w-4xl mx-auto">
    <!-- Header Section -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('user.dashboard') }}" class="text-gray-400 hover:text-[#00a651] transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <span class="text-xs font-black text-gray-400 tracking-widest uppercase">{{ $complaint->complaint_number }}</span>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $complaint->title }}</h2>
        </div>
        
        <div>
            @if($complaint->status === 'pending')
                <span class="px-6 py-2 rounded-full bg-orange-50 text-orange-500 text-sm font-bold border border-orange-100 flex items-center gap-2 shadow-sm">
                    <i class="fas fa-clock"></i> Pending Review
                </span>
            @elseif($complaint->status === 'in_progress')
                <span class="px-6 py-2 rounded-full bg-blue-50 text-blue-500 text-sm font-bold border border-blue-100 flex items-center gap-2 shadow-sm">
                    <i class="fas fa-spinner fa-spin-slow"></i> Investigation In Progress
                </span>
            @elseif($complaint->status === 'resolved')
                <span class="px-6 py-2 rounded-full bg-emerald-50 text-emerald-500 text-sm font-bold border border-emerald-100 flex items-center gap-2 shadow-sm">
                    <i class="fas fa-check-circle"></i> Resolved
                </span>
            @else
                <span class="px-6 py-2 rounded-full bg-red-50 text-red-500 text-sm font-bold border border-red-100 flex items-center gap-2 shadow-sm">
                    <i class="fas fa-times-circle"></i> Rejected
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sm:p-10 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-[#00a651]"></div>
                
                <div class="mb-8">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Detailed Description</h3>
                    <div class="prose max-w-none text-gray-700 font-medium leading-relaxed whitespace-pre-wrap">{{ $complaint->description }}</div>
                </div>

                @if($complaint->image_path)
                    <div class="pt-8 border-t border-gray-50">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Evidence Attachment</h3>
                        <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-inner bg-gray-50">
                            <img src="{{ Storage::url($complaint->image_path) }}" alt="Evidence" class="w-full h-auto object-cover max-h-[500px]">
                        </div>
                    </div>
                @endif
            </div>

            @if($complaint->admin_notes)
                <div class="bg-gray-800 rounded-3xl shadow-xl p-8 sm:p-10 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-8 opacity-10">
                        <i class="fas fa-comment-dots text-6xl"></i>
                    </div>
                    <h3 class="text-xs font-black text-white/40 uppercase tracking-widest mb-4">Response from Administration</h3>
                    <p class="text-lg font-semibold leading-relaxed">{{ $complaint->admin_notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Complaint Meta</h3>
                
                <div class="space-y-6">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center text-[#00a651] shrink-0">
                            <i class="fas fa-tag"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-0.5">Category</p>
                            <p class="font-bold text-gray-800">{{ $complaint->category }}</p>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-500 shrink-0">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-0.5">Priority Level</p>
                            <span class="px-2 py-0.5 rounded-lg @if($complaint->priority === 'High') bg-red-100 text-red-600 @elseif($complaint->priority === 'Medium') bg-orange-100 text-orange-600 @else bg-gray-100 text-gray-600 @endif text-[10px] font-black uppercase tracking-tight">
                                {{ $complaint->priority }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-500 shrink-0">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-0.5">Submission Date</p>
                            <p class="font-bold text-gray-800">{{ $complaint->created_at->format('M d, Y') }}</p>
                            <p class="text-[10px] font-bold text-gray-400 uppercase mt-0.5">{{ $complaint->created_at->format('h:i A') }}</p>
                        </div>
                    </div>

                    @if($complaint->resolved_at)
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-500 shrink-0">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div>
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-tighter mb-0.5">Resolved Date</p>
                            <p class="font-bold text-gray-800">{{ $complaint->resolved_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($complaint->status === 'rejected' && !auth()->user()->is_blocked)
                <div class="bg-red-50 rounded-3xl p-8 border border-red-100">
                    <h4 class="font-bold text-red-800 mb-2">Need to try again?</h4>
                    <p class="text-xs text-red-600 mb-6 leading-relaxed">If your complaint was rejected, please review the notes and feel free to submit a revised concern.</p>
                    <a href="{{ route('user.complaints.create') }}" class="block w-full text-center py-3 bg-red-600 text-white rounded-xl font-bold hover:bg-red-700 transition shadow-lg shadow-red-200">
                        Submit New
                    </a>
                </div>
            @endif
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
