@extends('layouts.app')

@section('content')
<div class="fixed inset-0 bg-[#d1d5db] overflow-hidden flex items-center justify-center">
    <!-- Background Blobs -->
    <div class="absolute top-[10%] left-[5%] w-96 h-96 bg-blue-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
    <div class="absolute bottom-[10%] right-[5%] w-96 h-96 bg-purple-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
    
    <div class="relative max-w-md w-full mx-4">
        <div class="bg-[#00a651] rounded-2xl shadow-2xl p-8 pt-10 text-center">
            <!-- Top Icon -->
            <div class="absolute -top-12 left-1/2 transform -translate-x-1/2">
                <div class="bg-[#008d44] w-20 h-20 rounded-full flex items-center justify-center border-4 border-[#00a651] shadow-lg">
                    <i class="fas fa-sign-in-alt text-white text-3xl"></i>
                </div>
            </div>

            <div class="mt-4 mb-8">
                <h2 class="text-3xl font-bold text-white mb-2">Welcome Back</h2>
                <p class="text-white opacity-80 text-sm">Please enter your credentials to continue</p>
            </div>
            
            <form method="POST" action="{{ route('login') }}" class="text-left">
                @csrf
                
                <div class="mb-5">
                    <label class="block text-white text-sm font-semibold mb-2 ml-1">ID</label>
                    <input type="text" 
                           name="id_number" 
                           value="{{ old('id_number') }}"
                           placeholder="Enter your ID"
                           class="w-full px-4 py-3 bg-[#c2c2c2] border-none rounded-lg focus:ring-2 focus:ring-white/50 text-gray-800 placeholder-gray-500 @error('id_number') ring-2 ring-red-500 @enderror"
                           required>
                    @error('id_number')
                        <p class="mt-1 text-white text-xs font-semibold">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-8">
                    <label class="block text-white text-sm font-semibold mb-2 ml-1">Password</label>
                    <input type="password" 
                           name="password" 
                           placeholder="Enter your password"
                           class="w-full px-4 py-3 bg-[#c2c2c2] border-none rounded-lg focus:ring-2 focus:ring-white/50 text-gray-800 placeholder-gray-500 @error('password') ring-2 ring-red-500 @enderror"
                           required>
                    @error('password')
                        <p class="mt-1 text-white text-xs font-semibold">{{ $message }}</p>
                    @enderror
                </div>
                
                <button type="submit" 
                        class="w-full bg-[#006633] text-white py-3.5 rounded-lg font-bold hover:bg-[#004d26] transition duration-200 shadow-lg">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    /* Ensure the content is centered and layout elements from layouts.app don't interfere */
    main { padding: 0 !important; }
    .lg\:ml-64 { margin-left: 0 !important; }
</style>
@endsection
