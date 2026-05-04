@extends("layouts.app")

@section('content')
<div class="fixed inset-0 z-50 bg-[#fef9e1] flex flex-col lg:relative lg:z-0 lg:bg-transparent lg:h-[calc(100vh-140px)]" x-data="complaintChat()">
    
    <!-- Mobile Sticky Header -->
    <div class="bg-[#163a24] text-white p-4 flex items-center justify-between shadow-lg shrink-0 lg:rounded-t-[2.5rem]">
        <div class="flex items-center gap-3">
            <a href="{{ route('user.dashboard') }}" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h2 class="text-sm font-black uppercase tracking-widest leading-none">{{ Str::limit($complaint->title, 25) }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[9px] font-bold text-white/40 uppercase tracking-tighter">{{ $complaint->complaint_number }}</span>
                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                    <span class="text-[9px] font-bold text-white/60 uppercase tracking-tighter">{{ $complaint->category }}</span>
                    <span class="w-1 h-1 rounded-full bg-white/20"></span>
                    <span class="text-[9px] font-black uppercase {{ $complaint->status === 'resolved' ? 'text-emerald-400' : ($complaint->status === 'rejected' ? 'text-red-400' : 'text-[#f3bc3e]') }}">
                        {{ str_replace('_', ' ', $complaint->status) }}
                    </span>
                </div>
            </div>
        </div>
        
        <button @click="showDetails = true" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition">
            <i class="fas fa-ellipsis-v"></i>
        </button>
    </div>

    <!-- Chat Messages Area -->
    <div id="chat-container" class="flex-1 overflow-y-auto p-4 space-y-6 custom-scrollbar lg:bg-white/50 lg:rounded-b-[2.5rem] lg:shadow-inner pb-32 lg:pb-24">
        
        <!-- Case Information Banner -->
        <div class="flex justify-center mb-8">
            <div class="bg-[#163a24]/5 px-6 py-2 rounded-full border border-[#163a24]/10 text-center">
                <p class="text-[10px] font-black text-[#163a24]/40 uppercase tracking-widest">Case opened on {{ $complaint->created_at->format('M d, Y') }}</p>
            </div>
        </div>

        <!-- Initial Complaint Message -->
        <div class="flex justify-end ml-12">
            <div class="bg-[#00a651] text-white p-5 rounded-[2rem] rounded-tr-none shadow-lg shadow-green-900/10 max-w-full relative group">
                <div class="flex items-center gap-2 mb-2 opacity-60">
                    <span class="text-[10px] font-black uppercase tracking-widest">You • Initial Report</span>
                </div>
                
                <div x-show="!editingDescription">
                    <p class="text-sm leading-relaxed font-medium">{{ $complaint->description }}</p>
                    
                    @if($complaint->image_path || $complaint->audio_paths || $complaint->extra_images)
                    <div class="mt-4 pt-4 border-t border-white/10 space-y-4">
                        @if($complaint->audio_paths)
                            @foreach($complaint->audio_paths as $index => $audioPath)
                            <div class="bg-white/10 p-3 rounded-2xl border border-white/5">
                                <p class="text-[10px] font-black uppercase tracking-widest mb-2 opacity-60">Voice Evidence {{ count($complaint->audio_paths) > 1 ? ($index + 1) : '' }}</p>
                                <audio controls src="{{ asset('storage/' . $audioPath) }}" class="w-full h-10 custom-audio-player"></audio>
                            </div>
                            @endforeach
                        @endif
                        
                        <div class="grid grid-cols-3 gap-2">
                            @if($complaint->image_path)
                            <img src="{{ asset('storage/' . $complaint->image_path) }}" class="rounded-xl w-full aspect-square object-cover cursor-zoom-in" @click="window.open($el.src)">
                            @endif
                            @if($complaint->extra_images)
                                @foreach($complaint->extra_images as $extra)
                                <img src="{{ asset('storage/' . $extra) }}" class="rounded-xl w-full aspect-square object-cover cursor-zoom-in" @click="window.open($el.src)">
                                @endforeach
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                
                @if($complaint->status === 'pending')
                <div x-show="editingDescription" x-cloak class="space-y-4">
                    <div class="relative">
                        <textarea x-model="editedDescription" 
                                  class="w-full bg-white/10 border border-white/20 rounded-xl p-3 text-sm font-medium focus:ring-0 focus:border-white/40 outline-none min-h-[120px]" placeholder="Update description..."></textarea>
                        
                        <div class="absolute bottom-3 right-3 flex items-center gap-2">
                            <div x-show="showAddMenu" x-cloak class="flex items-center gap-2 bg-[#163a24] rounded-xl p-1 shadow-xl border border-white/10">
                                <button type="button" @click="$refs.editImageInput.click(); showAddMenu = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition text-white">
                                    <i class="fas fa-image text-xs"></i>
                                </button>
                                <button type="button" @click="toggleRecording()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition" :class="isRecording ? 'text-red-500 animate-pulse' : 'text-white'">
                                    <i class="fas fa-microphone text-xs"></i>
                                </button>
                            </div>
                            <button type="button" @click="showAddMenu = !showAddMenu" class="w-10 h-10 bg-[#f3bc3e] text-[#163a24] rounded-xl flex items-center justify-center shadow-lg hover:scale-110 transition-transform active:scale-95">
                                <i class="fas fa-plus transition-transform" :class="showAddMenu ? 'rotate-45' : ''"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Attachments List in Edit Mode -->
                    <div class="space-y-3">
                        <template x-if="(originalAudio && !audioDeleted) || editHasAudio">
                            <div class="bg-white/10 p-3 rounded-xl flex items-center gap-3 border border-white/5 relative group">
                                <i class="fas fa-microphone text-[#f3bc3e]"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest" x-text="editHasAudio ? 'New Recording' : 'Original Audio'"></span>
                                <button type="button" @click="if(editHasAudio) { editHasAudio = false; if($refs.editAudioInput) $refs.editAudioInput.value=''; } else { audioDeleted = true; }" class="ml-auto w-6 h-6 bg-red-500 text-white rounded-lg flex items-center justify-center hover:bg-red-600 transition">
                                    <i class="fas fa-times text-[8px]"></i>
                                </button>
                            </div>
                        </template>

                        <div class="grid grid-cols-4 gap-2">
                            <template x-for="(img, index) in originalImages" :key="index">
                                <div x-show="!deletedImages.includes(img)" class="relative group aspect-square">
                                    <img :src="'/storage/' + img" class="w-full h-full object-cover rounded-xl border border-white/10">
                                    <button type="button" @click="deletedImages.push(img)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-lg flex items-center justify-center shadow-lg">
                                        <i class="fas fa-times text-[8px]"></i>
                                    </button>
                                </div>
                            </template>
                            <template x-for="(img, index) in editNewImagesPreviews" :key="'new_'+index">
                                <div class="relative group aspect-square">
                                    <img :src="img" class="w-full h-full object-cover rounded-xl border-2 border-[#f3bc3e]/50">
                                    <button type="button" @click="removeNewEditImage(index)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-lg flex items-center justify-center shadow-lg">
                                        <i class="fas fa-times text-[8px]"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-2">
                        <button @click="editingDescription = false" class="text-[10px] font-black uppercase tracking-widest opacity-60 hover:opacity-100 transition">Cancel</button>
                        <button @click="saveDescription()" class="text-[10px] font-black uppercase tracking-widest text-[#f3bc3e] hover:brightness-110 transition" :disabled="sending">
                            <span x-text="sending ? 'Saving...' : 'Save Changes'"></span>
                        </button>
                    </div>
                </div>
                @endif
                
                <!-- Hidden Inputs (Moved outside x-if for reliable ref access) -->
                <input type="file" x-ref="editImageInput" class="hidden" accept="image/*" multiple @change="handleEditImageSelect($event)">
                <input type="file" x-ref="editAudioInput" class="hidden" accept="audio/*">

                <div class="flex items-center justify-end mt-3 gap-3">
                    <p class="text-[9px] font-black text-white/40 uppercase">{{ $complaint->created_at->format('h:i A') }}</p>
                    @if($complaint->status === 'pending')
                    <button @click="startEditingComplaint()" 
                            x-show="!editingDescription"
                            class="text-white/40 hover:text-white transition-colors">
                        <i class="fas fa-pencil-alt text-[10px]"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>

        @foreach($complaint->messages as $msg)
            @if($msg->is_admin)
                <!-- Admin Message (Left) -->
                <div class="flex justify-start mr-12">
                    <div class="bg-white text-gray-800 p-5 rounded-[2rem] rounded-tl-none shadow-sm border border-gray-100 max-w-full">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Admin Response</span>
                        </div>
                        <p class="text-sm leading-relaxed font-semibold">{{ $msg->message }}</p>
                        
                        @if($msg->audio_paths)
                            @foreach($msg->audio_paths as $index => $audioPath)
                            <div class="mt-3 bg-gray-50 p-3 rounded-2xl border border-gray-100">
                                <p class="text-[10px] font-black uppercase tracking-widest mb-2 text-gray-400">Voice Message {{ count($msg->audio_paths) > 1 ? ($index + 1) : '' }}</p>
                                <audio controls src="{{ asset('storage/' . $audioPath) }}" class="w-full h-10 custom-audio-player"></audio>
                            </div>
                            @endforeach
                        @endif

                        @if($msg->images)
                        <div class="mt-3 grid grid-cols-2 gap-2">
                            @foreach($msg->images as $image)
                            <img src="{{ asset('storage/' . $image) }}" class="rounded-xl w-full h-24 object-cover cursor-zoom-in border border-gray-50" @click="window.open($el.src)">
                            @endforeach
                        </div>
                        @endif
                        
                        <p class="text-[9px] font-black text-gray-300 text-right mt-3 uppercase">{{ $msg->created_at->format('h:i A') }}</p>
                    </div>
                </div>
            @else
                <!-- User Reply (Right) -->
                <div class="flex justify-end ml-12">
                    <div class="bg-[#00a651] text-white p-5 rounded-[2rem] rounded-tr-none shadow-lg shadow-green-900/10 max-w-full relative group">
                        <div x-show="editingMessageId !== {{ $msg->id }}" class="space-y-3">
                            <p class="text-sm leading-relaxed font-medium">{{ $msg->message }}</p>
                            
                            @if($msg->audio_paths)
                                @foreach($msg->audio_paths as $index => $audioPath)
                                <div class="bg-white/10 p-3 rounded-2xl border border-white/5">
                                    <p class="text-[10px] font-black uppercase tracking-widest mb-2 opacity-60">Voice Message {{ count($msg->audio_paths) > 1 ? ($index + 1) : '' }}</p>
                                    <audio controls src="{{ asset('storage/' . $audioPath) }}" class="w-full h-10 custom-audio-player"></audio>
                                </div>
                                @endforeach
                            @endif

                            @if($msg->images)
                            <div class="mt-3 grid grid-cols-2 gap-2">
                                @foreach($msg->images as $image)
                                <img src="{{ asset('storage/' . $image) }}" class="rounded-xl w-full h-24 object-cover cursor-zoom-in border border-white/10" @click="window.open($el.src)">
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <div x-show="editingMessageId === {{ $msg->id }}" x-cloak class="space-y-4">
                            <div class="relative">
                                <textarea x-model="editedMessageContent" 
                                          class="w-full bg-white/10 border border-white/20 rounded-xl p-3 text-sm font-medium focus:ring-0 focus:border-white/40 outline-none min-h-[100px]" placeholder="Update your message"></textarea>
                                
                                <div class="absolute bottom-3 right-3 flex items-center gap-2">
                                    <div x-show="showAddMenuMsg" x-cloak class="flex items-center gap-2 bg-[#163a24] rounded-xl p-1 shadow-xl border border-white/10">
                                        <button type="button" @click="$refs['editMsgImageInput_' + {{ $msg->id }}].click(); showAddMenuMsg = false" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition text-white">
                                            <i class="fas fa-image text-xs"></i>
                                        </button>
                                        <button type="button" @click="toggleRecording()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-white/10 transition" :class="isRecording ? 'text-red-500 animate-pulse' : 'text-white'">
                                            <i class="fas fa-microphone text-xs"></i>
                                        </button>
                                    </div>
                                    <button type="button" @click="showAddMenuMsg = !showAddMenuMsg" class="w-10 h-10 bg-[#f3bc3e] text-[#163a24] rounded-xl flex items-center justify-center shadow-lg hover:scale-110 transition-transform active:scale-95">
                                        <i class="fas fa-plus transition-transform" :class="showAddMenuMsg ? 'rotate-45' : ''"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <template x-if="(originalMsgAudio[{{ $msg->id }}] && !msgAudioDeleted[{{ $msg->id }}]) || editMsgHasAudio">
                                    <div class="bg-white/10 p-3 rounded-xl flex items-center gap-3 border border-white/5 relative group">
                                        <i class="fas fa-microphone text-[#f3bc3e]"></i>
                                        <span class="text-[10px] font-black uppercase tracking-widest" x-text="editMsgHasAudio ? 'New Recording' : 'Original Audio'"></span>
                                        <button type="button" @click="if(editMsgHasAudio) { editMsgHasAudio = false; if($refs['editMsgAudioInput_' + {{ $msg->id }}]) $refs['editMsgAudioInput_' + {{ $msg->id }}].value=''; } else { msgAudioDeleted[{{ $msg->id }}] = true; }" class="ml-auto w-6 h-6 bg-red-500 text-white rounded-lg flex items-center justify-center hover:bg-red-600 transition">
                                            <i class="fas fa-times text-[8px]"></i>
                                        </button>
                                    </div>
                                </template>

                                <div class="grid grid-cols-4 gap-2">
                                    <template x-for="(img, index) in originalMsgImages[{{ $msg->id }}] || []" :key="index">
                                        <div x-show="!(deletedMsgImages[{{ $msg->id }}] || []).includes(img)" class="relative group aspect-square">
                                            <img :src="'/storage/' + img" class="w-full h-full object-cover rounded-xl border border-white/10">
                                            <button type="button" @click="markMsgImageDeleted({{ $msg->id }}, img)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-lg flex items-center justify-center shadow-lg">
                                                <i class="fas fa-times text-[8px]"></i>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-for="(img, index) in editNewMsgImagesPreviews" :key="'newmsg_'+index">
                                        <div class="relative group aspect-square">
                                            <img :src="img" class="w-full h-full object-cover rounded-xl border-2 border-[#f3bc3e]/50">
                                            <button type="button" @click="removeNewMsgEditImage(index)" class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white rounded-lg flex items-center justify-center shadow-lg">
                                                <i class="fas fa-times text-[8px]"></i>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex justify-end gap-3">
                                <button @click="editingMessageId = null" class="text-[10px] font-black uppercase tracking-widest opacity-60 hover:opacity-100 transition">Cancel</button>
                                <button @click="saveMessage({{ $msg->id }})" class="text-[10px] font-black uppercase tracking-widest text-[#f3bc3e] hover:brightness-110 transition" :disabled="sending">Save</button>
                            </div>
                        </div>

                        <!-- Hidden Edit Inputs for this message -->
                        <input type="file" x-ref="editMsgImageInput_{{ $msg->id }}" class="hidden" accept="image/*" multiple @change="handleMsgEditImageSelect($event, {{ $msg->id }})">
                        <input type="file" x-ref="editMsgAudioInput_{{ $msg->id }}" class="hidden" accept="audio/*">
                        
                        <div class="flex items-center justify-end mt-3 gap-3">
                            <p class="text-[9px] font-black text-white/40 uppercase">{{ $msg->created_at->format('h:i A') }}</p>
                            <button @click="startEditingMessage({{ $msg->id }}, '{{ addslashes($msg->message) }}')" 
                                    x-show="editingMessageId !== {{ $msg->id }}"
                                    class="text-white/40 hover:text-white transition-colors">
                                <i class="fas fa-pencil-alt text-[10px]"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Message Input Bar (Sticky Bottom) -->
    @if($complaint->status !== 'resolved' && $complaint->status !== 'rejected')
    <div class="p-4 bg-white/80 backdrop-blur-md border-t border-gray-100 sticky bottom-0 z-40 lg:rounded-b-[2.5rem]">
        
        <!-- Media Preview Area (Pre-sent) -->
        <div x-show="imagePreviews.length > 0 || audioFiles.length > 0" x-cloak class="max-w-4xl mx-auto mb-4 p-4 bg-[#163a24]/5 rounded-2xl flex flex-wrap gap-3">
            <!-- Audio Previews -->
            <template x-for="(audio, index) in audioPreviews" :key="index">
                <div class="bg-[#00a651] text-white p-3 rounded-xl flex items-center gap-3 w-full sm:w-auto">
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

            <!-- Image Previews -->
            <template x-for="(img, index) in imagePreviews" :key="index">
                <div class="relative group w-20 h-20">
                    <img :src="img" class="w-full h-full object-cover rounded-xl border border-[#163a24]/10 shadow-sm">
                    <button type="button" @click="removeImage(index)" class="absolute -top-2 -right-2 w-6 h-6 bg-red-500 text-white rounded-full flex items-center justify-center shadow-lg scale-0 group-hover:scale-100 transition-transform">
                        <i class="fas fa-times text-[8px]"></i>
                    </button>
                </div>
            </template>
        </div>

        <form @submit.prevent="sendMessage()" id="replyForm" class="max-w-4xl mx-auto flex items-center gap-3">
            <div class="flex items-center gap-2">
                <button type="button" 
                        @click="toggleRecording()" 
                        :disabled="sending"
                        :class="isRecording ? 'bg-red-500 text-white animate-pulse' : 'bg-[#163a24]/5 text-[#163a24]'"
                        class="w-12 h-12 flex items-center justify-center rounded-2xl transition shrink-0 relative">
                    <i class="fas fa-microphone"></i>
                </button>

                <button type="button" @click="$refs.imageInput.click()" :disabled="sending" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-[#163a24]/5 text-[#163a24] hover:bg-[#163a24]/10 transition shrink-0 relative">
                    <i class="fas fa-paperclip"></i>
                </button>
            </div>
            
            <!-- Hidden inputs must not be inside x-if -->
            <input type="file" x-ref="imageInput" multiple class="hidden" accept="image/*" @change="handleImageSelect($event)">
            <input type="file" x-ref="audioInput" class="hidden" accept="audio/*">
            
            <div class="flex-1 relative">
                <input type="text" x-model="newMessage" :required="audioFiles.length === 0 && imagePreviews.length === 0" :disabled="sending" placeholder="Type your reply..." class="w-full px-6 py-4 bg-gray-50 border-none rounded-[1.5rem] font-bold text-sm text-[#163a24] focus:ring-2 focus:ring-[#00a651]/20 outline-none transition-all placeholder-gray-400">
            </div>
            
            <button type="submit" :disabled="sending || (!newMessage && audioFiles.length === 0 && imagePreviews.length === 0)" class="w-12 h-12 flex items-center justify-center rounded-2xl bg-[#00a651] text-white shadow-lg shadow-green-200 hover:bg-[#008d44] transition-all active:scale-95 shrink-0 disabled:opacity-50">
                <i class="fas" :class="sending ? 'fa-spinner fa-spin' : 'fa-paper-plane text-sm'"></i>
            </button>
        </form>
    </div>
    @endif

    <!-- Details Overlay -->
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
                    'label' => 'Original Report' . (count($complaint->audio_paths) > 1 ? ' (' . ($index + 1) . ')' : ''),
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
                        'label' => ($msg->is_admin ? 'Admin Response' : 'Your Reply') . (count($msg->audio_paths) > 1 ? ' (' . ($index + 1) . ')' : ''),
                        'type' => 'message',
                        'id' => $msg->id . '_' . $index
                    ];
                }
            }
        }
    @endphp
    <div x-show="showDetails" x-cloak class="fixed inset-0 z-[100] flex items-end justify-center sm:items-center p-0 sm:p-4">
        <div x-show="showDetails" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="showDetails = false" 
             class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity"></div>

        <div x-show="showDetails" 
             x-transition:enter="transition ease-out duration-300 transform" 
             x-transition:enter-start="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0" 
             x-transition:enter-end="translate-y-0 sm:scale-100 sm:opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-y-0 sm:scale-100 sm:opacity-100"
             x-transition:leave-end="translate-y-full sm:translate-y-0 sm:scale-95 sm:opacity-0"
             class="bg-white w-full sm:max-w-2xl rounded-t-[3rem] sm:rounded-[3rem] p-8 space-y-8 max-h-[85vh] overflow-y-auto relative z-10 shadow-2xl border-t sm:border-none border-gray-100">
            
            <div class="flex justify-between items-center mb-2">
                <h3 class="text-2xl font-black text-[#163a24] uppercase tracking-tight">Case Details</h3>
                <button @click="showDetails = false" class="w-10 h-10 flex items-center justify-center rounded-2xl bg-gray-50 text-gray-400 hover:bg-gray-100 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="space-y-8">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mt-1">Official Reference: {{ $complaint->complaint_number }}</p>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Current Status</p>
                        <div class="flex items-center gap-3">
                            <div class="w-3 h-3 rounded-full {{ $complaint->status === 'resolved' ? 'bg-emerald-500' : ($complaint->status === 'rejected' ? 'bg-red-500' : 'bg-orange-500') }} shadow-sm"></div>
                            <span class="font-black text-sm uppercase text-gray-700 tracking-wider">{{ ucfirst(str_replace('_', ' ', $complaint->status)) }}</span>
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 rounded-[2rem] border border-gray-100">
                        <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-3">Submission Category</p>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-tag text-[#163a24]/20 text-xs"></i>
                            <span class="font-black text-sm uppercase text-gray-700 tracking-wider">{{ $complaint->category }}</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Filing Timeline</p>
                    <div class="space-y-6 relative pl-4 border-l-2 border-dashed border-gray-100 ml-2">
                        <div class="relative flex items-center gap-4">
                            <div class="absolute -left-[1.35rem] w-4 h-4 rounded-full bg-emerald-500 border-4 border-white shadow-sm"></div>
                            <div>
                                <p class="text-[10px] font-black text-gray-800 uppercase tracking-widest leading-none mb-1">Complaint Lodged</p>
                                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter">{{ $complaint->created_at->format('F d, Y | h:i A') }}</p>
                            </div>
                        </div>
                        @if($complaint->resolved_at)
                        <div class="relative flex items-center gap-4">
                            <div class="absolute -left-[1.35rem] w-4 h-4 rounded-full bg-[#f3bc3e] border-4 border-white shadow-sm"></div>
                            <div>
                                <p class="text-[10px] font-black text-gray-800 uppercase tracking-widest leading-none mb-1">Resolution Achieved</p>
                                <p class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter">{{ $complaint->resolved_at->format('F d, Y | h:i A') }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Unified Media Section -->
                <div class="pt-4 border-t border-gray-50 space-y-8">
                    <div>
                        <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4 ml-1">Media Library</h4>
                        
                        @if(count($allAudio) > 0)
                            <div class="space-y-3 mb-6">
                                @foreach($allAudio as $audio)
                                    <div class="bg-gray-50 p-4 rounded-2xl flex items-center gap-4 border border-gray-100 group hover:border-[#163a24]/20 transition-all">
                                        <div class="w-10 h-10 rounded-xl bg-[#163a24]/5 flex items-center justify-center text-[#163a24]">
                                            <i class="fas fa-microphone"></i>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-black uppercase tracking-widest text-[#163a24]">{{ $audio['label'] }}</p>
                                            <p class="text-[8px] font-bold text-gray-400 uppercase tracking-tighter mt-0.5">Voice recording attachment</p>
                                        </div>
                                        <div class="ml-auto">
                                            <audio src="{{ asset('storage/' . $audio['path']) }}" 
                                                   x-ref="{{ $audio['type'] === 'initial' ? 'audioPlayer_initial_lib' : 'audioPlayer_lib_' . $audio['id'] }}" 
                                                   class="hidden"></audio>
                                            <button @click="playAudio($refs['{{ $audio['type'] === 'initial' ? 'audioPlayer_initial_lib' : 'audioPlayer_lib_' . $audio['id'] }}'])" 
                                                    class="w-10 h-10 rounded-full bg-white shadow-sm flex items-center justify-center text-[#163a24] hover:bg-[#163a24] hover:text-white transition-all">
                                                <i class="fas" :class="activePlayer === $refs['{{ $audio['type'] === 'initial' ? 'audioPlayer_initial_lib' : 'audioPlayer_lib_' . $audio['id'] }}'] ? 'fa-pause' : 'fa-play'" class="text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(count($allImages) > 0)
                            <div class="grid grid-cols-3 gap-3">
                                @foreach($allImages as $img)
                                    <div class="relative group aspect-square">
                                        <img src="{{ asset('storage/' . $img) }}" 
                                             class="w-full h-full object-cover rounded-2xl border border-gray-100 cursor-zoom-in hover:brightness-90 transition-all shadow-sm"
                                             @click="window.open($el.src)">
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        @if(count($allAudio) === 0 && count($allImages) === 0)
                            <div class="p-8 text-center bg-gray-50 rounded-3xl border border-dashed border-gray-200">
                                <i class="fas fa-photo-video text-gray-300 text-3xl mb-3"></i>
                                <p class="text-[10px] font-black uppercase text-gray-400">No media attachments</p>
                            </div>
                        @endif
                    </div>
                </div>

                @if($complaint->admin_notes)
                <div class="bg-[#163a24] rounded-[2.5rem] p-8 text-white shadow-xl shadow-[#163a24]/10 relative overflow-hidden group">
                    <i class="fas fa-quote-right absolute -right-4 -bottom-4 text-white/5 text-8xl transform -rotate-12 group-hover:rotate-0 transition-transform duration-500"></i>
                    <p class="text-[9px] font-black text-[#f3bc3e] uppercase tracking-[0.2em] mb-4">Official Administrator Notes</p>
                    <p class="text-sm font-medium leading-relaxed italic relative z-10">"{{ $complaint->admin_notes }}"</p>
                </div>
                @endif
            </div>

            <div class="pt-4">
                <button @click="showDetails = false" class="w-full bg-[#163a24] text-[#f3bc3e] py-6 rounded-[2rem] font-black uppercase tracking-[0.2em] shadow-xl hover:brightness-110 active:scale-[0.98] transition-all">
                    Dismiss Details
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function complaintChat() {
    return {
        showDetails: false,
        sending: false,
        newMessage: '',
        
        imagePreviews: [],
        imageFiles: [],
        
        // Initial Complaint Edit State
        editingDescription: false,
        editedDescription: '{{ addslashes($complaint->description) }}',
        editedTitle: '{{ addslashes($complaint->title) }}',
        editedCategory: '{{ $complaint->category }}',
        showAddMenu: false,
        
        originalImages: @json($complaint->extra_images ? array_merge([$complaint->image_path], $complaint->extra_images) : ($complaint->image_path ? [$complaint->image_path] : [])),
        originalAudio: @json($complaint->audio_paths),
        deletedImages: [],
        audioDeleted: false,
        editNewImagesFiles: [],
        editNewImagesPreviews: [],
        editHasAudio: false,

        // Message Reply Edit State
        editingMessageId: null,
        editedMessageContent: '',
        showAddMenuMsg: false,
        originalMsgImages: {}, 
        originalMsgAudio: {},  
        deletedMsgImages: {},  
        msgAudioDeleted: {},   
        editNewMsgImagesFiles: [],
        editNewMsgImagesPreviews: [],
        editMsgHasAudio: false,
        
        // Recording state
        isRecording: false,
        mediaRecorder: null,
        audioChunks: [],
        audioFiles: [],
        audioPreviews: [],
        activePlayer: null,
        
        init() {
            this.scrollToBottom();
            
            // Initialize message originals
            @foreach($complaint->messages as $msg)
                @if(!$msg->is_admin)
                    this.originalMsgImages[{{ $msg->id }}] = @json($msg->images ?? []);
                    this.originalMsgAudio[{{ $msg->id }}] = @json($msg->audio_paths ?? []);
                    this.deletedMsgImages[{{ $msg->id }}] = [];
                    this.msgAudioDeleted[{{ $msg->id }}] = false;
                @endif
            @endforeach

            window.Echo.private('complaint.{{ $complaint->id }}')
                .listen('MessageSent', (e) => {
                    // Always reload if the message belongs to this complaint and we're not sending
                    if (!this.sending) {
                        window.location.reload();
                    }
                });
        },

        handleImageSelect(event) {
            const files = Array.from(event.target.files);
            files.forEach(file => {
                this.imageFiles.push(file);
                const reader = new FileReader();
                reader.onload = (e) => this.imagePreviews.push(e.target.result);
                reader.readAsDataURL(file);
            });
        },

        removeImage(index) {
            this.imageFiles.splice(index, 1);
            this.imagePreviews.splice(index, 1);
        },

        startEditingComplaint() {
            this.editingDescription = true;
            this.editingMessageId = null;
            this.editNewImagesFiles = [];
            this.editNewImagesPreviews = [];
            this.editHasAudio = false;
            this.deletedImages = [];
            this.audioDeleted = false;
        },

        startEditingMessage(msgId, content) {
            this.editingMessageId = msgId;
            this.editingDescription = false;
            this.editedMessageContent = content;
            this.editNewMsgImagesFiles = [];
            this.editNewMsgImagesPreviews = [];
            this.editMsgHasAudio = false;
            if (!this.deletedMsgImages[msgId]) this.deletedMsgImages[msgId] = [];
            if (this.msgAudioDeleted[msgId] === undefined) this.msgAudioDeleted[msgId] = false;
        },

        handleEditImageSelect(event) {
            const files = Array.from(event.target.files);
            files.forEach(file => {
                this.editNewImagesFiles.push(file);
                const reader = new FileReader();
                reader.onload = (e) => this.editNewImagesPreviews.push(e.target.result);
                reader.readAsDataURL(file);
            });
        },

        removeNewEditImage(index) {
            this.editNewImagesFiles.splice(index, 1);
            this.editNewImagesPreviews.splice(index, 1);
        },

        handleMsgEditImageSelect(event, msgId) {
            const files = Array.from(event.target.files);
            files.forEach(file => {
                this.editNewMsgImagesFiles.push(file);
                const reader = new FileReader();
                reader.onload = (e) => this.editNewImagesPreviews.push(e.target.result);
                reader.readAsDataURL(file);
            });
        },

        removeNewMsgEditImage(index) {
            this.editNewMsgImagesFiles.splice(index, 1);
            this.editNewMsgImagesPreviews.splice(index, 1);
        },

        markMsgImageDeleted(msgId, imgPath) {
            if (!this.deletedMsgImages[msgId]) this.deletedMsgImages[msgId] = [];
            this.deletedMsgImages[msgId].push(imgPath);
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

                    if (this.editingDescription) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        this.$refs.editAudioInput.files = dataTransfer.files;
                        this.editHasAudio = true;
                    } else if (this.editingMessageId) {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(file);
                        const ref = this.$refs['editMsgAudioInput_' + this.editingMessageId];
                        if (ref) ref.files = dataTransfer.files;
                        this.editMsgHasAudio = true;
                    } else {
                        const audioObj = new Audio(audioUrl);
                        audioObj.onended = () => { if(this.activePlayer === audioObj) this.activePlayer = null; };
                        this.audioFiles.push(file);
                        this.audioPreviews.push({ url: audioUrl, audio: audioObj });
                    }
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
                this.imageFiles.forEach(file => {
                    formData.append('images[]', file);
                });
                
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
                   // If it saved successfully but failed to return JSON (e.g. 500 but message created)
                   if (response.ok) {
                        window.location.reload();
                        return;
                   }
                   throw new Error("Server returned an invalid response");
                }

                if (response.ok) {
                    // Reset UI
                    this.newMessage = '';
                    this.imageFiles = [];
                    this.imagePreviews = [];
                    this.clearAllAudio();

                    // Force refresh to show new message immediately
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
        
        async saveDescription() {
            if (this.sending) return;
            this.sending = true;
            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('description', this.editedDescription);
                formData.append('title', this.editedTitle);
                formData.append('category', this.editedCategory);
                formData.append('deleted_images', JSON.stringify(this.deletedImages));
                if (this.audioDeleted) formData.append('delete_audio', '1');

                this.editNewImagesFiles.forEach(file => {
                    formData.append('images[]', file);
                });
                
                if (this.$refs.editAudioInput && this.$refs.editAudioInput.files.length > 0) {
                    formData.append('audio', this.$refs.editAudioInput.files[0]);
                }

                const response = await fetch('{{ route('user.complaints.update', $complaint) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                });
                
                const data = await response.json();
                if (response.ok) {
                    window.location.reload();
                } else {
                    console.error("Server error:", data);
                    alert(data.error || data.message || 'Failed to update');
                }
            } catch (error) {
                console.error("JS error:", error);
                alert('An error occurred while saving');
            } finally {
                this.sending = false;
            }
        },

        async saveMessage(msgId) {
            if (this.sending) return;
            this.sending = true;
            try {
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('message', this.editedMessageContent);
                formData.append('deleted_images', JSON.stringify(this.deletedMsgImages[msgId] || []));
                if (this.msgAudioDeleted[msgId]) formData.append('delete_audio', '1');

                this.editNewMsgImagesFiles.forEach(file => {
                    formData.append('images[]', file);
                });
                
                const audioInput = this.$refs['editMsgAudioInput_' + msgId];
                if (audioInput && audioInput.files.length > 0) {
                    formData.append('audio', audioInput.files[0]);
                }

                const response = await fetch(`/complaints/messages/${msgId}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData
                });
                
                const data = await response.json();
                if (response.ok) {
                    window.location.reload();
                } else {
                    console.error("Server error:", data);
                    alert(data.error || data.message || 'Failed to update');
                }
            } catch (error) {
                console.error("JS error:", error);
                alert('An error occurred while saving');
            } finally {
                this.sending = false;
            }
        },
        
        scrollToBottom() {
            setTimeout(() => {
                const container = document.getElementById('chat-container');
                if (container) container.scrollTop = container.scrollHeight;
            }, 100);
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
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(22, 58, 36, 0.05); border-radius: 10px; }

    /* Custom Audio Player Styling */
    .custom-audio-player {
        height: 32px;
        filter: invert(1) hue-rotate(180deg) brightness(1.5);
        opacity: 0.8;
    }
    .bg-white .custom-audio-player {
        filter: none;
        opacity: 1;
    }
</style>
@endsection
