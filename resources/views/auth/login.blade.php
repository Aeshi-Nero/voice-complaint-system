@extends('layouts.app')

@section('content')
<!-- Add Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=Poppins:wght@900&display=swap" rel="stylesheet">

<div x-data="loginManager()" class="min-h-screen bg-[#fef9e1] flex flex-col items-center justify-center py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden -m-4 lg:-m-10">
    
    <!-- Top Logo Section -->
    <div class="mb-12 text-center px-4 max-w-2xl">
        <div class="w-28 h-28 sm:w-36 sm:h-36 rounded-full flex items-center justify-center shadow-2xl mx-auto mb-8 overflow-hidden border-4 border-white bg-white p-4">
            @if($logoBase64)
                <img src="data:image/png;base64,{{ $logoBase64 }}" alt="Aldersgate Logo" class="w-full h-full object-contain">
            @else
                <i class="fas fa-university text-[#163a24] text-4xl"></i>
            @endif
        </div>
        
        <div class="space-y-2">
            <h1 class="text-4xl sm:text-6xl font-black text-[#163a24] tracking-[0.5em] uppercase leading-none font-['Poppins'] drop-shadow-md">V.O.I.C.E</h1>
            <div class="flex items-center justify-center gap-6 pt-2">
                <div class="h-0.5 w-16 bg-[#163a24]/10 rounded-full"></div>
                <p class="text-[10px] sm:text-xs font-black text-[#163a24] tracking-[0.25em] uppercase opacity-60 leading-relaxed italic">
                    Virtual Outlet for Institutional Complaint Engagement
                </p>
                <div class="h-0.5 w-16 bg-[#163a24]/10 rounded-full"></div>
            </div>
        </div>
    </div>

    <div class="relative max-w-md w-full">
        <!-- Main Card -->
        <div class="bg-[#163a24] rounded-[4rem] shadow-[0_50px_100px_rgba(22,58,36,0.5)] p-8 sm:p-14 text-left relative border-4 border-white/5">
            
            <div class="mb-12 text-center">
                <h2 class="text-4xl sm:text-5xl font-black text-white tracking-tightest mb-2 italic">Welcome</h2>
                <p class="text-[#9db6a1] text-xs sm:text-sm font-bold uppercase tracking-widest opacity-80">Institutional Access Terminal</p>
            </div>

            <!-- Forgot Password Module (Green Overlay style) -->
            <div x-show="showForgotNotice" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-cloak
                 class="mb-10 overflow-hidden rounded-[2rem] border-2 border-white/20 shadow-2xl">
                <div class="p-6 bg-[#00a651] text-white">
                    <div class="flex items-start gap-4 mb-6">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center text-white shrink-0">
                            <i class="fas" :class="forgotStep === 3 ? 'fa-check-circle' : 'fa-paper-plane'"></i>
                        </div>
                        <p class="text-[10px] font-black uppercase tracking-wider leading-relaxed" x-text="forgotMessage"></p>
                    </div>
                    
                    <!-- Persistent Reset Input -->
                    <div x-show="forgotStep < 3" class="space-y-4">
                        <input type="text" x-model="forgotContact" placeholder="Enter Email or Phone Number..." 
                               class="w-full px-6 py-4 bg-white rounded-2xl text-[#163a24] placeholder-gray-400 font-bold outline-none border-none transition-all text-xs shadow-inner"
                               @keydown.enter="handleReset()">
                        <button @click="handleReset()" :disabled="loading"
                                class="w-full bg-[#163a24] text-white py-4 rounded-xl font-black uppercase tracking-widest text-[10px] shadow-lg hover:bg-[#2d6a3d] transition-all flex items-center justify-center gap-3">
                            <span x-show="!loading">Dispatch New Password</span>
                            <i x-show="loading" class="fas fa-circle-notch fa-spin"></i>
                        </button>
                    </div>
                    
                    <!-- DISPLAY PIN LARGE FOR TESTING -->
                    <div x-show="forgotStep === 3" class="mb-6 bg-white/20 p-6 rounded-2xl border-2 border-dashed border-white/40 text-center animate-pulse">
                         <p class="text-[9px] font-black uppercase tracking-widest mb-1 opacity-70">Generated Password</p>
                         <p class="text-4xl font-black tracking-[0.2em]" x-text="generatedResetPin"></p>
                    </div>

                    <button x-show="forgotStep === 3" @click="showForgotNotice = false" class="w-full bg-white text-[#00a651] py-3 rounded-xl font-black uppercase tracking-widest text-[10px] shadow-lg">
                        Proceed to Login
                    </button>
                </div>
            </div>
            
            <form method="POST" action="{{ route('login') }}" class="space-y-8" x-show="!showForgotNotice">
                @csrf
                
                <div class="space-y-2">
                    <label class="block text-[#9db6a1] text-[10px] font-black uppercase tracking-widest ml-2">Registry ID</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-6 flex items-center text-[#163a24]/30 group-focus-within:text-[#00a651] transition-colors text-xl">
                            <i class="fas fa-id-card-alt"></i>
                        </span>
                        <input type="text" 
                               name="id_number" 
                               value="{{ old('id_number') }}"
                               placeholder="AC-0000-0000"
                               class="w-full pl-16 pr-6 py-5 bg-white rounded-[1.75rem] text-[#163a24] placeholder-gray-300 font-black outline-none border-none focus:ring-4 focus:ring-[#f3bc3e]/20 transition-all shadow-inner"
                               required>
                    </div>
                    @error('id_number')
                        <p class="mt-2 text-red-400 text-[9px] font-black uppercase ml-4 tracking-widest">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="space-y-2">
                    <label class="block text-[#9db6a1] text-[10px] font-black uppercase tracking-widest ml-2">Access Key</label>
                    <div class="relative group">
                        <span class="absolute inset-y-0 left-6 flex items-center text-[#163a24]/30 group-focus-within:text-[#00a651] transition-colors text-xl">
                            <i class="fas fa-lock"></i>
                        </span>
                        <input :type="showPassword ? 'text' : 'password'" 
                               name="password" 
                               placeholder="••••••••"
                               class="w-full pl-16 pr-16 py-5 bg-white rounded-[1.75rem] text-[#163a24] placeholder-gray-300 font-black outline-none border-none focus:ring-4 focus:ring-[#f3bc3e]/20 transition-all shadow-inner"
                               required>
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute inset-y-0 right-6 flex items-center text-[#163a24]/20 hover:text-[#00a651] transition-colors text-xl">
                            <i class="fas" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-2 text-red-400 text-[9px] font-black uppercase ml-4 tracking-widest">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between gap-4 px-2">
                    <label class="flex items-center gap-4 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="remember" class="peer hidden" {{ old('remember') ? 'checked' : '' }}>
                            <div class="w-6 h-6 bg-white/10 rounded-lg border-2 border-white/20 peer-checked:bg-[#f3bc3e] peer-checked:border-[#f3bc3e] transition-all"></div>
                            <i class="fas fa-check absolute inset-0 text-xs text-[#163a24] flex items-center justify-center opacity-0 peer-checked:opacity-100 transition-opacity"></i>
                        </div>
                        <span class="text-[10px] font-black text-[#9db6a1] uppercase tracking-widest group-hover:text-white transition-colors">Remember Me</span>
                    </label>

                    <button type="button" @click="showForgotNotice = true; forgotStep = 1" class="text-[10px] font-black text-[#f3bc3e] uppercase tracking-[0.15em] hover:text-yellow-400 transition-all border-b-2 border-transparent hover:border-[#f3bc3e]">
                        Forgot Password?
                    </button>
                </div>

                <div class="flex items-center justify-center pt-2">
                    <button type="button" @click="openClaimModal()" class="text-[#f3bc3e] text-[10px] font-black uppercase tracking-widest hover:underline decoration-2 underline-offset-4 transition-all opacity-40 hover:opacity-100">
                        First time? Initialize account
                    </button>
                </div>
                
                <button type="submit" 
                        class="w-full bg-[#f3bc3e] text-[#163a24] py-6 rounded-[2rem] font-black uppercase tracking-[0.3em] text-sm hover:bg-yellow-400 transition-all duration-500 flex items-center justify-center gap-4 shadow-2xl shadow-[#f3bc3e]/20 active:scale-95 group">
                    SIGN IN <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                </button>
            </form>
        </div>
        
        <!-- Bottom Footer Info -->
        <div class="text-center mt-12">
            <p class="text-[#163a24] text-[10px] font-black uppercase tracking-[0.4em] opacity-30 italic">
                V.O.I.C.E | V.1.4 | Aldersgate College Inc.
            </p>
        </div>
    </div>

    <!-- Claim Account Modal -->
    <template x-teleport="body">
        <div x-show="claimModalOpen" 
             class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-[#163a24]/95 backdrop-blur-xl"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-cloak>
            
            <div class="bg-white rounded-[3.5rem] shadow-2xl w-full max-w-md overflow-hidden border-4 border-[#f3bc3e]/20"
                 @click.away="closeClaimModal()">
                
                <div class="p-10 flex justify-between items-center bg-[#163a24] text-white border-b-4 border-[#f3bc3e]">
                    <div>
                        <h3 class="text-2xl font-black uppercase tracking-tight">Identity Vault</h3>
                        <p class="text-[10px] text-[#f3bc3e] font-bold uppercase tracking-widest mt-1">Registry Verification</p>
                    </div>
                    <button @click="closeClaimModal()" class="text-white/40 hover:text-white transition">
                        <i class="fas fa-times text-2xl"></i>
                    </button>
                </div>

                <div class="p-10 space-y-8">
                    <!-- Step 1: ID Entry -->
                    <div x-show="claimStep === 1" x-transition>
                        <p class="text-sm font-bold text-[#163a24]/60 mb-8 leading-relaxed italic">
                            Enter your official institutional ID number to synchronize your credentials.
                        </p>
                        <div class="space-y-6">
                            <input type="text" x-model="claimId" placeholder="AC-2024-XXXX"
                                   class="w-full px-8 py-5 bg-gray-50 border-2 border-transparent focus:border-[#f3bc3e] rounded-[1.5rem] font-black text-[#163a24] outline-none transition uppercase shadow-inner text-center tracking-widest">
                            <button @click="checkId()" :disabled="loading"
                                    class="w-full bg-[#163a24] text-white py-5 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lg hover:bg-[#2d6a3d] transition-all flex items-center justify-center gap-3">
                                <span x-show="!loading">Begin Validation</span>
                                <i x-show="loading" class="fas fa-circle-notch fa-spin"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 2: Contact Info -->
                    <div x-show="claimStep === 2" x-transition x-cloak>
                        <p class="text-lg font-black text-[#163a24] mb-2 uppercase tracking-tight">Access Granted, <span x-text="foundUser.name.split(' ')[0]" class="text-[#00a651]"></span>!</p>
                        <p class="text-xs font-bold text-[#163a24]/60 mb-8 italic leading-relaxed">
                            Provide your contact anchor to receive your secure temporary PIN.
                        </p>
                        <div class="space-y-6">
                            <input type="text" x-model="contactInfo" placeholder="Email or Mobile Number"
                                   class="w-full px-8 py-5 bg-gray-50 border-2 border-transparent focus:border-[#f3bc3e] rounded-[1.5rem] font-black text-[#163a24] outline-none transition shadow-inner text-center">
                            <button @click="verifyAccount()" :disabled="loading"
                                    class="w-full bg-[#00a651] text-white py-5 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lg hover:bg-emerald-600 transition-all flex items-center justify-center gap-3">
                                <span x-show="!loading">Generate PIN</span>
                                <i x-show="loading" class="fas fa-circle-notch fa-spin"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Step 3: Success -->
                    <div x-show="claimStep === 3" x-transition x-cloak class="text-center pb-4">
                        <div class="w-20 h-20 bg-green-50 rounded-[2rem] flex items-center justify-center text-green-500 text-3xl mx-auto mb-8 border-4 border-green-100 shadow-inner">
                            <i class="fas fa-shield-check"></i>
                        </div>
                        <h4 class="text-2xl font-black text-[#163a24] uppercase tracking-tight mb-4">Registry Success</h4>
                        
                        <div class="bg-gray-50 p-8 rounded-[2.5rem] border-4 border-dashed border-gray-200 mb-8">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Secure Access Key</p>
                            <p class="text-5xl font-black text-[#163a24] tracking-[0.25em]" x-text="generatedPin"></p>
                        </div>

                        <button @click="closeClaimModal()" 
                                class="w-full bg-[#163a24] text-white py-5 rounded-2xl font-black uppercase tracking-widest text-[10px] shadow-lg hover:bg-[#2d6a3d] transition-all">
                            Complete Synchronization
                        </button>
                    </div>

                    <div x-show="claimError" x-transition x-cloak class="mt-4 p-5 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-xl text-[10px] font-black uppercase tracking-wider">
                        <span x-text="claimError"></span>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>

<script>
    function loginManager() {
        return {
            showPassword: false,
            showForgotNotice: false,
            forgotContact: '',
            forgotMessage: 'PLEASE INPUT YOUR EMAIL OR PHONE NUMBER TO RECEIVE INSTRUCTIONS FOR GETTING YOUR NEW PASSWORD.',
            forgotStep: 1,
            generatedResetPin: '',
            claimModalOpen: false,
            claimStep: 1,
            claimId: '',
            contactInfo: '',
            claimError: '',
            loading: false,
            foundUser: null,
            successMessage: '',
            generatedPin: '',

            async handleReset() {
                if (!this.forgotContact) return;
                this.loading = true;
                try {
                    const response = await fetch("{{ route('password.forgot') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ contact: this.forgotContact })
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.forgotMessage = data.message;
                        this.generatedResetPin = data.new_password;
                        this.forgotStep = 3;
                    } else {
                        this.forgotMessage = data.message;
                    }
                } catch (e) {
                    this.forgotMessage = 'Institutional alert: Connection timeout. Please retry.';
                }
                this.loading = false;
            },

            openClaimModal() {
                this.claimModalOpen = true;
                this.claimStep = 1;
                this.claimId = '';
                this.contactInfo = '';
                this.claimError = '';
            },

            closeClaimModal() {
                this.claimModalOpen = false;
            },

            async checkId() {
                this.loading = true;
                this.claimError = '';
                try {
                    const response = await fetch("{{ route('claim.check') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ id_number: this.claimId })
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.foundUser = data.user;
                        this.claimStep = 2;
                    } else {
                        this.claimError = data.message;
                    }
                } catch (e) {
                    this.claimError = 'Connection error. Please try again.';
                }
                this.loading = false;
            },

            async verifyAccount() {
                this.loading = true;
                this.claimError = '';
                try {
                    const response = await fetch("{{ route('claim.verify') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ 
                            id_number: this.claimId,
                            contact_info: this.contactInfo
                        })
                    });
                    const data = await response.json();
                    if (data.success) {
                        this.successMessage = data.message;
                        this.generatedPin = data.pin;
                        this.claimStep = 3;
                    } else {
                        this.claimError = data.message;
                    }
                } catch (e) {
                    this.claimError = 'Connection error. Please try again.';
                }
                this.loading = false;
            }
        }
    }
</script>

<style>
    /* Reset layout interference */
    main { padding: 0 !important; margin-left: 0 !important; background: transparent !important; }
    header { display: none !important; }
    aside { display: none !important; }
    [x-cloak] { display: none !important; }
    .tracking-tightest { letter-spacing: -0.05em; }
</style>
@endsection
