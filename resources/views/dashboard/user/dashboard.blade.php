@extends("layouts.app")

@section("content")
<div class="max-w-6xl mx-auto h-[calc(100vh-140px)] flex flex-col relative overflow-hidden">
    
    <div x-data="complaintForm()" class="flex-1 flex flex-col">
        
        <!-- Center Hero Section -->
        <div class="flex-1 flex items-center justify-center px-4"
             x-show="!expanded"
             x-transition:leave="transition ease-in duration-300"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <div class="text-center space-y-4">
                <div class="inline-block bg-[#163a24]/5 text-[#163a24] px-4 py-1 rounded-full text-[10px] font-black uppercase tracking-[0.2em] mb-2">Institutional Feedback Portal</div>
                <h1 class="text-5xl md:text-7xl font-black text-[#163a24] tracking-tight leading-none mb-6">
                    Voice <span class="text-[#f3bc3e] italic">complaint</span> now.
                </h1>
                <div class="flex flex-col items-center gap-2">
                    <p class="text-xs md:text-sm font-bold text-[#163a24]/40 uppercase tracking-[0.3em]">
                        {{ auth()->user()->getRemainingComplaints() }} submissions remaining
                    </p>
                    <div class="flex gap-1">
                        @for($i = 0; $i < 6; $i++)
                            <div class="w-8 h-1 rounded-full {{ $i < auth()->user()->getRemainingComplaints() ? 'bg-[#00a651]' : 'bg-gray-200' }}"></div>
                        @endfor
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Form Container -->
        <div :class="expanded ? 'absolute inset-0 z-50 bg-[#fef9e1] overflow-y-auto' : 'w-full max-w-3xl mx-auto mb-2'"
             class="transition-all duration-500 ease-[cubic-bezier(0.4,0,0.2,1)] flex flex-col justify-end">
            
            <div :class="expanded ? 'min-h-full w-full max-w-4xl mx-auto p-6 md:p-12 flex flex-col justify-center' : 'w-full'">
                <form action="{{ route('user.complaints.store') }}" method="POST" enctype="multipart/form-data" id="complaintForm"
                      :class="expanded ? 'bg-white rounded-[3rem] shadow-2xl p-8 md:p-16 border-4 border-white' : 'bg-white rounded-[2.5rem] shadow-lg p-4 border-4 border-white'"
                      class="transition-all duration-500 flex flex-col">
                    @csrf
                    
                    <!-- Header (Visible when expanded) -->
                    <div x-show="expanded" x-cloak x-transition:enter="transition delay-200 duration-300"
                         x-transition:enter-start="opacity-0 -translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mb-10 flex justify-between items-center">
                        <div>
                            <h2 class="text-3xl font-black text-[#163a24] uppercase tracking-tighter">New Complaint</h2>
                            <p class="text-gray-400 font-bold">What's on your mind?</p>
                        </div>
                        <button type="button" @click="expanded = false" class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:text-red-500 transition-all">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>

                    <!-- Input Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div class="md:col-span-2">
                            <input type="text" name="title" x-model="title" required
                                   placeholder="Title..."
                                   @focus="expanded = true"
                                   class="w-full px-6 py-4 bg-[#fef9e1] border-none rounded-2xl font-bold text-[#163a24] outline-none focus:ring-2 focus:ring-[#f3bc3e]/30 transition placeholder-[#163a24]/30">
                        </div>
                        <div>
                            <div class="relative">
                                <select name="category" x-model="category" required
                                        @focus="expanded = true"
                                        class="w-full px-6 py-4 bg-[#fef9e1] border-none rounded-2xl appearance-none font-bold text-[#163a24] outline-none focus:ring-2 focus:ring-[#f3bc3e]/30 transition cursor-pointer">
                                    <option value="">Category</option>
                                    <option value="Academic">Academic</option>
                                    <option value="Faculty">Faculty</option>
                                    <option value="Administrative">Administrative</option>
                                    <option value="IT/Technical">IT/Technical</option>
                                    <option value="Health & Safety">Health & Safety</option>
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-[#163a24]/30">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Textarea with Action Icons -->
                    <div class="relative">
                        <textarea name="description" x-model="description" required
                                  placeholder="Voice your concern..."
                                  @focus="expanded = true"
                                  :rows="expanded ? 10 : 3"
                                  class="w-full px-6 py-5 pr-24 bg-[#fef9e1] border-none rounded-2xl font-bold text-[#163a24] outline-none focus:ring-2 focus:ring-[#f3bc3e]/30 transition placeholder-[#163a24]/30 resize-none leading-relaxed"></textarea>
                        
                        <!-- Floating Action Icons -->
                        <div class="absolute bottom-4 right-6 flex items-center gap-4 text-[#163a24]/30">
                            <button type="button" @click="$refs.imageInput.click()" class="hover:text-[#f3bc3e] transition-colors p-2" title="Attach Image">
                                <i class="fas fa-image text-lg" :class="images.length > 0 ? 'text-[#f3bc3e]' : ''"></i>
                            </button>
                            <button type="button" @click="toggleRecording()" 
                                    class="hover:text-[#f3bc3e] transition-colors p-2" 
                                    :class="isRecording ? 'text-red-500 animate-pulse' : (audioBlob ? 'text-[#f3bc3e]' : '')"
                                    title="Voice Input">
                                <i class="fas fa-microphone text-lg"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Hidden Inputs -->
                    <input type="file" x-ref="imageInput" name="images[]" multiple class="hidden" accept="image/*" @change="handleImageUpload">
                    <input type="file" x-ref="audioInput" name="audio" class="hidden" accept="audio/*">

                    <!-- Attachments Preview -->
                    <div x-show="images.length > 0 || audioBlob" x-cloak class="mt-4 flex flex-wrap gap-3">
                        <template x-for="(img, index) in images" :key="index">
                            <div class="relative w-16 h-16 rounded-lg overflow-hidden border-2 border-[#f3bc3e]/20 group">
                                <img :src="img.preview" class="w-full h-full object-cover">
                                <button type="button" @click="removeImage(index)" class="absolute inset-0 bg-red-500/80 text-white opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>
                        <div x-show="audioBlob" class="bg-[#f3bc3e]/10 px-4 py-2 rounded-xl flex items-center gap-3 border border-[#f3bc3e]/20">
                            <i class="fas fa-volume-up text-[#f3bc3e]"></i>
                            <span class="text-[10px] font-black text-[#163a24]">Voice Message</span>
                            <button type="button" @click="removeAudio()" class="text-red-500 hover:text-red-700">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Footer (Visible when expanded) -->
                    <div x-show="expanded" x-cloak x-transition:enter="transition delay-300 duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="mt-8 flex flex-col md:flex-row items-center justify-between gap-6 pt-8 border-t border-gray-100">
                        
                        <div class="flex items-center gap-4 text-gray-400">
                            <div class="w-10 h-10 bg-[#fef9e1] rounded-xl flex items-center justify-center text-[#163a24]/40">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest" x-text="statusText"></span>
                        </div>

                        <div class="flex items-center gap-6 w-full md:w-auto">
                            <button type="submit" class="flex-1 md:flex-none px-10 py-4 bg-[#163a24] text-white rounded-xl font-black uppercase tracking-widest shadow-xl hover:bg-[#1a442a] transition-all flex items-center justify-center gap-3">
                                Submit <i class="fas fa-paper-plane text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </form>
                
                <!-- Submission helper (Visible when not expanded) -->
                <p x-show="!expanded" class="text-center mt-4 text-[10px] font-black text-[#163a24]/20 uppercase tracking-[0.2em]">
                    Press <span class="bg-[#163a24]/5 px-2 py-1 rounded text-[#163a24]/40">Enter</span> to grow the workspace
                </p>
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
        audioBlob: null,
        statusText: 'Media can be attached using the icons above',

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
                        this.audioBlob = new Blob(this.audioChunks, { type: 'audio/webm' });
                        const audioFile = new File([this.audioBlob], 'voice-message.webm', { type: 'audio/webm' });
                        
                        // Set the file to the hidden input
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(audioFile);
                        this.$refs.audioInput.files = dataTransfer.files;
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

        removeAudio() {
            this.audioBlob = null;
            this.$refs.audioInput.value = '';
            this.statusText = 'Media can be attached using the icons above';
        },

        resetForm() {
            this.expanded = false;
            this.title = '';
            this.category = '';
            this.description = '';
            this.images = [];
            this.audioBlob = null;
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
