@foreach($messages as $msg)
    @if($msg->is_admin)
        <div class="flex justify-end ml-auto max-w-[85%] lg:max-w-[70%]">
            <div class="bg-[#00a651] text-white p-5 lg:p-7 rounded-[2rem] rounded-tr-none shadow-lg shadow-green-900/10">
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
