@extends("layouts.app")

@section("content")
<main class="max-w-6xl mx-auto pb-20">
    <!-- Header Section -->
    <div class="mb-10 max-w-4xl">
        <h1 class="text-4xl font-black tracking-tighter text-[#163a24] mb-2 uppercase">Create New Poll</h1>
        <p class="text-lg text-gray-500 font-bold leading-relaxed max-w-2xl">
            Design and launch community polls to gather student feedback on campus initiatives.
        </p>
    </div>

    <form action="{{ route('admin.polls.store') }}" method="POST">
        @csrf
        <div class="flex flex-col lg:flex-row gap-10">
            <!-- Form Canvas -->
            <div class="flex-1 bg-white p-10 rounded-[2.5rem] shadow-xl border border-[#163a24]/5">
                <div class="space-y-10">
                    <!-- Title & Objective -->
                    <section class="space-y-8">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-[#163a24]/60 ml-1">Poll Title</label>
                            <input name="title" value="{{ old('title') }}" required
                                   class="w-full px-0 py-4 border-b-2 border-gray-100 bg-transparent focus:border-[#f3bc3e] focus:ring-0 text-2xl font-black text-[#163a24] transition-all placeholder-gray-200" 
                                   placeholder="e.g., Campus Library Hours Extension" type="text"/>
                            @error('title') <p class="text-red-500 text-[10px] font-black mt-2 uppercase">{{ $message }}</p> @enderror
                        </div>
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-[#163a24]/60 ml-1">Description/Objective</label>
                            <textarea name="description" required rows="4"
                                      class="w-full p-6 rounded-3xl bg-[#fef9e1] border-none focus:ring-2 focus:ring-[#f3bc3e] font-bold text-[#163a24] leading-relaxed placeholder-[#163a24]/20 resize-none" 
                                      placeholder="Explain the purpose of this poll and how the results will be used...">{{ old('description') }}</textarea>
                            @error('description') <p class="text-red-500 text-[10px] font-black mt-2 uppercase">{{ $message }}</p> @enderror
                        </div>
                    </section>

                    <!-- Dynamic Poll Options -->
                    <section x-data="{ options: {{ count(old('options', ['', ''])) }} }" class="space-y-6">
                        <div class="flex justify-between items-center mb-4">
                            <label class="block text-[10px] font-black uppercase tracking-[0.2em] text-[#163a24]/60 ml-1">Poll Options</label>
                            <span class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Min. 2 options required</span>
                        </div>
                        
                        <div class="space-y-4">
                            <template x-for="i in options" :key="i">
                                <div class="flex items-center gap-4 group">
                                    <span class="text-[10px] font-black text-[#163a24]/20 w-6" x-text="String(i).padStart(2, '0')"></span>
                                    <input name="options[]" required
                                           class="flex-1 px-6 py-4 rounded-2xl bg-[#fef9e1] border-none focus:ring-2 focus:ring-[#f3bc3e] font-black text-[#163a24] placeholder-[#163a24]/20" 
                                           placeholder="Enter option text..." type="text"/>
                                    <button type="button" @click="if(options > 2) options--" 
                                            class="p-3 text-red-300 hover:text-red-500 transition-colors opacity-0 group-hover:opacity-100">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <button type="button" @click="if(options < 10) options++"
                                class="flex items-center gap-2 text-xs font-black text-[#163a24] hover:text-[#f3bc3e] mt-6 uppercase tracking-widest transition">
                            <i class="fas fa-plus-circle"></i>
                            Add Another Option
                        </button>
                        @error('options') <p class="text-red-500 text-[10px] font-black mt-2 uppercase">{{ $message }}</p> @enderror
                    </section>
                </div>
            </div>

            <!-- Settings Sidebar -->
            <div class="w-full lg:w-80 space-y-8">
                <div class="bg-[#163a24] p-8 rounded-[2.5rem] space-y-8 shadow-2xl">
                    <h3 class="text-xs font-black tracking-[0.2em] text-[#f3bc3e] uppercase">Poll Settings</h3>
                    
                    <div class="space-y-6">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase text-white/40 tracking-widest ml-1">Expiration Date</label>
                            <input name="expires_at" value="{{ old('expires_at') }}" required
                                   class="w-full px-6 py-4 bg-white/5 border-none rounded-2xl text-sm font-bold text-white focus:ring-2 focus:ring-[#f3bc3e]" 
                                   type="date"/>
                            @error('expires_at') <p class="text-red-400 text-[10px] font-black mt-2 uppercase">{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase text-white/40 tracking-widest ml-1">Target Audience</label>
                            <select class="w-full px-6 py-4 bg-white/5 border-none rounded-2xl text-sm font-bold text-white focus:ring-2 focus:ring-[#f3bc3e] appearance-none cursor-pointer">
                                <option class="text-gray-900">All Students</option>
                                <option class="text-gray-900">Faculty Only</option>
                                <option class="text-gray-900">IT Department</option>
                            </select>
                        </div>
                    </div>

                    <div class="pt-8 border-t border-white/5 space-y-4">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase text-white/60 tracking-widest">Anonymous Voting</span>
                            <div class="w-10 h-5 bg-[#22c55e] rounded-full p-1 cursor-pointer">
                                <div class="w-3 h-3 bg-white rounded-full ml-auto shadow-sm"></div>
                            </div>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black uppercase text-white/60 tracking-widest">Live Results</span>
                            <div class="w-10 h-5 bg-white/10 rounded-full p-1 cursor-pointer">
                                <div class="w-3 h-3 bg-white rounded-full shadow-sm"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Notice -->
                <div class="bg-[#f3bc3e] p-8 rounded-[2.5rem] relative overflow-hidden group">
                    <div class="absolute -right-4 -bottom-4 opacity-10 transform group-hover:scale-110 transition-transform">
                        <i class="fas fa-poll text-8xl"></i>
                    </div>
                    <p class="text-[10px] font-black uppercase tracking-widest mb-4 text-[#163a24]/60">System Preview</p>
                    <p class="text-sm font-black text-[#163a24] leading-tight mb-4">Your poll will be pushed to all active student portals upon launch.</p>
                    <div class="w-full bg-[#163a24]/10 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-[#163a24] h-full w-2/3"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Global Actions -->
        <div class="mt-12 flex flex-col sm:flex-row items-center gap-6 border-t border-gray-100 pt-10">
            <button type="submit" 
                    class="w-full sm:w-auto px-12 py-5 bg-[#163a24] text-[#f3bc3e] rounded-2xl font-black uppercase tracking-widest flex items-center justify-center gap-3 shadow-2xl hover:bg-[#1a442a] hover:-translate-y-1 transition-all active:scale-95">
                <i class="fas fa-rocket"></i>
                Launch Poll
            </button>
            <a href="{{ route('admin.polls.index') }}" 
               class="text-sm font-black text-gray-400 hover:text-red-500 uppercase tracking-widest transition">
                Cancel Creation
            </a>
        </div>
    </form>
</main>
@endsection
