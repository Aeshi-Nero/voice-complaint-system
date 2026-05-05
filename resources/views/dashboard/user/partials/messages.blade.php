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
