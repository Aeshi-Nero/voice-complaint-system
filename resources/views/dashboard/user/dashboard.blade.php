@extends("layouts.app")

@section("content")
<div class="h-[calc(100vh-140px)] flex flex-col relative overflow-hidden">
    
    <div x-data="complaintForm()" class="flex-1 flex flex-col w-full">
        
        <!-- Center Hero Section -->
        <div class="flex-1 flex items-center justify-center px-6"
             x-show="!expanded"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="text-center space-y-6 max-w-2xl">
                <div class="inline-block bg-[#163a24]/5 text-[#163a24] px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[0.2em]">Institutional Feedback Portal</div>
                <h1 class="text-4xl md:text-7xl font-black text-[#163a24] tracking-tight leading-[1.1]">
                    Voice your complaints now.
                </h1>
                <div class="flex flex-col items-center gap-3 pt-4">
                    <p class="text-[10px] md:text-sm font-black text-[#163a24]/40 uppercase tracking-[0.3em]">
                        {{ auth()->user()->getRemainingComplaints() }} submissions remaining
                    </p>
                    <div class="flex gap-1.5">
                        @for($i = 0; $i < 6; $i++)
                            <div class="w-8 md:w-10 h-1.5 rounded-full {{ $i < auth()->user()->getRemainingComplaints() ? 'bg-[#00a651]' : 'bg-gray-200' }}"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Form Container -->
        <div :class="expanded ? 'fixed inset-0 z-[100] bg-[#fef9e1] overflow-y-auto' : 'w-full max-w-4xl mx-auto px-4 mb-4 md:mb-8'"
             class="transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] flex flex-col justify-end">
            
            <div :class="expanded ? 'min-h-full w-full p-4 md:p-12 flex flex-col' : 'w-full'">
                <form action="{{ route('user.complaints.store') }}" method="POST" enctype="multipart/form-data" id="complaintForm"
                      @submit.prevent="submitComplaint()"
                      :class="expanded ? 'bg-white rounded-[2.5rem] md:rounded-[4rem] shadow-2xl p-6 md:p-16 border-4 border-white flex-1' : 'bg-white rounded-[2rem] md:rounded-[3rem] shadow-lg p-3 md:p-5 border-4 border-white'"
                      class="transition-all duration-500 flex flex-col relative">
                    @csrf
                    
                    <!-- Header (Visible when expanded) -->
                    <div x-show="expanded" x-cloak x-transition:enter="transition delay-200 duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mb-8 md:mb-12 flex justify-between items-start">
                        <div>
                            <div class="bg-red-50 text-red-600 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest inline-block mb-3">Draft Submission</div>
                            <h2 class="text-3xl md:text-5xl font-black text-[#163a24] uppercase tracking-tighter leading-none">New Complaint</h2>
                            <p class="text-gray-400 font-bold mt-2 text-sm md:text-base">What's on your mind?</p>
                        </div>
                        <button type="button" @click="expanded = false" class="w-12 h-12 md:w-16 md:h-16 rounded-2xl bg-gray-50 flex items-center justify-center text-gray-400 hover:text-red-500 hover:bg-red-50 transition-all border border-gray-100">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>

                    <!-- Input Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 md:gap-4 mb-3 md:mb-4">
                        <div class="md:col-span-2">
                            <input type="text" name="title" x-model="title" required
                                   placeholder="Title..."
                                   @focus="expanded = true"
                                   class="w-full px-6 py-4 md:py-6 bg-[#fef9e1] border-none rounded-2xl md:rounded-3xl font-bold text-[#163a24] outline-none focus:ring-4 focus:ring-[#f3bc3e]/20 transition placeholder-[#163a24]/30 text-base md:text-xl">
                        </div>
                        <div>
                            <div class="relative h-full">
                                <select name="category" x-model="category" required
                                        @focus="expanded = true"
                                        class="w-full h-full px-6 py-4 md:py-6 bg-[#fef9e1] border-none rounded-2xl md:rounded-3xl appearance-none font-bold text-[#163a24] outline-none focus:ring-4 focus:ring-[#f3bc3e]/20 transition cursor-pointer text-base md:text-xl">
                                    <option value="">Category</option>
                                    <option value="Academic">Academic</option>
                                    <option value="Faculty">Faculty</option>
                                    <option value="Administrative">Administrative</option>
                                    <option value="IT/Technical">IT/Technical</option>
                                    <option value="Health & Safety">Health & Safety</option>
                                </select>
                                <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-[#163a24]/30">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Textarea with Action Icons -->
                    <div class="relative flex-1 flex flex-col">
                        <textarea name="description" x-model="description" required
                                  placeholder="Voice your concern..."
                                  @focus="expanded = true"
                                  :rows="expanded ? 12 : 3"
                                  :class="expanded ? 'flex-1' : ''"
                                  class="w-full px-6 py-5 md:py-8 pr-24 bg-[#fef9e1] border-none rounded-2xl md:rounded-3xl font-bold text-[#163a24] outline-none focus:ring-4 focus:ring-[#f3bc3e]/20 transition placeholder-[#163a24]/30 resize-none leading-relaxed text-base md:text-xl"></textarea>
                        
                        <!-- Floating Action Icons -->
                        <div class="absolute bottom-6 right-6 flex items-center gap-4 text-[#163a24]/30">
                            <button type="button" @click="$refs.imageInput.click()" class="hover:text-[#f3bc3e] transition-all p-2 hover:scale-110" title="Attach Image">
                                <i class="fas fa-image text-xl md:text-2xl" :class="images.length > 0 ? 'text-[#f3bc3e]' : ''"></i>
                            </button>
                            <button type="button" @click="toggleRecording()" 
                                    class="hover:text-[#f3bc3e] transition-all p-2 hover:scale-110 relative" 
                                    :class="isRecording ? 'text-red-500 animate-pulse' : (audioFiles.length > 0 ? 'text-[#f3bc3e]' : '')"
                                    title="Voice Input">
                                <i class="fas fa-microphone text-xl md:text-2xl"></i>
                                <template x-if="audioFiles.length > 0">
                                    <span class="absolute top-0 right-0 w-5 h-5 bg-red-500 text-white text-[8px] font-black rounded-full flex items-center justify-center border-2 border-[#fef9e1]" x-text="audioFiles.length"></span>
                                </template>
                            </button>
                        </div>
                    </div>

                    <!-- Hidden Inputs -->
                    <input type="file" x-ref="imageInput" name="images[]" multiple class="hidden" accept="image/*" @change="handleImageUpload">
                    <input type="file" x-ref="audioInput" name="audio" class="hidden" accept="audio/*">

                    <!-- Attachments Preview -->
                    <div x-show="images.length > 0 || audioFiles.length > 0" x-cloak class="mt-4 flex flex-wrap gap-3 p-2">
                        <template x-for="(img, index) in images" :key="index">
                            <div class="relative w-20 h-20 rounded-2xl overflow-hidden border-4 border-white shadow-md group">
                                <img :src="img.preview" class="w-full h-full object-cover">
                                <button type="button" @click="removeImage(index)" class="absolute inset-0 bg-red-500/80 text-white opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                        <template x-for="(audio, index) in audioPreviews" :key="index">
                            <div class="bg-[#163a24] text-white px-5 py-3 rounded-2xl flex items-center gap-4 shadow-lg">
                                <button type="button" @click="playAudio(index)" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                                    <i class="fas" :class="activePlayer === audio.audio ? 'fa-pause' : 'fa-play'" class="text-[#f3bc3e]"></i>
                                </button>
                                <div class="flex flex-col">
                                    <span class="text-[10px] font-black uppercase tracking-widest" x-text="'Voice Record ' + (audioPreviews.length > 1 ? (index + 1) : '')"></span>
                                    <span class="text-[8px] text-white/40 uppercase font-bold" x-text="activePlayer === audio.audio ? 'Playing...' : 'Ready to listen'"></span>
                                </div>
                                <button type="button" @click="removeAudio(index)" class="text-white/40 hover:text-red-400 transition-colors ml-2">
                                    <i class="fas fa-times-circle text-lg"></i>
                                </button>
                            </div>
                        </template>
                    </div>

                    <!-- Footer (Visible when expanded) -->
                    <div x-show="expanded" x-cloak x-transition:enter="transition delay-300 duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-8 flex flex-col md:flex-row items-center justify-between gap-6 pt-8 border-t border-gray-100">
                        
                        <div class="flex items-center gap-4 text-gray-400 bg-gray-50 px-6 py-3 rounded-2xl">
                            <i class="fas fa-info-circle text-[#163a24]/40"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest" x-text="statusText"></span>
                        </div>

                        <div class="flex items-center gap-6 w-full md:w-auto">
                            <button type="submit" class="w-full md:w-auto px-12 py-5 bg-[#163a24] text-white rounded-2xl font-black uppercase tracking-widest shadow-[0_10px_0_rgb(13,34,21)] active:shadow-none active:translate-y-2 transition-all flex items-center justify-center gap-4 text-sm">
                                Submit Complaint <i class="fas fa-paper-plane text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Submission helper (Visible when not expanded) -->
                <p x-show="!expanded" class="text-center mt-6 text-[10px] font-black text-[#163a24]/30 uppercase tracking-[0.3em] animate-pulse">
                    Click to start your <span class="text-[#00a651]">voice</span>
                </p>
            </div>
        </div>
    </div>
</div>
    </div>
</div>

<script>
function complaintForm() {
    return {
        expanded: false,
        title: '',
        category: '',
        description: '',
        images: [],
        isRecording: false,
        mediaRecorder: null,
        audioChunks: [],
        audioFiles: [],
        audioPreviews: [],
        statusText: 'Media can be attached using the icons above',
        activePlayer: null,

        handleImageUpload(e) {
            const files = Array.from(e.target.files);
            files.forEach(file => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.images.push({
                        file: file,
                        preview: e.target.result
                    });
                };
                reader.readAsDataURL(file);
            });
            this.expanded = true;
        },

        removeImage(index) {
            this.images.splice(index, 1);
        },

        playAudio(index) {
            const preview = this.audioPreviews[index];
            if (!preview) return;
            
            if (this.activePlayer && this.activePlayer !== preview.audio) {
                this.activePlayer.pause();
                this.activePlayer.currentTime = 0;
            }

            if (preview.audio.paused) {
                preview.audio.play();
                this.activePlayer = preview.audio;
                preview.audio.onended = () => { if(this.activePlayer === preview.audio) this.activePlayer = null; };
            } else {
                preview.audio.pause();
                if (this.activePlayer === preview.audio) this.activePlayer = null;
            }
        },

        async toggleRecording() {
            if (this.isRecording) {
                this.mediaRecorder.stop();
                this.isRecording = false;
                this.statusText = 'Recording stopped';
            } else {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    this.mediaRecorder = new MediaRecorder(stream);
                    this.audioChunks = [];
                    
                    this.mediaRecorder.ondataavailable = (e) => {
                        this.audioChunks.push(e.data);
                    };

                    this.mediaRecorder.onstop = () => {
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
                    this.expanded = true;
                    this.statusText = 'Recording voice message...';
                } catch (err) {
                    console.error('Microphone access denied:', err);
                    alert('Please allow microphone access to use voice messages.');
                }
            }
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
            if (this.audioFiles.length === 0) {
                this.statusText = 'Media can be attached using the icons above';
            }
        },

        async submitComplaint() {
            const formData = new FormData(document.getElementById('complaintForm'));
            
            // Re-append images from our array if needed, but the input might already have them.
            // However, audioFiles definitely needs to be appended manually.
            this.audioFiles.forEach(file => {
                formData.append('audio[]', file);
            });

            try {
                const response = await fetch("{{ route('user.complaints.store') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.ok) {
                    window.location.reload();
                } else {
                    const data = await response.json();
                    alert(data.message || 'Error submitting complaint');
                }
            } catch (err) {
                console.error('Error submitting form:', err);
                alert('An error occurred. Please try again.');
            }
        },

        resetForm() {
            this.audioPreviews.forEach(p => {
                p.audio.pause();
                URL.revokeObjectURL(p.url);
            });
            this.expanded = false;
            this.title = '';
            this.category = '';
            this.description = '';
            this.images = [];
            this.audioFiles = [];
            this.audioPreviews = [];
            this.activePlayer = null;
            this.$refs.imageInput.value = '';
            this.$refs.audioInput.value = '';
            this.statusText = 'Media can be attached using the icons above';
        }
    }
}
</script>

<style>
    [x-cloak] { display: none !important; }
    .tracking-tightest { letter-spacing: -0.05em; }
</style>
@endsection
