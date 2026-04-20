@extends('layouts.app')

@section('content')
<!-- Add Google Fonts: Poppins and Inter -->
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@900&family=Inter:wght@400;700;900&display=swap" rel="stylesheet">

<div class="min-h-screen bg-gradient-to-b from-[#fef9e1] to-[#f2e19d] flex flex-col items-center justify-center py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden -m-4 lg:-m-8">
    
    <!-- Top Logo Section -->
    <div class="mb-10 text-center">
        <div class="bg-[#163a24] w-48 h-48 rounded-full flex items-center justify-center shadow-2xl mx-auto mb-8 overflow-hidden border-8 border-white">
            <img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAAEbCAYAAABgArlgAACAAElEQVR42uydd5gkVfX+P7equru6e3py2Jx32dklLLDknAWJigJmBAUzoqKICRUVc0bEiBkRBZScc9xl0/SyOU+Onbur6v7+uHdy90z37Czi78t9nn0WZqerq27d+95z3nPOewRvjAkNISDXZNvAAmA+cBxwPFAL/AvEZ6zGlPvGTL0x3hiTN6w3pqD44TSFQHgRYC7wJuBI4DANUgFAAF3A3W+A1f/hdRK1BRAEkZOekfMtTbwxKW8A1mu6AE1gBnhnAG8FDgLqACPPrz8BPP/GrP3fNsCBT4BcLgx3tRO1dwBRYAPQZzWmnTemaOIT+8YoDFQWsBS4BDgPWAiYY3ykB7jIakw/8Mbs/d8dpgmZtfZpwG1AJSD12tgCrAHuBlYD294ArzcAa7IsqsXAB4ALgakFrKmR4w/AlVZjOvnGLP6fX0MhvR7ekuefs8BubY3/C3gK6LQa0/KNmXsDsIoeblMQKeQU4P3A5cDsIoEKvQAvsBrTL74xk28M04DMOvtkbWXVjPGrCaBJA9dtwFarMf0G//n/E2A5UVsghWEtmTxi24kGLZAnAF8EjgZ8pWAd8D3gujdM/DfGkHVqAz/TB2Axa2ibtsr+qIHLe2MW/8cBK9cUEkJ4RwFhqzH94CSBVQTkFcBngPoJXCIKnGM1pje/saTeGCNAazlwJzCtyI94wHrgR8DfrMZ07xuz+D8KWHJlJa6dXgj8CXhYwrW+xvTEH17lUjUAXwXeA9gTuEwG+DTws/9lDkIIged5AvBr6zKo58MYslYC+mdBVIQ5oP/26c8Ne116btwhnzXzWBWZIX87+u8UkNY/779WFkjqv3OAK4T4XwAsH/AN4OoS6AX08z+o1+aKybS2nKagECDNJak3AGsfv/xq4GYUkXkXKiKXneC1AKYDPwAuYOIpHg8DF1uN6Y7XMRCZGlBMoExzKvX61A/rZ58GTAEagCqgXP/uUMDyDQEnU/+b0H+MAtZCP4gbedab1L/T/7uuBq2s/jN0k6aAXv2nA2gGdmkQc4BWoAVo07/j9F/rvw1sTtRepK2sxaWe0dpN/Bbwx8kK5jhR+yggDqyx9uLA/28M638IrILakjlPL/7ZekN1TXArzwD5M+DsEk++oaMbuFGqDfTfszylZIhlFNFAPBeo1sC0n/6ZX/+sWv/eMKvH9VxyroOUEgk4To6MkyXj5Ehl02SyaTJOjrSTI51Nk8llcD2XrJPrv4fhi8swsUwLT3pkHWcIdg2Cqd/0YRgCvy9AwOfH9tkELAvbZxP02wQsHwGfH7/Bj9B413/dIUA01ALr1qCVRaUSbJBSbkWR21uBnUCn/veMEGLfW8VSbETIn6B4zlKseKHf4/eBA5yoff3eHoymetvLgbcB73OaQlusJck3AGuSwUpooPoIg2R4lbYOuiZwvQjIr+4lWHkogvQJ32t4SkkpdRY1U1DpFrXAwahk1lptPTVoQPINBaNUNk0slaAz3k08m8ZxHTr7utjT087u7lba+jrpjPeS9Vwkkkw2QyqTIpnLkHGy5JwcOdfF8Vw86eFJqcBNylFg1A9IAoEEPQhFCHmMcZpDH2OfmP6f9f/7/sBNDWv7A+2F6v8B69p/hOEPYv7zQdvN79mYmX09867vDNXvN2RzN2P+X9rE8uS3X3p2Z6PqbeDe77Yv+S+D87vtm/7r+vO99mBf/m9f3m9/7p5G+Z8/X3Zf/qX35f8BeR66G2v9718AAAAASUVORK5CYII=" alt="Logo" class="w-full h-full object-cover">
        </div>
        <h1 class="text-9xl font-black text-[#163a24] tracking-tightest uppercase font-['Poppins']">V.O.I.C.E</h1>
        <p class="text-xs font-bold text-[#163a24] tracking-[0.5em] uppercase opacity-90 mt-4 font-['Inter']">Virtual Outlet for Institutional Complaint Engagement</p>
    </div>

    <div class="relative max-w-md w-full">
        <div class="bg-[#163a24] rounded-[2.5rem] shadow-2xl p-10 sm:p-12 text-left relative border-4 border-white/10">
            <div class="mb-10 text-center">
                <h2 class="text-3xl font-bold text-white mb-2">Welcome</h2>
                <p class="text-[#9db6a1] text-sm">Please enter your credentials to continue</p>
            </div>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-6" x-data="{ showPassword: false }">
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
                        <input :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               placeholder="••••••••"
                               class="w-full pl-12 pr-12 py-4 bg-white rounded-2xl text-[#163a24] placeholder-gray-400 font-bold outline-none border-none transition duration-300 @error('password') ring-2 ring-red-500 @enderror"
                               required>
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-5 flex items-center text-[#163a24]/40 hover:text-[#163a24] transition-colors">
                            <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
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
        </div>
        
        <!-- Bottom Footer Info -->
        <div class="text-center mt-12">
            <p class="text-[#163a24] text-[12px] font-black uppercase tracking-[0.2em] opacity-60">
                V.O.I.C.E v.1.4 | Aldersgate College Inc.
            </p>
        </div>
    </div>
</div>

<style>
    /* Reset layout interference from app.blade.php */
    main { padding: 0 !important; margin-left: 0 !important; }
    header { display: none !important; }
    .lg\:ml-64 { margin-left: 0 !important; }
    .lg\:ml-80 { margin-left: 0 !important; }
    aside { display: none !important; }
</style>
@endsection
