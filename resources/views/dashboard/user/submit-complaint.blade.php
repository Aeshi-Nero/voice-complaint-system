@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Submit Complaint</h2>
        <p class="text-gray-500 font-medium">Please provide detailed information about your concern.</p>
    </div>

    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8 sm:p-10 relative overflow-hidden">
        <!-- Top accent -->
        <div class="absolute top-0 left-0 right-0 h-2 bg-[#00a651]"></div>

        <div class="mb-8 p-4 bg-blue-50 border border-blue-100 rounded-2xl flex items-center gap-4">
            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <p class="text-sm font-bold text-blue-900 leading-tight">Submission Limit</p>
                <p class="text-xs text-blue-700 mt-0.5">
                    You have <span class="font-black">{{ auth()->user()->getRemainingComplaints() }}</span>/6 submissions left for today.
                </p>
            </div>
        </div>
        
        <form method="POST" action="{{ route('user.complaints.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Category *</label>
                    <div class="relative">
                        <select name="category" class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#00a651] focus:bg-white transition appearance-none font-semibold text-gray-700" required>
                            <option value="">Select Category</option>
                            <option value="Academic">Academic</option>
                            <option value="Faculty">Faculty</option>
                            <option value="Administrative">Administrative</option>
                            <option value="IT/Technical">IT/Technical</option>
                            <option value="Health & Safety">Health & Safety</option>
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    @error('category') <p class="text-red-500 text-[10px] font-bold mt-1.5 ml-1">{{ $message }}</p> @enderror
                </div>
                
                <div>
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Priority *</label>
                    <div class="relative">
                        <select name="priority" class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#00a651] focus:bg-white transition appearance-none font-semibold text-gray-700" required>
                            <option value="">Select Priority</option>
                            <option value="Low">Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                        </select>
                        <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                    @error('priority') <p class="text-red-500 text-[10px] font-bold mt-1.5 ml-1">{{ $message }}</p> @enderror
                </div>
            </div>
            
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Title *</label>
                <input type="text" name="title" value="{{ old('title') }}" 
                       placeholder="Brief summary of your complaint"
                       class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#00a651] focus:bg-white transition font-semibold text-gray-700"
                       required>
                @error('title') <p class="text-red-500 text-[10px] font-bold mt-1.5 ml-1">{{ $message }}</p> @enderror
            </div>
            
            <div>
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Description *</label>
                <textarea name="description" rows="6" 
                          placeholder="Provide all relevant details here..."
                          class="w-full px-4 py-3.5 bg-gray-50 border border-gray-100 rounded-xl focus:ring-2 focus:ring-[#00a651] focus:bg-white transition font-semibold text-gray-700"
                          required>{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-[10px] font-bold mt-1.5 ml-1">{{ $message }}</p> @enderror
                @error('profanity') <p class="text-red-500 text-[10px] font-bold mt-1.5 ml-1 bg-red-50 p-2 rounded-lg border border-red-100">{{ $message }}</p> @enderror
            </div>
            
            <div class="p-6 bg-gray-50 rounded-2xl border border-dashed border-gray-200">
                <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-3 ml-1">Attachment (Optional)</label>
                <div class="flex items-center justify-center w-full">
                    <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-200 border-dashed rounded-xl cursor-pointer bg-white hover:bg-gray-50 transition">
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            <i class="fas fa-cloud-upload-alt text-2xl text-gray-300 mb-2"></i>
                            <p class="text-xs text-gray-500 font-bold uppercase tracking-tight">Upload Image</p>
                            <p class="text-[10px] text-gray-400 mt-1">JPG, PNG (Max 5MB)</p>
                        </div>
                        <input type="file" name="image" accept="image/jpeg,image/png" class="hidden" />
                    </label>
                </div>
                @error('image') <p class="text-red-500 text-[10px] font-bold mt-1.5 ml-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="flex flex-col sm:flex-row gap-4 pt-4">
                <button type="submit" class="flex-1 px-8 py-4 bg-[#00a651] text-white rounded-xl font-bold shadow-lg hover:bg-[#008d44] transition flex items-center justify-center gap-2">
                    <i class="fas fa-paper-plane"></i> Submit Complaint
                </button>
                <a href="{{ route('user.dashboard') }}" class="px-8 py-4 bg-gray-100 text-gray-500 rounded-xl font-bold hover:bg-gray-200 transition text-center">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
