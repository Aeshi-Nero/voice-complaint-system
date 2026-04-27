@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto pb-20">
    <!-- Page Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start gap-6 mb-8 lg:mb-10">
        <div class="max-w-2xl">
            <h2 class="text-2xl lg:text-4xl font-black text-[#163a24] tracking-tight mb-2 uppercase">Submit New Complaint</h2>
            <p class="text-gray-500 font-bold text-xs lg:text-sm leading-relaxed">Your voice helps us improve. Please provide clear details so we can address your concerns effectively.</p>
        </div>
        
        <!-- Submissions Tag -->
        <div class="bg-[#f3bc3e] text-[#163a24] px-5 lg:px-6 py-2.5 lg:py-3 rounded-xl lg:rounded-2xl flex items-center gap-3 shadow-sm border-b-4 border-[#ca8a04] w-full lg:w-auto justify-center">
            <i class="fas fa-clipboard-check"></i>
            <span class="font-black text-[10px] lg:text-xs uppercase tracking-widest">{{ auth()->user()?->getRemainingComplaints() }}/6 submissions left</span>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white rounded-3xl lg:rounded-[2.5rem] shadow-xl p-6 lg:p-16 border border-[#163a24]/5">
        @if(auth()->user()->banned_until && auth()->user()->banned_until->isFuture())
            <div class="mb-8 lg:mb-12 p-6 lg:p-10 bg-red-50 border-2 lg:border-4 border-red-100 rounded-2xl lg:rounded-[2.5rem] flex flex-col lg:flex-row items-center gap-6 lg:gap-8 shadow-sm text-center lg:text-left">
                <div class="w-12 h-12 lg:w-16 lg:h-16 bg-red-600 rounded-2xl lg:rounded-3xl flex items-center justify-center text-white text-xl lg:text-2xl shadow-lg shadow-red-200 shrink-0">
                    <i class="fas fa-ban"></i>
                </div>
                <div>
                    <h3 class="text-lg lg:text-xl font-black text-red-900 uppercase tracking-tight mb-1 lg:mb-2">Access Restricted</h3>
                    <p class="text-red-700 font-bold text-xs lg:text-sm leading-relaxed">Your account is temporarily banned until <span class="underline decoration-2">{{ auth()->user()->banned_until->format('M d, Y | h:i A') }}</span>.</p>
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('user.complaints.store') }}" enctype="multipart/form-data" class="space-y-8 lg:space-y-12 {{ (auth()->user()->banned_until && auth()->user()->banned_until->isFuture()) ? 'opacity-50 pointer-events-none' : '' }}">
            @csrf
            
            <div class="grid grid-cols-1 gap-8 lg:gap-10">
                <!-- Category -->
                <div>
                    <label class="block text-[9px] lg:text-[10px] font-black text-[#163a24] uppercase tracking-[0.2em] mb-3 lg:mb-4 ml-1">Category</label>
                    <div class="relative">
                        <select name="category" class="w-full px-5 lg:px-6 py-4 lg:py-5 bg-[#fef9e1] border-none rounded-xl lg:rounded-2xl appearance-none font-bold text-[#163a24] outline-none focus:ring-2 focus:ring-[#f3bc3e] transition cursor-pointer text-sm lg:text-base" required>
                            <option value="">Select Category</option>
                            <option value="Academic" {{ old('category') == 'Academic' ? 'selected' : '' }}>Academic</option>
                            <option value="Faculty" {{ old('category') == 'Faculty' ? 'selected' : '' }}>Faculty</option>
                            <option value="Administrative" {{ old('category') == 'Administrative' ? 'selected' : '' }}>Administrative</option>
                            <option value="IT/Technical" {{ old('category') == 'IT/Technical' ? 'selected' : '' }}>IT/Technical</option>
                            <option value="Health & Safety" {{ old('category') == 'Health & Safety' ? 'selected' : '' }}>Health & Safety</option>
                        </select>
                        <div class="absolute inset-y-0 right-5 lg:right-6 flex items-center pointer-events-none text-[#163a24]/30">
                            <i class="fas fa-wave-square text-xs"></i>
                        </div>
                    </div>
                    @error('category') <p class="text-red-500 text-[9px] lg:text-[10px] font-black mt-2 lg:mt-3 ml-1 uppercase">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <!-- Title -->
            <div x-data="{ count: {{ strlen(old('title', '')) }} }">
                <div class="flex justify-between items-center mb-3 lg:mb-4 px-1">
                    <label class="block text-[9px] lg:text-[10px] font-black text-[#163a24] uppercase tracking-[0.2em]">Complaint Title</label>
                    <span class="text-[9px] lg:text-[10px] font-bold text-gray-300 tracking-widest"><span x-text="count">0</span> / 200</span>
                </div>
                <input type="text" name="title" value="{{ old('title') }}" 
                       @input="count = $event.target.value.length"
                       maxlength="200"
                       placeholder="Brief summary..."
                       class="w-full px-5 lg:px-8 py-4 lg:py-5 bg-[#fef9e1] border-none rounded-xl lg:rounded-2xl font-bold text-[#163a24] outline-none focus:ring-2 focus:ring-[#f3bc3e] transition placeholder-[#163a24]/20 text-sm lg:text-base"
                       required>
                @error('title') <p class="text-red-500 text-[9px] lg:text-[10px] font-black mt-2 lg:mt-3 ml-1 uppercase">{{ $message }}</p> @enderror
            </div>
            
            <!-- Description -->
            <div x-data="{ count: {{ strlen(old('description', '')) }} }">
                <div class="flex justify-between items-center mb-3 lg:mb-4 px-1">
                    <label class="block text-[9px] lg:text-[10px] font-black text-[#163a24] uppercase tracking-[0.2em]">Detailed Description</label>
                    <span class="text-[9px] lg:text-[10px] font-bold text-gray-300 tracking-widest"><span x-text="count">0</span> / 2000</span>
                </div>
                <textarea name="description" rows="6" 
                          @input="count = $event.target.value.length"
                          maxlength="2000"
                          placeholder="Provide context..."
                          class="w-full px-5 lg:px-8 py-4 lg:py-6 bg-[#fef9e1] border-none rounded-2xl lg:rounded-3xl font-bold text-[#163a24] outline-none focus:ring-2 focus:ring-[#f3bc3e] transition placeholder-[#163a24]/20 resize-none text-sm lg:text-base"
                          required>{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-[9px] lg:text-[10px] font-black mt-2 lg:mt-3 ml-1 uppercase">{{ $message }}</p> @enderror
                @error('profanity') <p class="text-red-500 text-[9px] lg:text-[10px] font-black mt-4 ml-1 bg-red-50 p-4 lg:p-6 rounded-2xl lg:rounded-3xl border-2 border-red-100 uppercase tracking-tight">{{ $message }}</p> @enderror
            </div>
            
            <!-- Attachment -->
            <div>
                <label class="block text-[9px] lg:text-[10px] font-black text-[#163a24] uppercase tracking-[0.2em] mb-4 lg:mb-6 ml-1">Evidence (Optional)</label>
                <div class="flex flex-col gap-6">
                    <label class="flex flex-col items-center justify-center w-full h-40 lg:h-48 border-4 border-dashed border-gray-100 rounded-2xl lg:rounded-[2rem] cursor-pointer bg-white hover:bg-gray-50 hover:border-[#f3bc3e]/30 transition-all group">
                        <div class="flex flex-col items-center justify-center px-4 text-center">
                            <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gray-50 rounded-xl lg:rounded-2xl flex items-center justify-center text-gray-300 group-hover:text-[#f3bc3e] transition-colors mb-3 lg:mb-4">
                                <i class="fas fa-cloud-upload-alt text-lg lg:text-xl"></i>
                            </div>
                            <p class="text-xs lg:text-sm font-black text-[#163a24] mb-1">Click to upload</p>
                            <p class="text-[8px] lg:text-[10px] text-gray-400 font-bold uppercase tracking-tight">JPG, PNG or PDF (Max. 10MB)</p>
                        </div>
                        <input type="file" name="image" accept="image/jpeg,image/png,application/pdf" class="hidden" />
                    </label>
                </div>
                @error('image') <p class="text-red-500 text-[10px] font-black mt-3 ml-1 uppercase">{{ $message }}</p> @enderror
            </div>
            
            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-end gap-10 pt-10 border-t border-gray-100">
                <a href="{{ route('user.dashboard') }}" class="text-[10px] font-black text-[#163a24] hover:text-red-500 uppercase tracking-[0.2em] transition">
                    Cancel
                </a>
                <button type="submit" class="w-full sm:w-auto px-12 py-5 bg-[#163a24] text-white rounded-2xl font-black uppercase tracking-widest shadow-2xl hover:bg-[#1a442a] transition-all flex items-center justify-center gap-4 group">
                    Submit Complaint <i class="fas fa-paper-plane text-xs group-hover:translate-x-1 transition-transform"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Confidentiality Notice -->
    <div class="mt-12 bg-[#22c55e]/5 rounded-3xl p-10 border border-[#22c55e]/10 flex items-start gap-8">
        <div class="w-12 h-12 bg-[#163a24] rounded-xl flex items-center justify-center text-white shrink-0">
            <i class="fas fa-info"></i>
        </div>
        <div>
            <h4 class="text-sm font-black text-[#163a24] uppercase tracking-widest mb-2">Confidentiality Notice</h4>
            <p class="text-xs font-bold text-[#163a24]/60 leading-relaxed">
                Your identity is protected by the University Privacy Policy. Complaints are routed to the appropriate department for impartial review. Response times typically vary between 2-5 business days depending on the priority level.
            </p>
        </div>
    </div>
</div>
@endsection
