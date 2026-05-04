@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto pb-20" x-data="complaintEditor()">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row justify-between items-start gap-6 mb-10">
        <div class="max-w-2xl">
            <!-- Back Button -->
            <a href="{{ route('user.complaints.index') }}" class="inline-flex items-center gap-2 text-[#163a24]/40 hover:text-[#163a24] font-black text-[10px] uppercase tracking-[0.2em] mb-6 transition-colors group">
                <i class="fas fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                Back to My Complaints
            </a>
            <h2 class="text-4xl font-black text-[#163a24] tracking-tight mb-2">Edit Complaint #{{ $complaint->complaint_number }}</h2>
            <p class="text-gray-500 font-bold text-sm leading-relaxed">Refine your submission. You can update the text, add more images, or include a voice explanation.</p>
        </div>
        
        <div class="bg-[#163a24] text-[#f3bc3e] px-6 py-3 rounded-2xl flex items-center gap-3 shadow-lg">
            <i class="fas fa-edit"></i>
            <span class="font-black text-xs uppercase tracking-widest">Editing Mode</span>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-[2.5rem] shadow-xl p-10 lg:p-16 border border-[#163a24]/5">
        <form method="POST" action="{{ route('user.complaints.update', $complaint) }}" enctype="multipart/form-data" class="space-y-10" @submit="submitForm($event)">
            @csrf
            @method('PUT')
            
            <!-- Category & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <label class="block text-[10px] font-black text-[#163a24] uppercase tracking-[0.2em] mb-4">Category</label>
                    <div class="relative">
                        <select name="category" class="w-full px-6 py-5 bg-[#fef9e1] border-none rounded-2xl appearance-none font-bold text-[#163a24] outline-none focus:ring-2 focus:ring-[#f3bc3e] transition cursor-pointer" required>
                            <option value="Academic" {{ old('category', $complaint->category) == 'Academic' ? 'selected' : '' }}>Academic</option>
                            <option value="Faculty" {{ old('category', $complaint->category) == 'Faculty' ? 'selected' : '' }}>Faculty</option>
                            <option value="Administrative" {{ old('category', $complaint->category) == 'Administrative' ? 'selected' : '' }}>Administrative</option>
                            <option value="IT/Technical" {{ old('category', $complaint->category) == 'IT/Technical' ? 'selected' : '' }}>IT/Technical</option>
                            <option value="Health & Safety" {{ old('category', $complaint->category) == 'Health & Safety' ? 'selected' : '' }}>Health & Safety</option>
                        </select>
                        <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-[#163a24]/30">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Current Status</label>
                    <div class="px-6 py-5 bg-gray-50 rounded-2xl font-black text-[#163a24] uppercase tracking-widest text-sm flex items-center gap-3">
                        <span class="w-2 h-2 rounded-full bg-[#f3bc3e] animate-pulse"></span>
                        {{ strtoupper($complaint->status) }}
                    </div>
                </div>
            </div>
            
            <!-- Title -->
            <div x-data="{ count: {{ strlen(old('title', $complaint->title)) }} }">
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-[10px] font-black text-[#163a24] uppercase tracking-[0.2em]">Complaint Title</label>
                    <span class="text-[10px] font-bold text-gray-300 tracking-widest"><span x-text="count">0</span> / 200</span>
                </div>
                <input type="text" name="title" value="{{ old('title', $complaint->title) }}" 
                       @input="count = $event.target.value.length"
                       maxlength="200"
                       class="w-full px-8 py-5 bg-[#fef9e1] border-none rounded-2xl font-bold text-[#163a24] outline-none focus:ring-2 focus:ring-[#f3bc3e] transition shadow-inner"
                       required>
            </div>
            
            <!-- Detailed Description with Integrated Icons -->
            <div x-data="{ count: {{ strlen(old('description', $complaint->description)) }} }" class="relative">
                <div class="flex justify-between items-center mb-4">
                    <label class="block text-[10px] font-black text-[#163a24] uppercase tracking-[0.2em]">Detailed Description</label>
                    <span class="text-[10px] font-bold text-gray-300 tracking-widest"><span x-text="count">0</span> / 2000</span>
                </div>
                
                <div class="relative group bg-[#fef9e1] rounded-[2rem] shadow-inner border-2 border-transparent focus-within:border-[#f3bc3e] transition-all overflow-hidden">
                    <textarea name="description" rows="8" 
                              @input="count = $event.target.value.length"
                              maxlength="2000"
                              class="w-full px-8 pt-8 pb-20 bg-transparent border-none font-bold text-[#163a24] outline-none focus:ring-0 transition resize-none placeholder-[#163a24]/20"
                              required>{{ old('description', $complaint->description) }}</textarea>
                    
                    <!-- Integrated Icons Bar -->
                    <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between px-6 py-4 bg-white/50 backdrop-blur-md rounded-2xl border border-[#163a24]/5">
                        <div class="flex items-center gap-4">
                            <!-- Image Icon Button -->
                            <label class="flex items-center gap-2 px-4 py-2 bg-[#163a24] text-[#f3bc3e] rounded-xl cursor-pointer hover:bg-[#1a442a] transition-all active:scale-95 shadow-lg shadow-black/10">
                                <i class="fas fa-image text-sm"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest" x-text="imageCount > 0 ? imageCount + ' Selected' : 'Add Images'"></span>
                                <input type="file" name="images[]" multiple accept="image/*" class="hidden" @change="imageCount = $event.target.files.length" />
                            </label>

                            <!-- Voice Record Button -->
                            <button type="button" 
                                    @click="isRecording ? stopRecording() : startRecording()"
                                    :class="isRecording ? 'bg-red-500 text-white' : 'bg-[#f3bc3e] text-[#163a24]'"
                                    class="flex items-center gap-2 px-4 py-2 rounded-xl transition-all active:scale-95 shadow-lg shadow-black/5">
                                <i class="fas" :class="isRecording ? 'fa-stop animate-pulse' : 'fa-microphone'"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest" x-text="isRecording ? formatTime(recordingTime) : (audioFiles.length > 0 ? audioFiles.length + ' Recorded' : 'Voice Rec.')"></span>
                            </button>

                            <!-- Play/Delete New Recordings -->
                            <div class="flex flex-wrap gap-2 border-l border-[#163a24]/10 pl-4 ml-2">
                                <template x-for="(audio, index) in audioPreviews" :key="index">
                                    <div class="flex items-center gap-2 bg-white/30 p-1 rounded-lg">
                                        <button type="button" @click="playRecording(index)" class="w-8 h-8 bg-blue-500 text-white rounded-lg flex items-center justify-center hover:bg-blue-600 transition shadow-md">
                                            <i class="fas" :class="activePlayer === audio.audio ? 'fa-pause' : 'fa-play'"></i>
                                        </button>
                                        <button type="button" @click="deleteRecording(index)" class="w-8 h-8 bg-red-50 text-red-500 rounded-lg flex items-center justify-center hover:bg-red-100 transition border border-red-100">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 text-gray-400">
                            <i class="fas fa-info-circle text-xs"></i>
                            <span class="text-[9px] font-bold uppercase tracking-tighter">Images & Voice are optional</span>
                        </div>
                    </div>
                </div>

                @error('profanity') <p class="text-red-500 text-[10px] font-black mt-4 ml-1 bg-red-50 p-6 rounded-3xl border-2 border-red-100 uppercase tracking-tight shadow-sm">{{ $message }}</p> @enderror
            </div>

            <!-- Existing Assets Preview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-4">
                @if($complaint->image_path || $complaint->extra_images)
                <div class="p-6 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i class="fas fa-images"></i> Current Attachments
                    </p>
                    <div class="grid grid-cols-3 sm:grid-cols-4 gap-3">
                        @if($complaint->image_path)
                        <div class="aspect-square rounded-xl overflow-hidden border-2 border-white shadow-sm ring-1 ring-black/5">
                            <img src="{{ asset('storage/' . $complaint->image_path) }}" class="w-full h-full object-cover">
                        </div>
                        @endif
                        @if($complaint->extra_images)
                            @foreach($complaint->extra_images as $img)
                            <div class="aspect-square rounded-xl overflow-hidden border-2 border-white shadow-sm ring-1 ring-black/5">
                                <img src="{{ asset('storage/' . $img) }}" class="w-full h-full object-cover">
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                @endif

                @if($complaint->audio_paths)
                <div class="p-6 bg-[#f3bc3e]/5 rounded-3xl border-2 border-dashed border-[#f3bc3e]/20 flex flex-col justify-center space-y-4">
                    <p class="text-[10px] font-black text-[#163a24]/40 uppercase tracking-widest mb-1 flex items-center gap-2">
                        <i class="fas fa-volume-up"></i> Existing Audio
                    </p>
                    @foreach($complaint->audio_paths as $index => $path)
                    <div class="flex items-center gap-3">
                        <audio controls class="flex-1 h-10 filter sepia brightness-90">
                            <source src="{{ asset('storage/' . $path) }}" type="audio/webm">
                        </audio>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="deleted_audio[]" value="{{ $path }}" id="del_audio_{{ $index }}" class="hidden peer">
                            <label for="del_audio_{{ $index }}" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center cursor-pointer hover:bg-red-500 hover:text-white transition-all peer-checked:bg-red-500 peer-checked:text-white">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </label>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            <!-- Hidden Audio Input -->
            <input type="file" name="audio" id="audioInput" class="hidden">
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-10 pt-10 border-t border-gray-100">
                <a href="{{ route('user.complaints.index') }}" class="text-[10px] font-black text-gray-400 hover:text-red-500 uppercase tracking-[0.2em] transition group">
                    Discard Changes
                </a>
                <button type="submit" class="w-full sm:w-auto px-12 py-5 bg-[#163a24] text-white rounded-2xl font-black uppercase tracking-widest shadow-[0_8px_0_rgb(22,58,36,0.2)] hover:shadow-none hover:translate-y-1 transition-all flex items-center justify-center gap-4 group">
                    Save Updates <i class="fas fa-save text-xs group-hover:scale-110 transition-transform"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function complaintEditor() {
    return {
        imageCount: 0,
        isRecording: false,
        recordingTime: 0,
        audioFiles: [],
        audioPreviews: [],
        mediaRecorder: null,
        audioChunks: [],
        timer: null,
        activePlayer: null,

        formatTime(seconds) {
            const mins = Math.floor(seconds / 60);
            const secs = seconds % 60;
            return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
        },

        async startRecording() {
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
                this.recordingTime = 0;
                this.timer = setInterval(() => {
                    this.recordingTime++;
                }, 1000);
            } catch (err) {
                alert('Microphone access denied or not available.');
            }
        },

        stopRecording() {
            this.mediaRecorder.stop();
            this.mediaRecorder.stream.getTracks().forEach(track => track.stop());
            this.isRecording = false;
            clearInterval(this.timer);
        },

        playRecording(index) {
            const preview = this.audioPreviews[index];
            if (!preview) return;

            if (this.activePlayer && this.activePlayer !== preview.audio) {
                this.activePlayer.pause();
                this.activePlayer.currentTime = 0;
            }

            if (preview.audio.paused) {
                preview.audio.play();
                this.activePlayer = preview.audio;
            } else {
                preview.audio.pause();
                this.activePlayer = null;
            }
        },

        deleteRecording(index) {
            const preview = this.audioPreviews[index];
            if (preview) {
                preview.audio.pause();
                if (this.activePlayer === preview.audio) this.activePlayer = null;
                URL.revokeObjectURL(preview.url);
            }
            this.audioFiles.splice(index, 1);
            this.audioPreviews.splice(index, 1);
        },

        async submitForm(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            
            this.audioFiles.forEach(file => {
                formData.append('audio[]', file);
            });

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                if (response.ok) {
                    window.location.href = "{{ route('user.complaints.index') }}";
                } else {
                    const data = await response.json();
                    alert(data.message || 'Error updating complaint');
                }
            } catch (err) {
                console.error('Error submitting form:', err);
                alert('An error occurred. Please try again.');
            }
        }
    }
}
</script>
@endsection
