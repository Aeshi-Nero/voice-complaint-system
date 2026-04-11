@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-b from-[#fef9e1] to-[#f2e19d] flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden -m-4 lg:-m-8">
    
    <!-- Top Logo Section -->
    <div class="mb-8 text-center">
        <div class="bg-[#163a24] w-16 h-16 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-4">
            <i class="fas fa-graduation-cap text-white text-3xl"></i>
        </div>
        <h1 class="text-2xl font-black text-[#163a24] tracking-tight uppercase">Aldersgate College</h1>
        <p class="text-[10px] font-bold text-[#163a24] tracking-[0.4em] uppercase opacity-80 mt-1">University Portal</p>
    </div>

    <div class="relative max-w-md w-full">
        <div class="bg-[#163a24] rounded-[2.5rem] shadow-2xl p-10 sm:p-12 text-left relative">
            <div class="mb-10">
                <h2 class="text-3xl font-bold text-white mb-2">Welcome Back</h2>
                <p class="text-[#9db6a1] text-sm">Please enter your credentials to continue</p>
            </div>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6">
                @csrf
                
                <div>
                    <label class="block text-[#9db6a1] text-[10px] font-bold uppercase tracking-widest mb-2 ml-1">ID Number</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-5 flex items-center text-[#163a24]/40">
                            <i class="fas fa-id-card-alt"></i>
                        </span>
                        <input type="text" 
                               name="id_number" 
                               value="{{ old('id_number') }}"
                               placeholder="Enter your ID"
                               class="w-full pl-12 pr-6 py-4 bg-white rounded-2xl text-[#163a24] placeholder-gray-400 font-bold outline-none border-none transition duration-300 @error('id_number') ring-2 ring-red-500 @enderror"
                               required>
                    </div>
                    @error('id_number')
                        <p class="mt-2 text-red-400 text-[10px] font-bold uppercase ml-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-[#9db6a1] text-[10px] font-bold uppercase tracking-widest mb-2 ml-1">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-5 flex items-center text-[#163a24]/40">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input type="password" 
                               name="password" 
                               placeholder="••••••••"
                               class="w-full pl-12 pr-6 py-4 bg-white rounded-2xl text-[#163a24] placeholder-gray-400 font-bold outline-none border-none transition duration-300 @error('password') ring-2 ring-red-500 @enderror"
                               required>
                    </div>
                    @error('password')
                        <p class="mt-2 text-red-400 text-[10px] font-bold uppercase ml-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between py-2">
                    <div class="flex items-center">
                        <input id="remember_me" name="remember" type="checkbox" class="h-4 w-4 text-[#f3bc3e] focus:ring-[#f3bc3e] border-white/20 rounded bg-white/10">
                        <label for="remember_me" class="ml-2 block text-xs text-[#9db6a1] font-bold">
                            Remember me
                        </label>
                    </div>
                    <div class="text-xs">
                        <a href="#" class="font-bold text-[#f3bc3e] hover:text-yellow-300">
                            Forgot password?
                        </a>
                    </div>
                </div>
                
                <button type="submit" 
                        class="w-full bg-[#f3bc3e] text-[#163a24] py-4 rounded-2xl font-black uppercase tracking-widest hover:bg-yellow-400 transition duration-300 flex items-center justify-center gap-2">
                    Sign In <i class="fas fa-arrow-right text-sm"></i>
                </button>
            </form>

            <!-- Bottom Support Section -->
            <div class="mt-12 pt-8 border-t border-white/10 text-center">
                <p class="text-[#9db6a1] text-xs mb-2">Need help accessing your account?</p>
                <a href="#" class="text-white font-bold hover:underline transition">Contact IT Support</a>
            </div>
        </div>
        
        <!-- Bottom Footer Info -->
        <div class="text-center mt-12">
            <p class="text-[#163a24]/40 text-[10px] font-black uppercase tracking-[0.3em]">
                V.O.I.C.E. &bull; V2.4.0 &bull; Academic Excellence
            </p>
        </div>
    </div>
</div>

<style>
    /* Reset layout interference from app.blade.php */
    main { padding: 0 !important; }
    header { display: none !important; }
    .lg\:ml-64 { margin-left: 0 !important; }
    aside { display: none !important; }
</style>
@endsection
