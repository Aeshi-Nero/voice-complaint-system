@extends("layouts.app")

@section('content')
<div class="max-w-full mx-auto pb-20" x-data="{ newMessages: false }" x-init="
    LiveUpdate.lastCount['chat_{{ $complaint->id }}'] = {{ $complaint->messages->count() }};
    setInterval(() => {
        LiveUpdate.check('chat_{{ $complaint->id }}', '{{ route('complaints.messages.get', $complaint) }}', () => {
            newMessages = true;
        });
    }, 4000);
">
    <!-- Live Chat Notification -->
    <div x-show="newMessages" x-transition x-cloak class="fixed bottom-10 left-1/2 -translate-x-1/2 z-[100]">
        <button @click="window.location.reload()" class="bg-[#163a24] text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-4 hover:scale-105 transition-all border border-[#f3bc3e]/30">
            <i class="fas fa-comment-dots animate-bounce text-[#f3bc3e]"></i>
            <span class="text-xs font-black uppercase tracking-widest">New reply from Admin. Refresh to view.</span>
        </button>
    </div>
    <!-- Header Section -->
    <div class="mb-10 flex flex-col sm:flex-row sm:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('user.dashboard') }}" class="text-gray-400 hover:text-[#00a651] transition-all transform hover:-translate-x-1">
                    <i class="fas fa-arrow-left text-lg"></i>
                </a>
                <span class="text-xs font-black text-gray-400 tracking-widest uppercase bg-white px-3 py-1 rounded-full shadow-sm border border-gray-100">{{ $complaint->complaint_number }}</span>
            </div>
            <h2 class="text-4xl font-black text-gray-900 tracking-tight leading-tight">{{ $complaint->title }}</h2>
        </div>
        
        <div class="shrink-0">
            @if($complaint->status === 'pending')
                <span class="px-8 py-3 rounded-2xl bg-orange-50 text-orange-500 text-sm font-black border border-orange-100 flex items-center gap-3 shadow-sm uppercase tracking-wider">
                    <span class="w-2.5 h-2.5 rounded-full bg-orange-500 animate-pulse"></span>
                    Pending Review
                </span>
            @elseif($complaint->status === 'in_progress')
                <span class="px-8 py-3 rounded-2xl bg-blue-50 text-blue-500 text-sm font-black border border-blue-100 flex items-center gap-3 shadow-sm uppercase tracking-wider">
                    <i class="fas fa-spinner fa-spin-slow"></i>
                    In Progress
                </span>
            @elseif($complaint->status === 'resolved')
                <span class="px-8 py-3 rounded-2xl bg-emerald-50 text-emerald-500 text-sm font-black border border-emerald-100 flex items-center gap-3 shadow-sm uppercase tracking-wider">
                    <i class="fas fa-check-circle"></i>
                    Resolved
                </span>
            @else
                <span class="px-8 py-3 rounded-2xl bg-red-50 text-red-500 text-sm font-black border border-red-100 flex items-center gap-3 shadow-sm uppercase tracking-wider">
                    <i class="fas fa-times-circle"></i>
                    Rejected
                </span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 items-start">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-10">
            
            <!-- Category & Status Quick Info -->
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-8 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-1.5 bg-[#00a651]"></div>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-6">
                    <div class="p-4 bg-gray-50/50 rounded-2xl border border-gray-50">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Category</p>
                        <p class="font-bold text-gray-800">{{ $complaint->category }}</p>
                    </div>
                    <div class="p-4 bg-gray-50/50 rounded-2xl border border-gray-50">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Priority</p>
                        <p class="font-bold text-gray-800">{{ $complaint->priority ?? 'Medium' }}</p>
                    </div>
                    <div class="p-4 bg-gray-50/50 rounded-2xl border border-gray-50">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Submitted On</p>
                        <p class="font-bold text-gray-800">{{ $complaint->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Conversation History Card (Horizontally Expanded) -->
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-8 border-b border-gray-50 bg-white">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#00a651]"></span>
                        Official Conversation History
                    </h3>
                </div>
                
                <div id="chat-messages" class="bg-gray-50/30 p-8 h-[750px] overflow-y-auto space-y-8 shadow-inner">
                    <!-- Original Complaint (User Sent - Green) -->
                    <div class="bg-[#00a651] p-8 rounded-[32px] shadow-lg relative overflow-hidden border border-[#008d44]">
                        <div class="absolute top-0 left-0 w-1.5 h-full bg-white/20"></div>
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-8 h-8 rounded-xl bg-white text-[#00a651] flex items-center justify-center font-black text-xs">
                                {{ strtoupper(substr($complaint->user->name, 0, 1)) }}
                            </div>
                            <p class="text-xs font-black text-white uppercase tracking-widest">{{ $complaint->user->name }} (You)</p>
                        </div>
                        <h4 class="text-xl font-black text-white mb-3">{{ $complaint->title }}</h4>
                        <p class="text-white/90 leading-relaxed text-lg">{{ $complaint->description }}</p>
                        <div class="mt-6 pt-4 border-t border-white/10 flex items-center justify-between">
                            <span class="text-[10px] text-white/60 font-bold uppercase tracking-widest">{{ $complaint->created_at->format('M d, Y | h:i A') }}</span>
                            <span class="px-3 py-1 bg-white/10 text-white text-[9px] font-black rounded-full uppercase tracking-widest">Initial Report</span>
                        </div>
                    </div>

                    @foreach($complaint->messages as $msg)
                        @if($msg->is_admin)
                            <!-- Admin Message (White) -->
                            <div class="p-8 rounded-[35px] shadow-sm relative border bg-white border-gray-200 ml-12">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-blue-500 text-white shadow-lg shadow-blue-100 flex items-center justify-center font-black text-sm">
                                            {{ strtoupper(substr($msg->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-gray-900 uppercase tracking-widest">{{ $msg->user->name }}</p>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase">Support Admin</p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">{{ $msg->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-gray-700 leading-relaxed text-lg font-medium">{{ $msg->message }}</p>
                                
                                @if($msg->images)
                                    <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($msg->images as $image)
                                            <img src="{{ Storage::url($image) }}" class="rounded-xl w-full h-32 object-cover cursor-zoom-in" onclick="window.open(this.src)">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @else
                            <!-- User Message (Green) -->
                            <div class="p-8 rounded-[35px] shadow-lg relative border bg-[#00a651] border-[#008d44] mr-12 text-white">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-2xl bg-white text-[#00a651] shadow-lg shadow-green-900/20 flex items-center justify-center font-black text-sm">
                                            {{ strtoupper(substr($msg->user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black text-white uppercase tracking-widest">{{ $msg->user->name }}</p>
                                            <p class="text-[9px] font-bold text-white/60 uppercase">Your Message</p>
                                        </div>
                                    </div>
                                    <span class="text-[10px] font-black text-white/40 uppercase tracking-widest">{{ $msg->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-white leading-relaxed text-lg font-medium">{{ $msg->message }}</p>

                                @if($msg->images)
                                    <div class="mt-4 grid grid-cols-2 md:grid-cols-3 gap-3">
                                        @foreach($msg->images as $image)
                                            <img src="{{ Storage::url($image) }}" class="rounded-xl w-full h-32 object-cover border-2 border-white/20 cursor-zoom-in" onclick="window.open(this.src)">
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>

            <!-- Evidence Section (Expanded for user) -->
            @if($complaint->image_path || $complaint->audio_path || $complaint->extra_images)
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-8">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-6">Evidence Attachments</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($complaint->audio_path)
                        <div class="col-span-full bg-[#fef9e1] p-6 rounded-3xl border border-[#f3bc3e]/20">
                            <p class="text-[10px] font-black text-[#f3bc3e] uppercase tracking-widest mb-4">Voice Evidence Record</p>
                            <audio controls class="w-full">
                                <source src="{{ Storage::url($complaint->audio_path) }}" type="audio/webm">
                                Your browser does not support the audio element.
                            </audio>
                        </div>
                    @endif

                    @if($complaint->image_path)
                        <div class="rounded-3xl overflow-hidden border border-gray-100 shadow-inner bg-gray-50 p-3">
                            @if(Str::endsWith($complaint->image_path, '.pdf'))
                                <a href="{{ Storage::url($complaint->image_path) }}" target="_blank" class="flex flex-col items-center justify-center gap-4 p-10 h-full bg-white rounded-2xl hover:shadow-xl transition-all group">
                                    <i class="fas fa-file-pdf text-red-500 text-5xl group-hover:scale-110 transition-transform"></i>
                                    <span class="text-sm font-black text-gray-800 uppercase tracking-widest">Open Evidence PDF</span>
                                </a>
                            @else
                                <img src="{{ Storage::url($complaint->image_path) }}" alt="Evidence" class="w-full h-72 object-cover rounded-2xl cursor-zoom-in hover:opacity-90 transition" onclick="window.open(this.src)">
                            @endif
                        </div>
                    @endif

                    @if($complaint->extra_images)
                        @foreach($complaint->extra_images as $extra_image)
                            <div class="rounded-3xl overflow-hidden border border-gray-100 shadow-inner bg-gray-50 p-3">
                                <img src="{{ Storage::url($extra_image) }}" alt="Extra Evidence" class="w-full h-72 object-cover rounded-2xl cursor-zoom-in hover:opacity-90 transition" onclick="window.open(this.src)">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
            @endif

            @if($complaint->admin_notes)
                <div class="bg-gray-900 rounded-[40px] shadow-2xl p-10 text-white relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-10 opacity-10">
                        <i class="fas fa-comment-dots text-8xl"></i>
                    </div>
                    <h3 class="text-xs font-black text-[#00a651] uppercase tracking-widest mb-4">Official Admin Response</h3>
                    <p class="text-2xl font-semibold leading-relaxed">{{ $complaint->admin_notes }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-8">
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-8">
                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-8">Submission Timeline</h3>
                
                <div class="space-y-10 relative">
                    <div class="absolute left-5 top-0 bottom-0 w-0.5 bg-gray-50"></div>
                    
                    <div class="relative flex gap-6">
                        <div class="w-10 h-10 rounded-full bg-green-50 border-4 border-white shadow-sm flex items-center justify-center text-[#00a651] z-10">
                            <i class="fas fa-check text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-800 uppercase tracking-widest">Submitted</p>
                            <p class="text-[10px] font-bold text-gray-400 mt-0.5">{{ $complaint->created_at->format('M d, Y | h:i A') }}</p>
                        </div>
                    </div>

                    @if($complaint->status !== 'pending')
                    <div class="relative flex gap-6">
                        <div class="w-10 h-10 rounded-full bg-blue-50 border-4 border-white shadow-sm flex items-center justify-center text-blue-500 z-10">
                            <i class="fas fa-spinner text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-800 uppercase tracking-widest">Under Review</p>
                            <p class="text-[10px] font-bold text-gray-400 mt-0.5">Administrative Action Taken</p>
                        </div>
                    </div>
                    @endif

                    @if($complaint->resolved_at)
                    <div class="relative flex gap-6">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 border-4 border-white shadow-sm flex items-center justify-center text-emerald-500 z-10">
                            <i class="fas fa-flag-checkered text-xs"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black text-gray-800 uppercase tracking-widest">Resolved</p>
                            <p class="text-[10px] font-bold text-gray-400 mt-0.5">{{ $complaint->resolved_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            @if($complaint->status === 'rejected' && !auth()->user()->is_blocked)
                <div class="bg-red-50 rounded-[40px] p-10 border border-red-100 shadow-lg shadow-red-50">
                    <h4 class="font-black text-red-800 text-xl mb-3">Re-submission available</h4>
                    <p class="text-sm text-red-600 mb-8 leading-relaxed">Your complaint was rejected. You can submit a new corrected report by clicking below.</p>
                    <a href="{{ route('user.complaints.create') }}" class="block w-full text-center py-5 bg-red-600 text-white rounded-[25px] font-black hover:bg-red-700 transition shadow-xl shadow-red-200 uppercase tracking-widest text-xs">
                        Create New Report
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
