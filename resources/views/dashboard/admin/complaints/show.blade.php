@extends("layouts.app")

@section("content")
<div class="h-[calc(100vh-140px)] lg:h-[calc(100vh-160px)] flex flex-col lg:flex-row gap-6" x-data="adminComplaintChat()">
    
    <!-- Main Chat Section -->
    <div class="flex-1 flex flex-col bg-white lg:rounded-[3rem] shadow-sm border border-gray-100 overflow-hidden relative">
        
        <!-- Chat Header -->
        <div class="bg-[#163a24] text-white p-4 lg:p-6 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.complaints') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h2 class="text-sm lg:text-base font-black uppercase tracking-widest leading-none">{{ Str::limit($complaint->title, 30) }}</h2>
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-[9px] font-bold text-white/40 uppercase tracking-tighter">{{ $complaint->complaint_number }}</span>
                        <span class="w-1 h-1 rounded-full bg-white/20"></span>
                        <span class="text-[9px] font-bold text-white/60 uppercase tracking-tighter">{{ $complaint->category }}</span>
                        <span class="w-1 h-1 rounded-full bg-white/20"></span>
                        <span class="text-[9px] font-black uppercase {{ $complaint->status === 'resolved' ? 'text-emerald-400' : ($complaint->status === 'rejected' ? 'text-red-400' : 'text-accent') }}">
                            {{ str_replace('_', ' ', $complaint->status) }}
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <button @click="showModeration = true" class="w-10 h-10 flex items-center justify-center rounded-xl bg-accent text-primary shadow-lg shadow-accent/20 lg:hidden">
                    <i class="fas fa-gavel"></i>
                </button>
                <button @click="showDetails = true" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>

        <!-- Messages Area -->
        <div id="chat-container" class="flex-1 overflow-y-auto p-4 lg:p-8 space-y-6 custom-scrollbar bg-gray-50/30">
            
            <div class="flex justify-center mb-4">
                <div class="bg-[#163a24]/5 px-6 py-2 rounded-full border border-[#163a24]/10">
                    <p class="text-[10px] font-black text-[#163a24]/40 uppercase tracking-widest text-center">Case opened by {{ $complaint->user->name }}</p>
                </div>
            </div>

            <!-- Initial Submission -->
            <div class="flex justify-start max-w-[85%] lg:max-w-[70%]">
                <div class="bg-white text-gray-800 p-5 lg:p-7 rounded-[2rem] rounded-tl-none shadow-sm border border-gray-200 relative">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-2 h-2 rounded-full bg-accent"></div>
                        <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Student Submission</span>
                    </div>
                    <h4 class="font-black text-sm lg:text-base uppercase mb-2 tracking-tight text-primary">{{ $complaint->title }}</h4>
                    <p class="text-sm lg:text-base leading-relaxed font-semibold text-gray-700">{{ $complaint->description }}</p>
                    
                    @if($complaint->image_path || $complaint->audio_paths || $complaint->extra_images)
                    <div class="mt-5 pt-5 border-t border-gray-50 space-y-4">
                        @if($complaint->audio_paths)
                            @foreach($complaint->audio_paths as $index => $audioPath)
                            <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black uppercase tracking-widest mb-3 text-gray-400">Voice Evidence {{ count($complaint->audio_paths) > 1 ? ($index + 1) : '' }}</p>
                                <audio controls src="{{ asset('storage/' . $audioPath) }}" class="w-full h-10 custom-audio-player"></audio>
                            </div>
                            @endforeach
                        @endif
                        
                        <div class="grid grid-cols-2 gap-3">
                            @if($complaint->image_path)
                            <img src="{{ asset('storage/' . $complaint->image_path) }}" class="rounded-2xl w-full h-32 lg:h-48 object-cover border border-gray-100 cursor-zoom-in hover:opacity-90 transition" @click="window.open($el.src)">
                            @endif
                            @if($complaint->extra_images)
                                @foreach($complaint->extra_images as $extra)
                                <img src="{{ asset('storage/' . $extra) }}" class="rounded-2xl w-full h-32 lg:h-48 object-cover border border-gray-100 cursor-zoom-in hover:opacity-90 transition" @click="window.open($el.src)">
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endif
                    
                    <p class="text-[9px] font-black text-gray-300 text-right mt-4 uppercase">{{ $complaint->created_at->format('h:i A | M d') }}</p>
                </div>
            </div>

            @foreach($complaint->messages as $msg)
                @if($msg->is_admin)
                    <div class="flex justify-end ml-auto max-w-[85%] lg:max-w-[70%]">
                        <div class="bg-primary text-white p-5 lg:p-7 rounded-[2rem] rounded-tr-none shadow-lg shadow-green-900/10">
                            <div class="flex items-center gap-2 mb-2 opacity-60">
                                <span class="text-[10px] font-black uppercase tracking-widest">You • Admin Response</span>
                            </div>
                            <p class="text-sm lg:text-base leading-relaxed font-medium">{{ $msg->message }}</p>
                            
                            @if($msg->audio_paths)
                            @foreach($msg->audio_paths as $index => $audioPath)
                            <div class="mt-3 bg-white/10 p-3 rounded-2xl border border-white/5">
                                <p class="text-[10px] font-black uppercase tracking-widest mb-2 opacity-60">Voice Message {{ count($msg->audio_paths) > 1 ? ($index + 1) : '' }}</p>
                                <audio controls src="{{ asset('storage/' . $audioPath) }}" class="w-full h-10 custom-audio-player"></audio>
                            </div>
                            @endforeach
                            @endif

                            @if($msg->images)
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                @foreach($msg->images as $image)
                                <img src="{{ asset('storage/' . $image) }}" class="rounded-2xl w-full h-24 lg:h-32 object-cover cursor-zoom-in border border-white/10" @click="window.open($el.src)">
                                @endforeach
                            </div>
                            @endif
                            <p class="text-[9px] font-black text-white/40 text-right mt-4 uppercase">{{ $msg->created_at->format('h:i A') }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex justify-start max-w-[85%] lg:max-w-[70%]">
                        <div class="bg-white text-gray-800 p-5 lg:p-7 rounded-[2rem] rounded-tl-none shadow-sm border border-gray-100">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-2 h-2 rounded-full bg-accent"></div>
                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">{{ $msg->user->name }}</span>
                            </div>
                            <p class="text-sm lg:text-base leading-relaxed font-semibold">{{ $msg->message }}</p>
                            
                            @if($msg->audio_paths)
                            @foreach($msg->audio_paths as $index => $audioPath)
                            <div class="mt-3 bg-gray-50 p-3 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black uppercase tracking-widest mb-2 text-gray-400">Voice Message {{ count($msg->audio_paths) > 1 ? ($index + 1) : '' }}</p>
                                <audio controls src="{{ asset('storage/' . $audioPath) }}" class="w-full h-10 custom-audio-player"></audio>
                            </div>
                            @endforeach
                            @endif

                            @if($msg->images)
                            <div class="mt-4 grid grid-cols-2 gap-3">
                                @foreach($msg->images as $image)
                                <img src="{{ asset('storage/' . $image) }}" class="rounded-2xl w-full h-24 lg:h-32 object-cover cursor-zoom-in border border-gray-100" @click="window.open($el.src)">
                                @endforeach
                            </div>
                            @endif
                            <p class="text-[9px] font-black text-gray-300 text-right mt-4 uppercase">{{ $msg->created_at->format('h:i A') }}</p>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Input Bar -->
        <div class="p-4 lg:p-6 bg-white border-t border-gray-100 shrink-0">
            <!-- Media Preview Area (Pre-sent) -->
            <div x-show="audioFiles.length > 0" x-cloak class="max-w-4xl mx-auto mb-4 p-4 bg-primary/5 rounded-2xl flex flex-wrap gap-3">
                <!-- Audio Previews -->
                <template x-for="(audio, index) in audioPreviews" :key="index">
                    <div class="bg-primary text-white p-3 rounded-xl flex items-center gap-3 w-full sm:w-auto">
                        <i class="fas fa-microphone-alt"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest" x-text="'Voice Record ' + (audioPreviews.length > 1 ? (index + 1) : '')"></span>
                        <button type="button" @click="playPreview(index)" class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center hover:bg-white/30 transition">
                            <i class="fas" :class="activePlayer === audio.audio ? 'fa-pause' : 'fa-play'" style="font-size: 8px;"></i>
                        </button>
                        <button type="button" @click="removeAudio(index)" class="ml-2 text-white/60 hover:text-white">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </div>
                </template>
            </div>

            <form @submit.prevent="sendMessage()" enctype="multipart/form-data" class="max-w-4xl mx-auto flex items-center gap-3 lg:gap-4">
                @csrf
                <button type="button" 
                        @click="toggleRecording()"
                        :class="isRecording ? 'bg-red-500 text-white animate-pulse' : 'bg-primary/5 text-primary hover:bg-primary/10'"
                        class="w-12 h-12 lg:w-14 lg:h-14 flex items-center justify-center rounded-2xl transition shrink-0 relative">
                    <i class="fas fa-microphone text-lg"></i>
                    <template x-if="audioFiles.length > 0">
                        <span class="absolute -top-1 -right-1 w-6 h-6 bg-accent text-primary text-[10px] font-black rounded-lg flex items-center justify-center border-4 border-white" x-text="audioFiles.length"></span>
                    </template>
                </button>

                <button type="button" @click="$refs.imageInput.click()" class="w-12 h-12 lg:w-14 lg:h-14 flex items-center justify-center rounded-2xl bg-primary/5 text-primary hover:bg-primary/10 transition shrink-0 relative">
                    <i class="fas fa-paperclip text-lg"></i>
                    <template x-if="imageCount > 0">
                        <span class="absolute -top-1 -right-1 w-6 h-6 bg-accent text-primary text-[10px] font-black rounded-lg flex items-center justify-center border-4 border-white" x-text="imageCount"></span>
                    </template>
                </button>
                <input type="file" x-ref="imageInput" name="images[]" multiple class="hidden" accept="image/*" @change="imageCount = $el.files.length">
                <input type="file" x-ref="audioInput" name="audio" class="hidden" accept="audio/*">
                
                <div class="flex-1 relative">
                    <input type="text" x-model="newMessage" :required="audioFiles.length === 0 && imageCount === 0" placeholder="Type your response..." class="w-full px-6 py-4 lg:py-5 bg-gray-50 border-none rounded-[1.5rem] lg:rounded-[2rem] font-bold text-sm lg:text-base text-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all placeholder-gray-400">
                </div>
                
                <button type="submit" :disabled="sending || (!newMessage && audioFiles.length === 0 && imageCount === 0)" class="w-12 h-12 lg:w-14 lg:h-14 flex items-center justify-center rounded-2xl bg-[#00a651] text-white shadow-lg shadow-green-900/10 hover:bg-[#008d44] transition-all active:scale-95 shrink-0 disabled:opacity-50">
                    <i class="fas" :class="sending ? 'fa-spinner fa-spin' : 'fa-paper-plane text-base'"></i>
                </button>
            </form>
        </div>
    </div>

    <!-- Desktop Sidebar Section -->
    <div class="hidden lg:flex flex-col w-96 gap-6 overflow-y-auto shrink-0 custom-scrollbar">
        <!-- Submitter Identity -->
        <div class="bg-white rounded-[3rem] p-8 shadow-sm border border-gray-100">
            <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-6">Submitter Identity</h3>
            <div class="flex items-center gap-4 mb-6">
                <div class="w-16 h-16 rounded-2xl bg-primary text-white flex items-center justify-center text-2xl font-black shadow-xl">
                    {{ strtoupper(substr($complaint->user->name, 0, 1)) }}
                </div>
                <div>
                    <h4 class="font-black text-lg text-primary leading-tight">{{ $complaint->user->name }}</h4>
                    <p class="text-gray-400 text-[9px] font-black uppercase tracking-widest mt-1">{{ $complaint->user->id_number }}</p>
                    <span class="text-accent text-[9px] font-black uppercase tracking-widest">{{ $complaint->user->course ?? 'General' }}</span>
                </div>
            </div>
            
            <div class="space-y-3">
                @if(!$complaint->user->is_blocked)
                <form method="POST" action="{{ route('admin.users.block', $complaint->user) }}" onsubmit="return confirm('Restrict access for this user?')">
                    @csrf
                    <button class="w-full py-4 bg-red-50 text-red-500 text-[10px] font-black uppercase tracking-[0.15em] rounded-2xl hover:bg-red-500 hover:text-white transition-all">Restrict Submitter</button>
                </form>
                @else
                    <div class="py-4 bg-red-600 text-white text-center rounded-2xl font-black text-[10px] uppercase tracking-widest">User Restricted</div>
                @endif
            </div>
        </div>

        <!-- Moderation Logic -->
        <div class="bg-[#163a24] rounded-[3.5rem] p-8 shadow-2xl text-white relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-[10px] font-black text-white/40 uppercase tracking-[0.2em] mb-6">Case Decision</h3>
                
                @if($complaint->status === 'pending' || $complaint->status === 'in_progress')
                    @php $route = $complaint->status === 'pending' ? route('admin.complaints.update', $complaint) : route('admin.complaints.resolve', $complaint); @endphp
                    <form method="POST" action="{{ $route }}" class="space-y-6">
                        @csrf
                        <div>
                            <label class="block text-[9px] font-black text-white/30 uppercase tracking-widest mb-3 ml-1">Resolution Notes</label>
                            <textarea name="admin_notes" rows="4" 
                                      placeholder="Enter resolution details..."
                                      class="w-full px-6 py-5 bg-white/5 border border-white/10 rounded-3xl font-bold text-white outline-none focus:ring-4 focus:ring-accent/20 transition placeholder-white/10 text-sm">{{ $complaint->admin_notes }}</textarea>
                        </div>

                        <div class="space-y-3">
                            @if($complaint->status === 'pending')
                                <button type="submit" name="action" value="accept" 
                                        class="w-full bg-accent text-primary py-4 rounded-2xl font-black uppercase tracking-widest shadow-xl hover:bg-yellow-300 transition-all flex items-center justify-center gap-3 text-xs">
                                    <i class="fas fa-check-double"></i> Accept Case
                                </button>
                                <button type="submit" name="action" value="reject" 
                                        class="w-full bg-white/5 text-red-400 border border-red-400/30 py-4 rounded-2xl font-black hover:bg-red-500 hover:text-white transition-all flex items-center justify-center gap-3 text-xs">
                                    <i class="fas fa-ban"></i> Reject Submission
                                </button>
                            @else
                                <button type="submit" 
                                        class="w-full bg-accent text-primary py-5 rounded-2xl font-black uppercase tracking-widest shadow-xl hover:bg-yellow-300 transition-all flex items-center justify-center gap-3 text-sm">
                                    <i class="fas fa-flag-checkered"></i> Resolve Case
                                </button>
                            @endif
                        </div>
                    </form>
                @else
                    <div class="py-10 text-center opacity-40">
                        <i class="fas fa-lock text-4xl mb-4 text-accent"></i>
                        <p class="text-xs font-black uppercase tracking-widest">Case Finalized</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Details Overlay (Mobile Bottom Sheet / Desktop Centered Modal) -->
    @php
        $allImages = [];
        if($complaint->image_path) $allImages[] = $complaint->image_path;
        if($complaint->extra_images) $allImages = array_merge($allImages, $complaint->extra_images);
        foreach($complaint->messages as $msg) {
            if($msg->images) $allImages = array_merge($allImages, $msg->images);
        }

        $allAudio = [];
        if($complaint->audio_paths) {
            foreach($complaint->audio_paths as $index => $path) {
                $allAudio[] = [
                    'path' => $path, 
                    'label' => 'Student Submission' . (count($complaint->audio_paths) > 1 ? ' (' . ($index + 1) . ')' : ''),
                    'type' => 'initial',
                    'id' => 'initial_' . $index
                ];
            }
        }
        foreach($complaint->messages as $msg) {
            if($msg->audio_paths) {
                foreach($msg->audio_paths as $index => $path) {
                    $allAudio[] = [
                        'path' => $path, 
                        'label' => ($msg->is_admin ? 'Your Response' : 'Student Reply') . (count($msg->audio_paths) > 1 ? ' (' . ($index + 1) . ')' : ''),
                        'type' => 'message',
                        'id' => $msg->id . '_' . $index
                    ];
                }
            }
        }
    @endphp
    <div x-show="showDetails" x-cloak class="fixed inset-0 z-[100] bg-primary/60 backdrop-blur-sm flex items-end justify-center lg:items-center p-0 lg:p-4">
        <div @click.away="showDetails = false" class="bg-white w-full lg:max-w-2xl rounded-t-[3rem] lg:rounded-[3rem] p-8 space-y-6 max-h-[85vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-xl font-black text-primary uppercase tracking-tight">Case Details</h3>
                <button @click="showDetails = false" class="text-gray-400"><i class="fas fa-times"></i></button>
            </div>

            <!-- Details Section -->
            <div class="space-y-6 animate-in fade-in slide-in-from-bottom-2 duration-300">
                <div class="flex items-center gap-5 p-6 bg-primary rounded-[2.5rem] text-white">
                    <div class="w-16 h-16 rounded-2xl bg-white text-primary flex items-center justify-center text-2xl font-black shadow-xl">{{ strtoupper(substr($complaint->user->name, 0, 1)) }}</div>
                    <div>
                        <h4 class="font-black text-xl leading-none">{{ $complaint->user->name }}</h4>
                        <p class="text-white/40 text-[10px] font-bold mt-2 uppercase">{{ $complaint->user->id_number }}</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-3">
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Status</p>
                        <p class="text-xs font-black uppercase text-primary">{{ str_replace('_', ' ', $complaint->status) }}</p>
                    </div>
                    <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Category</p>
                        <p class="text-xs font-black uppercase text-primary">{{ $complaint->category }}</p>
                    </div>
                </div>

                <!-- Media Library Section (Below Case Details) -->
                <div class="pt-6 border-t border-gray-100 space-y-6">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 ml-1">Media Gallery</h4>
                        
                        <!-- Audio Section -->
                        @if(count($allAudio) > 0)
                            <div class="space-y-3 mb-6">
                                @foreach($allAudio as $audio)
                                    <div class="bg-gray-50 p-4 rounded-2xl flex items-center gap-4 border border-gray-100">
                                        <i class="fas fa-microphone text-primary"></i>
                                        <div>
                                            <p class="text-[10px] font-black uppercase tracking-widest text-primary">{{ $audio['label'] }}</p>
                                        </div>
                                        <audio src="{{ asset('storage/' . $audio['path']) }}" 
                                               x-ref="{{ $audio['type'] === 'initial' ? 'audioPlayer_initial' : 'audioPlayer_lib_' . $audio['id'] }}" 
                                               class="hidden"></audio>
                                        <button @click="playAudio($refs['{{ $audio['type'] === 'initial' ? 'audioPlayer_initial' : 'audioPlayer_lib_' . $audio['id'] }}'])" 
                                                class="ml-auto w-8 h-8 rounded-full bg-white shadow-sm flex items-center justify-center text-primary">
                                            <i class="fas" :class="activePlayer === $refs['{{ $audio['type'] === 'initial' ? 'audioPlayer_initial' : 'audioPlayer_lib_' . $audio['id'] }}'] ? 'fa-pause' : 'fa-play'" class="text-[10px]"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        <!-- Images Section -->
                        @if(count($allImages) > 0)
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($allImages as $img)
                                    <img src="{{ asset('storage/' . $img) }}" 
                                         class="aspect-square w-full object-cover rounded-xl border border-gray-100"
                                         @click="window.open($el.src)">
                                @endforeach
                            </div>
                        @endif

                        @if(count($allAudio) === 0 && count($allImages) === 0)
                            <p class="text-[10px] font-bold text-gray-400 uppercase text-center py-4 italic">No attachments</p>
                        @endif
                    </div>
                </div>
            </div>

            <button @click="showDetails = false" class="w-full bg-primary text-white py-5 rounded-2xl font-black uppercase tracking-widest">Back to Case</button>
        </div>
    </div>

    <div x-show="showModeration" x-cloak class="fixed inset-0 z-[100] bg-primary/60 backdrop-blur-sm flex items-end justify-center lg:hidden">
        <div @click.away="showModeration = false" class="bg-white w-full rounded-t-[3rem] p-8 space-y-6">
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-xl font-black text-primary uppercase tracking-tight">Case Decision</h3>
                <button @click="showModeration = false" class="text-gray-400"><i class="fas fa-times"></i></button>
            </div>
             <!-- ... existing moderation form ... -->
             <button @click="showModeration = false" class="w-full bg-gray-100 text-gray-400 py-5 rounded-2xl font-black uppercase tracking-widest">Close Panel</button>
        </div>
    </div>
</div>

<script>
function adminComplaintChat() {
    return {
        showDetails: false,
        activeTab: 'details',
        showModeration: false,
        imageCount: 0,
        activePlayer: null,
        
        // Recording state
        isRecording: false,
        mediaRecorder: null,
        audioChunks: [],
        audioFiles: [],
        audioPreviews: [],
        sending: false,
        newMessage: '',
        activePlayer: null,

        init() {
            this.scrollToBottom();
            window.Echo.private('complaint.{{ $complaint->id }}').listen('MessageSent', (e) => { 
                if (!this.sending) {
                    window.location.reload(); 
                }
            });
        },
        scrollToBottom() {
            setTimeout(() => {
                const container = document.getElementById('chat-container');
                if (container) container.scrollTop = container.scrollHeight;
            }, 100);
        },
        async toggleRecording() {
            if (this.isRecording) {
                this.stopRecording();
            } else {
                await this.startRecording();
            }
        },
        async startRecording() {
            if (this.isRecording) return;
            try {
                const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                this.mediaRecorder = new MediaRecorder(stream);
                this.audioChunks = [];

                this.mediaRecorder.ondataavailable = (event) => {
                    if (event.data.size > 0) {
                        this.audioChunks.push(event.data);
                    }
                };

                this.mediaRecorder.onstop = () => {
                    if (this.audioChunks.length === 0) return;

                    const audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                    const audioUrl = URL.createObjectURL(audioBlob);
                    const file = new File([audioBlob], `recording_${Date.now()}.webm`, { type: 'audio/webm' });

                    const audioObj = new Audio(audioUrl);
                    audioObj.onended = () => { if(this.activePlayer === audioObj) this.activePlayer = null; };
                    this.audioFiles.push(file);
                    this.audioPreviews.push({ url: audioUrl, audio: audioObj });
                };

                this.mediaRecorder.start();
                this.isRecording = true;
            } catch (err) {
                console.error("Error accessing microphone:", err);
                alert("Could not access microphone.");
            }
        },
        stopRecording() {
            if (this.mediaRecorder && this.mediaRecorder.state !== 'inactive') {
                this.mediaRecorder.stop();
                this.mediaRecorder.stream.getTracks().forEach(track => track.stop());
            }
            this.isRecording = false;
        },
        playPreview(index) {
            const preview = this.audioPreviews[index];
            if (!preview) return;
            this.playAudio(preview.audio);
        },
        removeAudio(index) {
            const preview = this.audioPreviews[index];
            if (preview) {
                preview.audio.pause();
                if (this.activePlayer === preview.audio) this.activePlayer = null;
                URL.revokeObjectURL(preview.url);
            }
            this.audioFiles.splice(index, 1);
            this.audioPreviews.splice(index, 1);
        },
        clearAllAudio() {
            this.audioPreviews.forEach(p => {
                p.audio.pause();
                URL.revokeObjectURL(p.url);
            });
            this.audioFiles = [];
            this.audioPreviews = [];
            this.activePlayer = null;
        },
        async sendMessage() {
            if (this.sending) return;
            if (this.isRecording) {
                this.stopRecording();
                await new Promise(r => setTimeout(r, 200));
            }

            this.sending = true;

            try {
                const formData = new FormData();
                formData.append('message', this.newMessage || '');
                
                // Append image files
                if (this.$refs.imageInput.files.length > 0) {
                    for (let i = 0; i < this.$refs.imageInput.files.length; i++) {
                        formData.append('images[]', this.$refs.imageInput.files[i]);
                    }
                }
                
                // Append audio files
                this.audioFiles.forEach(file => {
                    formData.append('audio[]', file);
                });

                const response = await fetch('{{ route('complaints.messages.store', $complaint) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                });

                let data;
                try {
                    data = await response.json();
                } catch (e) {
                    console.error("Failed to parse JSON response");
                    if (response.ok) {
                        window.location.reload();
                        return;
                    }
                    throw new Error("Server returned an invalid response");
                }

                if (response.ok) {
                    this.newMessage = '';
                    this.imageCount = 0;
                    if (this.$refs.imageInput) this.$refs.imageInput.value = '';
                    this.clearAllAudio();
                    window.location.reload();
                } else {
                    console.error("Server error:", data);
                    alert(data.error || data.message || 'Failed to send message');
                }
            } catch (error) {
                console.error("JS error:", error);
                alert('An error occurred while sending');
            } finally {
                this.sending = false;
            }
        },
        playAudio(player) {
            if (!player) return;

            if (this.activePlayer && this.activePlayer !== player) {
                this.activePlayer.pause();
                this.activePlayer.currentTime = 0;
            }
            
            if (player.paused) {
                const playPromise = player.play();
                if (playPromise !== undefined) {
                    playPromise.then(_ => {
                        this.activePlayer = player;
                        player.onended = () => { 
                            if (this.activePlayer === player) this.activePlayer = null;
                        };
                    }).catch(error => {
                        console.error("Playback failed:", error);
                    });
                }
            } else {
                player.pause();
                if (this.activePlayer === player) this.activePlayer = null;
            }
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0, 54, 22, 0.05); border-radius: 10px; }

    /* Custom Audio Player Styling */
    .custom-audio-player {
        height: 32px;
        opacity: 0.8;
    }
    .bg-primary .custom-audio-player {
        filter: invert(1) hue-rotate(180deg) brightness(1.5);
    }
</style>
@endsection
