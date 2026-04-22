@extends("layouts.app")

@section("content")
<div class="max-w-full mx-auto pb-20" x-data="{ newMessages: false }" x-init="
    window.Echo.private('complaint.{{ $complaint->id }}')
        .listen('MessageSent', (e) => {
            newMessages = true;
            // Optional: Auto-append message instead of just showing refresh button
            // This would require more complex DOM manipulation or using Alpine/Livewire better
        });
">
    <!-- Live Chat Notification -->
    <div x-show="newMessages" x-transition x-cloak class="fixed bottom-10 right-10 z-[100]">
        <button @click="window.location.reload()" class="bg-[#00a651] text-white px-8 py-4 rounded-2xl shadow-2xl flex items-center gap-4 hover:scale-105 transition-all">
            <i class="fas fa-comment-dots animate-bounce"></i>
            <span class="text-xs font-black uppercase tracking-widest">New message received. Refresh to view.</span>
        </button>
    </div>
    <!-- Header Section -->
    <div class="mb-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('admin.complaints') }}" class="text-gray-400 hover:text-[#00a651] transition-all transform hover:-translate-x-1">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Complaint Detail Header Card (Category/Status Meta) -->
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-10 relative overflow-hidden">
                <div class="absolute top-0 left-0 right-0 h-2 bg-[#00a651]"></div>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-8 bg-gray-50/50 p-8 rounded-[32px] border border-gray-50">
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Category</p>
                        <p class="font-bold text-gray-800 text-lg">{{ $complaint->category }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Submitted</p>
                        <p class="font-bold text-gray-800 text-sm">{{ $complaint->submitted_at->format('M d, Y') }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Priority</p>
                        <p class="font-bold text-gray-800 text-sm uppercase">{{ $complaint->priority ?? 'Medium' }}</p>
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</p>
                        <p class="font-bold text-gray-800 text-sm uppercase tracking-tighter">{{ ucfirst(str_replace('_', ' ', $complaint->status)) }}</p>
                    </div>
                </div>
            </div>

            <!-- Complaint Conversation History Box (Expanded) -->
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-10 border-b border-gray-50 flex items-center justify-between bg-white">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-[#00a651]"></span>
                        Official Conversation Thread
                    </h3>
                </div>
                <div id="chat-messages" class="bg-gray-50/30 p-10 h-[800px] overflow-y-auto space-y-8 shadow-inner">
                    <!-- Initial Complaint Message (User - Green) -->
                    <div class="bg-[#00a651] p-10 rounded-[32px] shadow-sm relative overflow-hidden max-w-full text-white">
                        <div class="absolute top-0 left-0 w-2 h-full bg-white/20"></div>
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-2xl bg-white text-[#00a651] flex items-center justify-center font-black">
                                {{ strtoupper(substr($complaint->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-black text-white uppercase tracking-widest">{{ $complaint->user->name }}</p>
                                <p class="text-[10px] font-bold text-white/60 uppercase">Original Submission</p>
                            </div>
                        </div>
                        <h4 class="text-2xl font-black text-white mb-4">{{ $complaint->title }}</h4>
                        <p class="text-white leading-relaxed text-xl mb-8">{{ $complaint->description }}</p>

                        {{-- Evidence attached to original complaint --}}
                        @if($complaint->image_path || $complaint->audio_path)
                            <div class="space-y-6 pt-8 border-t border-white/10">
                                @if($complaint->audio_path)
                                    <div class="bg-white/10 p-6 rounded-2xl border border-white/10">
                                        <p class="text-[10px] font-black text-white/40 uppercase tracking-widest mb-3">Original Voice Record</p>
                                        <audio controls class="w-full h-10 filter invert brightness-200">
                                            <source src="{{ Storage::url($complaint->audio_path) }}" type="audio/webm">
                                        </audio>
                                    </div>
                                @endif

                                @if($complaint->image_path)
                                    <div class="rounded-2xl overflow-hidden border border-white/10 group/img relative">
                                        @if(Str::endsWith($complaint->image_path, '.pdf'))
                                            <a href="{{ Storage::url($complaint->image_path) }}" target="_blank" class="flex items-center gap-4 p-6 bg-white/5 hover:bg-white/10 transition-all">
                                                <i class="fas fa-file-pdf text-2xl"></i>
                                                <span class="text-sm font-black uppercase tracking-widest">View Evidence PDF</span>
                                            </a>
                                        @else
                                            <img src="{{ Storage::url($complaint->image_path) }}" class="w-full h-auto max-h-96 object-cover cursor-zoom-in" onclick="window.open(this.src)">
                                        @endif
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mt-8 pt-6 border-t border-white/10 flex items-center justify-between">
                            <span class="text-xs text-white/40 font-bold uppercase tracking-widest">{{ $complaint->created_at->format('F d, Y | h:i A') }}</span>
                            <span class="px-4 py-1.5 bg-white/10 text-white text-[10px] font-black rounded-full uppercase tracking-widest">Case Opened</span>
                        </div>
                    </div>
                    
                    @foreach($complaint->messages as $msg)
                        <div class="p-10 rounded-[40px] shadow-sm relative border {{ $msg->is_admin ? 'bg-white border-gray-200' : 'bg-[#00a651] text-white border-[#008d44]' }}">
                            <div class="flex items-center justify-between mb-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-black text-lg {{ $msg->is_admin ? 'bg-blue-500 text-white shadow-lg shadow-blue-200' : 'bg-white text-[#00a651] shadow-lg shadow-green-900/20' }}">
                                        {{ strtoupper(substr($msg->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black uppercase tracking-widest {{ $msg->is_admin ? 'text-gray-900' : 'text-white' }}">{{ $msg->user->name }}</p>
                                        <p class="text-[10px] font-bold uppercase tracking-widest {{ $msg->is_admin ? 'text-gray-400' : 'text-white/60' }}">{{ $msg->is_admin ? 'Official Administrator' : 'Complaint Submitter' }}</p>
                                    </div>
                                </div>
                                <span class="text-xs font-black uppercase tracking-widest {{ $msg->is_admin ? 'text-gray-400' : 'text-white/40' }}">{{ $msg->created_at->format('h:i A') }}</span>
                            </div>
                            <p class="leading-relaxed text-xl font-medium {{ $msg->is_admin ? 'text-gray-700' : 'text-white' }}">{{ $msg->message }}</p>
                            
                            @if($msg->images)
                                <div class="mt-6 grid grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach($msg->images as $image)
                                        <img src="{{ Storage::url($image) }}" class="rounded-2xl w-full h-40 object-cover cursor-zoom-in" onclick="window.open(this.src)">
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-6 flex justify-end">
                                <span class="text-[10px] font-black uppercase tracking-widest {{ $msg->is_admin ? 'text-gray-300' : 'text-white/30' }}">{{ $msg->created_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Admin Message Form -->
                <div class="p-10 bg-white border-t border-gray-100">
                    <form method="POST" action="{{ route('complaints.messages.store', $complaint) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="flex gap-4 items-center">
                            <div class="flex-1 relative">
                                <input type="text" name="message" 
                                       placeholder="Type your official response here..." 
                                       class="w-full px-8 py-5 pr-16 bg-gray-50 border border-gray-100 rounded-[25px] focus:ring-4 focus:ring-[#00a651]/10 focus:bg-white focus:border-[#00a651] transition-all font-semibold text-gray-700 shadow-inner"
                                       required>
                                <label class="absolute right-6 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-[#00a651] transition-colors">
                                    <i class="fas fa-image text-xl"></i>
                                    <input type="file" name="images[]" multiple class="hidden" accept="image/*" onchange="updateImagePreview(this)">
                                </label>
                            </div>
                            <button type="submit" 
                                    class="px-10 py-5 bg-[#00a651] text-white rounded-[25px] font-black shadow-lg shadow-green-200 hover:bg-[#008d44] hover:-translate-y-1 transition-all active:scale-95 flex items-center gap-3 uppercase tracking-widest text-xs">
                                <i class="fas fa-paper-plane"></i>
                                Send Message
                            </button>
                        </div>
                        <div id="image-preview-container" class="mt-4 flex flex-wrap gap-2"></div>
                    </form>
                </div>
            </div>
        </div>
        
        <!-- Sidebar: User & History -->
        <div class="space-y-8">
            <!-- User Intelligence Card -->
            <div class="bg-gray-900 rounded-[40px] shadow-xl p-8 text-white relative overflow-hidden group">
                <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-white/5 rounded-full transition-transform group-hover:scale-150 duration-700"></div>
                
                <h3 class="text-xs font-black text-white/40 uppercase tracking-widest mb-8 flex items-center gap-2">
                    <i class="fas fa-user-shield text-[#00a651]"></i>
                    Submitter Identity
                </h3>
                
                <div class="flex items-center gap-5 mb-8">
                    <div class="w-16 h-16 rounded-2xl bg-[#00a651] flex items-center justify-center text-3xl font-black shadow-lg shadow-green-900/50">
                        {{ strtoupper(substr($complaint->user->name, 0, 1)) }}
                    </div>
                    <div>
                        <h4 class="font-black text-xl leading-tight">{{ $complaint->user->name }}</h4>
                        <p class="text-white/40 text-xs font-bold uppercase tracking-widest mt-1">{{ $complaint->user->id_number }}</p>
                    </div>
                </div>

                <div class="space-y-4 mb-8">
                    <div class="flex justify-between items-center py-3 border-b border-white/5">
                        <span class="text-xs font-bold text-white/40 uppercase">Academic Path</span>
                        <span class="font-bold text-sm">{{ $complaint->user->course ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center py-3 border-b border-white/5">
                        <span class="text-xs font-bold text-white/40 uppercase">Account Status</span>
                        @if($complaint->user->is_blocked)
                            <span class="bg-red-500/10 text-red-500 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-red-500/20">BLOCKED</span>
                        @elseif($complaint->user->banned_until && $complaint->user->banned_until->isFuture())
                            <span class="bg-orange-500/10 text-orange-500 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-orange-500/20">BANNED (24H)</span>
                        @else
                            <span class="bg-emerald-500/10 text-emerald-500 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border border-emerald-500/20">ACTIVE</span>
                        @endif
                    </div>
                    @if($complaint->user->banned_until && $complaint->user->banned_until->isFuture())
                    <div class="flex justify-between items-center py-3">
                        <span class="text-xs font-bold text-white/40 uppercase">Banned Until</span>
                        <span class="font-bold text-xs text-orange-400">{{ $complaint->user->banned_until->format('M d, h:i A') }}</span>
                    </div>
                    @endif
                </div>
                
                <div class="space-y-3">
                    @if(!$complaint->user->is_blocked && !($complaint->user->banned_until && $complaint->user->banned_until->isFuture()))
                    <form method="POST" action="{{ route('admin.users.quick_ban', $complaint->user) }}" onsubmit="return confirm('Are you sure do you want to ban this person for 24 hours?')">
                        @csrf
                        <button type="submit" class="w-full px-6 py-4 bg-red-600/20 text-red-500 border border-red-500/30 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-600 hover:text-white transition-all">
                            <i class="fas fa-bolt mr-2"></i>Quick Ban (24H)
                        </button>
                    </form>
                    @endif

                    @if(!$complaint->user->is_blocked && $rejectionCount >= 2)
                    <form method="POST" action="{{ route('admin.users.block', $complaint->user) }}">
                        @csrf
                        <button type="submit" class="w-full px-6 py-4 bg-red-600 text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-red-700 transition-all shadow-lg shadow-red-900/20">
                            Restrict Access (Permanent)
                        </button>
                    </form>
                    @endif
                </div>
            </div>
            
            <!-- Historical Context -->
            <div class="bg-white rounded-[40px] shadow-sm border border-gray-100 p-8">
                <div class="flex items-center justify-between mb-8">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">User History</h3>
                    <span class="bg-gray-100 text-gray-500 px-2 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-widest">{{ $userComplaints->count() }} TOTAL</span>
                </div>

                <div class="space-y-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($userComplaints as $userComplaint)
                    <div class="p-5 rounded-3xl border border-gray-50 hover:bg-gray-50 transition cursor-pointer group"
                         onclick="window.location='{{ route('admin.complaints.show', $userComplaint) }}'">
                        <div class="flex justify-between items-center mb-3">
                            <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest group-hover:text-gray-500">{{ $userComplaint->complaint_number }}</span>
                            <span class="text-[9px] font-black uppercase tracking-tighter px-2 py-0.5 rounded-md
                                @if($userComplaint->status === 'resolved') bg-emerald-50 text-emerald-600 @elseif($userComplaint->status === 'rejected') bg-red-50 text-red-600 @else bg-gray-100 text-gray-500 @endif">
                                {{ ucfirst($userComplaint->status) }}
                            </span>
                        </div>
                        <p class="text-sm font-bold text-gray-700 leading-snug group-hover:text-[#00a651] transition">{{ Str::limit($userComplaint->title, 50) }}</p>
                        <p class="text-[10px] font-bold text-gray-400 mt-2 uppercase tracking-tighter">{{ $userComplaint->submitted_at->format('M d, Y') }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Moderation Quick Action Card -->
            @if($complaint->status === 'pending' || $complaint->status === 'in_progress')
            <div class="bg-gray-900 rounded-[40px] shadow-xl p-8 text-white relative overflow-hidden group">
                <div class="absolute -top-10 -left-10 w-40 h-40 bg-white/5 rounded-full transition-transform group-hover:scale-150 duration-700"></div>
                
                <h3 class="text-xs font-black text-white/40 uppercase tracking-widest mb-8 flex items-center gap-2">
                    <i class="fas fa-gavel text-[#00a651]"></i>
                    Case Decision
                </h3>

                @if($complaint->status === 'pending')
                <form method="POST" action="{{ route('admin.complaints.update', $complaint) }}" class="space-y-6 relative z-10">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-3 ml-1">Admin Notes</label>
                        <textarea name="admin_notes" rows="3" 
                                  placeholder="Enter resolution notes..."
                                  class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:ring-2 focus:ring-[#00a651] focus:bg-white/10 transition-all font-semibold text-white placeholder-white/20 text-sm">{{ $complaint->admin_notes }}</textarea>
                    </div>

                    <div class="space-y-3">
                        <button type="submit" name="action" value="accept" 
                                class="w-full bg-[#00a651] text-white px-6 py-4 rounded-2xl font-black shadow-lg hover:bg-[#008d44] hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-widest text-[10px]">
                            <i class="fas fa-check-double"></i>
                            Accept & Process
                        </button>
                        <button type="submit" name="action" value="reject" 
                                class="w-full bg-white/5 text-red-400 border border-red-400/30 px-6 py-4 rounded-2xl font-black hover:bg-red-400 hover:text-white transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-widest text-[10px]">
                            <i class="fas fa-ban"></i>
                            Reject Submission
                        </button>
                    </div>
                </form>
                @elseif($complaint->status === 'in_progress')
                <form method="POST" action="{{ route('admin.complaints.resolve', $complaint) }}" class="space-y-6 relative z-10">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-3 ml-1">Final Resolution Details</label>
                        <textarea name="admin_notes" rows="3" 
                                  placeholder="Enter final notes..."
                                  class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl focus:ring-2 focus:ring-[#00a651] focus:bg-white/10 transition-all font-semibold text-white placeholder-white/20 text-sm">{{ $complaint->admin_notes }}</textarea>
                    </div>
                    <button type="submit" 
                            class="w-full bg-[#00a651] text-white px-6 py-4 rounded-2xl font-black shadow-lg hover:bg-[#008d44] hover:-translate-y-1 transition-all active:scale-95 flex items-center justify-center gap-3 uppercase tracking-widest text-[10px]">
                        <i class="fas fa-flag-checkered"></i>
                        Finalize & Resolve
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    function updateImagePreview(input) {
        const container = document.getElementById('image-preview-container');
        container.innerHTML = '';
        if (input.files) {
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'w-16 h-16 rounded-xl object-cover border-2 border-gray-100 shadow-sm';
                    container.appendChild(img);
                }
                reader.readAsDataURL(file);
            });
        }
    }
</script>

<style>
    @keyframes spin-slow {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .fa-spin-slow {
        animation: spin-slow 3s linear infinite;
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #f1f1f1;
        border-radius: 10px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #e5e5e5;
    }
</style>
@endsection
