@foreach($messages as $msg)
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
                    </div>

                    <!-- Existing Images -->
                    <template x-if="originalMsgImages[{{ $msg->id }}] && originalMsgImages[{{ $msg->id }}].length > 0">
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="(img, index) in originalMsgImages[{{ $msg->id }}]" :key="index">
                                <div x-show="!(deletedMsgImages[{{ $msg->id }}] || []).includes(img)" class="relative group aspect-square">
                                    <img :src="'/storage/' + img" class="w-full h-full object-cover rounded-xl border border-white/10">
                                    <button type="button" @click="markMsgImageDeleted({{ $msg->id }}, img)" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-lg flex items-center justify-center shadow-lg hover:bg-red-600 transition">
                                        <i class="fas fa-times text-[8px]"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- Existing Audio -->
                    <template x-if="originalMsgAudio[{{ $msg->id }}] && originalMsgAudio[{{ $msg->id }}].length > 0 && !msgAudioDeleted[{{ $msg->id }}]">
                        <div class="bg-white/10 p-3 rounded-xl flex items-center gap-3 border border-white/5">
                            <i class="fas fa-microphone text-[#f3bc3e]"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest">Original Audio</span>
                            <button type="button" @click="msgAudioDeleted[{{ $msg->id }}] = true" class="ml-auto w-6 h-6 bg-red-500 text-white rounded-lg flex items-center justify-center hover:bg-red-600 transition">
                                <i class="fas fa-times text-[8px]"></i>
                            </button>
                        </div>
                    </template>

                    <!-- New Images Preview -->
                    <template x-if="editNewMsgImagesPreviews.length > 0">
                        <div class="grid grid-cols-3 gap-2">
                            <template x-for="(preview, index) in editNewMsgImagesPreviews" :key="'new_'+index">
                                <div class="relative group aspect-square">
                                    <img :src="preview" class="w-full h-full object-cover rounded-xl border-2 border-accent/50">
                                    <button type="button" @click="removeNewMsgEditImage(index)" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-red-500 text-white rounded-lg flex items-center justify-center shadow-lg hover:bg-red-600 transition">
                                        <i class="fas fa-times text-[8px]"></i>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- New Audio Indicator -->
                    <template x-if="editMsgHasAudio">
                        <div class="bg-accent/20 p-3 rounded-xl flex items-center gap-3 border border-accent/30">
                            <i class="fas fa-microphone text-accent"></i>
                            <span class="text-[10px] font-black uppercase tracking-widest text-accent">New Recording</span>
                            <button type="button" @click="cancelMsgEditAudio({{ $msg->id }})" class="ml-auto w-6 h-6 bg-red-500 text-white rounded-lg flex items-center justify-center hover:bg-red-600 transition">
                                <i class="fas fa-times text-[8px]"></i>
                            </button>
                        </div>
                    </template>

                    <!-- Action Buttons -->
                    <div class="flex items-center gap-2 pt-2">
                        <button type="button" @click="$refs['editMsgImageInput_{{ $msg->id }}'].click()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition text-white/80 hover:text-white" title="Add images">
                            <i class="fas fa-image text-xs"></i>
                        </button>
                        <button type="button" @click="toggleRecording()" class="w-9 h-9 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition text-white/80 hover:text-white" title="Record audio">
                            <i class="fas fa-microphone text-xs"></i>
                        </button>
                        <button type="button" @click="saveMessage({{ $msg->id }})" class="ml-auto text-[10px] font-black uppercase tracking-widest bg-white text-[#00a651] px-5 py-2.5 rounded-xl hover:bg-white/90 transition shadow-lg">
                            Save
                        </button>
                        <button type="button" @click="cancelMessageEdit()" class="text-[10px] font-black uppercase tracking-widest text-white/50 hover:text-white transition px-3 py-2.5">
                            Cancel
                        </button>
                    </div>

                    <!-- Hidden Inputs -->
                    <input type="file" x-ref="editMsgImageInput_{{ $msg->id }}" class="hidden" accept="image/*" multiple @change="handleMsgEditImageSelect($event, {{ $msg->id }})">
                    <input type="file" x-ref="editMsgAudioInput_{{ $msg->id }}" class="hidden" accept="audio/*">
                </div>

                <div class="flex items-center justify-end mt-3 gap-3">
                    <p class="text-[9px] font-black text-white/40 uppercase">{{ $msg->created_at->format('h:i A') }}</p>
                    <button @click="startEditingMessage({{ $msg->id }}, '{{ addslashes($msg->message) }}')" 
                            class="text-white/40 hover:text-white transition-colors">
                        <i class="fas fa-pencil-alt text-[10px]"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif
@endforeach
