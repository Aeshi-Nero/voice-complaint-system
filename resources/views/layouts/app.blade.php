<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>V.O.I.C.E. - Virtual Outlet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script src="https://js.pusher.com/8.3.0/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
    <style>
        [x-cloak] { display: none !important; }
        .live-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
    </style>
    <script>
        // Global live update handler
        window.LiveUpdate = {
            lastCount: {},
            async check(key, url, callback) {
                try {
                    const res = await fetch(url);
                    const data = await res.json();
                    const currentCount = Array.isArray(data) ? data.length : (data.total_votes || 0);
                    
                    if (this.lastCount[key] !== undefined && currentCount > this.lastCount[key]) {
                        callback(data);
                    }
                    this.lastCount[key] = currentCount;
                } catch (e) {}
            }
        };

        // Initialize Echo
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'reverb',
            key: '{{ env('REVERB_APP_KEY') }}',
            wsHost: '{{ env('REVERB_HOST') }}',
            wsPort: {{ env('REVERB_PORT', 8080) }},
            wssPort: {{ env('REVERB_PORT', 8080) }},
            forceTLS: false,
            enabledTransports: ['ws', 'wss'],
        });
    </script>
</head>
<body class="bg-[#fef9e1]">
    <div x-data="{ sidebarOpen: false, profileModalOpen: false, profilePreview: null, showCurrentPassword: false, showNewPassword: false }" class="min-h-screen flex flex-col">  
            <!-- Mobile Header -->
            <div class="lg:hidden bg-[#163a24] text-white p-4 flex items-center justify-between sticky top-0 z-[60] shadow-lg">
                @auth
                    <div class="flex items-center gap-3">
                        <button @click="sidebarOpen = true" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition">
                            <i class="fas fa-bars"></i>
                        </button>
                        <span class="text-xs font-black uppercase tracking-widest text-white">Welcome, {{ auth()->user()->name }}</span>
                    </div>

                    <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition">
                        <i class="fas fa-bell"></i>
                    </button>
                @endauth
            </div>

            <!-- Mobile Sidebar Overlay -->
            <div x-show="sidebarOpen" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="sidebarOpen = false" 
                 class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[45] lg:hidden"
                 x-cloak>
            </div>

            <!-- Sidebar Navigation -->
            @auth
            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed inset-y-0 left-0 z-50 w-80 bg-[#163a24] text-white transition-transform duration-300 transform overflow-y-auto flex flex-col shadow-2xl"
            >
                <!-- Logo -->
                <div class="p-8 mb-4">
                    <div class="flex items-center gap-4">
                        <div class="bg-white w-12 h-12 rounded-full flex items-center justify-center overflow-hidden border-2 border-[#f3bc3e] shadow-inner">
                            <img src="data:image/png;base64,{{ $logoBase64 ?? '' }}" alt="Logo" class="w-full h-full object-cover">
                        </div>
                        <div>
                            <h2 class="text-2xl font-black tracking-tighter leading-none uppercase text-white">V.O.I.C.E.</h2>
                            <p class="text-[9px] font-bold text-[#f3bc3e] tracking-tight uppercase opacity-80 mt-1">Virtual Outlet for Institutional Complaint Engagement</p>
                        </div>
                    </div>
                </div>

                <!-- Nav -->
                <nav class="flex-1 px-4 space-y-1">
                    @if(auth()->check() && !auth()->user()->isAdmin())
                        <div class="px-4 mb-6">
                            <div class="bg-white/10 rounded-3xl p-4 flex items-center gap-4 border border-white/5">
                                <div class="relative w-14 h-14 flex items-center justify-center">
                                    <!-- Background Circle -->
                                    <svg class="absolute inset-0 w-full h-full -rotate-90">
                                        <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="transparent" class="text-white/5" />
                                        <!-- Progress Circle -->
                                        <circle cx="28" cy="28" r="24" stroke="currentColor" stroke-width="4" fill="transparent" 
                                                class="text-yellow-400 transition-all duration-1000 ease-out"
                                                stroke-dasharray="150.8"
                                                :stroke-dashoffset="150.8 - ({{ auth()->user()->getRemainingComplaints() }} / 6 * 150.8)" />
                                    </svg>
                                    <div class="relative z-10 w-11 h-11 rounded-full bg-[#163a24] flex items-center justify-center shadow-inner">
                                        <span class="text-[10px] font-black text-white italic tracking-tighter">{{ auth()->user()->getRemainingComplaints() }}/6</span>
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <p class="text-[9px] font-black text-white/40 uppercase tracking-[0.2em] leading-tight">current</p>
                                    <p class="text-[11px] font-black text-white uppercase tracking-widest leading-tight">submissions left</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    <p class="px-4 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-4 mt-8">Main Menu</p>
                    
                    @if(auth()->user()?->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group {{ Request::is('admin/dashboard') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-th-large w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">Dashboard</span>
                        </a>
                        <a href="{{ route('admin.complaints') }}" 
                           class="flex items-center justify-between px-6 py-4 rounded-2xl transition group {{ Request::is('admin/complaints*') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <div class="flex items-center gap-4">
                                <i class="fas fa-list-alt w-5 text-center"></i>
                                <span class="text-sm font-black uppercase tracking-widest">All Complaints</span>
                            </div>
                            @if(($totalComplaintsCount ?? 0) > 0)
                            <div class="flex items-center justify-center min-w-[20px] h-5 px-1.5 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-5 w-auto px-1.5 bg-green-500 text-[10px] font-black text-white items-center justify-center shadow-[0_0_8px_rgba(34,197,94,0.6)]">
                                    {{ $totalComplaintsCount }}
                                </span>
                            </div>
                            @endif
                        </a>
                        <a href="{{ route('admin.users.index') }}" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group {{ Request::is('admin/users*') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-users-cog w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">User Management</span>
                        </a>
                        <a href="{{ route('admin.polls.index') }}" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group {{ Request::is('admin/polls*') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-poll-h w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">Polls Management</span>
                        </a>
                        <a href="{{ route('admin.reports.index') }}" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group {{ Request::is('admin/reports*') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-chart-line w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">Reports</span>
                        </a>
                    @else
                        <a href="{{ route('user.dashboard') }}" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group {{ Request::is('user/dashboard') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-th-large w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">Homepage</span>
                        </a>
                        <a href="{{ route('user.complaints.index') }}" 
                           class="flex items-center justify-between px-6 py-4 rounded-2xl transition group {{ Request::is('user/complaints') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <div class="flex items-center gap-4">
                                <i class="fas fa-list-ul w-5 text-center"></i>
                                <span class="text-sm font-black uppercase tracking-widest">My Complaints</span>
                            </div>
                            @if(($unseenMessagesCount ?? 0) > 0)
                            <div class="flex items-center justify-center min-w-[20px] h-5 px-1.5 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-5 w-auto px-1.5 bg-green-500 text-[10px] font-black text-white items-center justify-center shadow-[0_0_8px_rgba(34,197,94,0.6)]">
                                    {{ $unseenMessagesCount }}
                                </span>
                            </div>
                            @endif
                        </a>
                        <a href="{{ route('user.polls') }}" 
                           class="flex items-center justify-between px-6 py-4 rounded-2xl transition group {{ Request::is('user/polls') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <div class="flex items-center gap-4">
                                <i class="fas fa-chart-bar w-5 text-center"></i>
                                <span class="text-sm font-black uppercase tracking-widest">Polls</span>
                            </div>
                            @if($hasNewPolls ?? false)
                            <div class="flex h-2 w-2 relative">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.6)]"></span>
                            </div>
                            @endif
                        </a>
                    @endif
                </nav>

                <!-- Bottom Sidebar -->
                <div class="p-4 mt-auto space-y-4">
                    <!-- Divider line -->
                    <div class="border-t border-white/10 mx-4 mb-4"></div>

                    <div class="px-4 pb-4">
                        <button @click="profileModalOpen = true" class="flex items-center gap-4 w-full text-left transition group">
                            <div class="relative">
                                @if(auth()->user()?->profile_image)
                                    <div class="w-12 h-12 rounded-2xl overflow-hidden border-2 border-white/20 group-hover:border-yellow-400 transition-colors shadow-lg">
                                        <img src="{{ asset('storage/' . auth()->user()?->profile_image) }}" class="w-full h-full object-cover">
                                    </div>
                                @else
                                    <div class="w-12 h-12 rounded-2xl bg-white/10 border-2 border-white/20 group-hover:border-yellow-400 flex items-center justify-center transition-colors shadow-lg">
                                        <i class="fas fa-user text-xl text-white/40 group-hover:text-yellow-400"></i>
                                    </div>
                                @endif
                                <!-- Small edit badge -->
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-yellow-400 rounded-lg flex items-center justify-center shadow-md">
                                    <i class="fas fa-pen text-[8px] text-[#163a24]"></i>
                                </div>
                            </div>
                            
                            <div class="flex-1 overflow-hidden">
                                <p class="text-xs font-black text-yellow-400 uppercase tracking-widest truncate">{{ auth()->user()?->id_number }}</p>
                                <p class="text-[9px] font-bold text-white/30 uppercase tracking-[0.2em] mt-0.5">
                                    {{ auth()->user()?->role === 'admin' ? 'System Administrator' : 'Verified Student' }}
                                </p>
                                <p id="institutional-clock" class="text-[9px] font-black text-[#f3bc3e] uppercase tracking-[0.1em] mt-1.5 opacity-80"></p>
                            </div>
                        </button>

                        <form method="POST" action="{{ route('logout') }}" class="mt-4">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 px-4 py-2 w-full text-white/30 hover:text-red-400 transition group rounded-xl hover:bg-white/5">
                                <i class="fas fa-sign-out-alt text-sm"></i>
                                <span class="text-[10px] font-black uppercase tracking-widest">Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
            @endauth

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-[#fef9e1] relative lg:ml-80 transition-all duration-300">
                <!-- PC Header -->
                <div class="hidden lg:flex bg-[#163a24] text-white p-6 sticky top-0 z-40 shadow-lg items-center justify-between">
                    @auth
                        <span class="text-lg font-black uppercase tracking-widest text-white">Welcome, {{ auth()->user()->name }}</span>
                        <button class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white/10 hover:bg-white/20 transition shadow-lg border border-white/5">
                            <i class="fas fa-bell text-lg"></i>
                        </button>
                    @endauth
                </div>

                <div class="p-8 lg:p-12">
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-green-100 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-check-circle"></i>
                                <p class="font-medium">{{ session('success') }}</p>
                            </div>
                        </div>
                    @endif
                    
                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-100 border-l-4 border-red-500 text-red-700 rounded-lg shadow-sm">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-exclamation-triangle"></i>
                                <p class="font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="mb-6 p-6 bg-red-50 border-4 border-red-100 rounded-[2rem] shadow-sm">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 bg-red-600 rounded-xl flex items-center justify-center text-white shrink-0">
                                    <i class="fas fa-exclamation-circle text-lg"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-black text-red-900 uppercase tracking-widest mb-2">Attention Required</h4>
                                    <ul class="list-disc list-inside text-xs font-bold text-red-700 space-y-1">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @yield('content')
                </div>

                @auth
                <!-- Profile Edit Modal -->
                <div x-show="profileModalOpen" 
                     x-cloak
                     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-[#004d26]/80 backdrop-blur-sm"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0">
                    
                    <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md overflow-hidden border-4 border-[#004d26]/10"
                         @click.away="profileModalOpen = false">
                        <div class="p-8 flex justify-between items-center bg-[#004d26] text-white">
                            <h3 class="text-xl font-black uppercase tracking-tight">Edit Profile</h3>
                            <button @click="profileModalOpen = false" class="text-white/40 hover:text-white transition">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                            @csrf
                            
                            <!-- Profile Image Upload -->
                            <div class="flex flex-col items-center mb-4">
                                <div class="relative group">
                                    <div class="w-28 h-28 rounded-[2rem] overflow-hidden border-4 border-gray-50 shadow-xl bg-gray-50 flex items-center justify-center text-4xl font-black text-gray-200 rotate-3 group-hover:rotate-0 transition-transform">
                                        <div x-show="!profilePreview" class="w-full h-full flex items-center justify-center">
                                            @if(auth()->user()?->profile_image)
                                                <img src="{{ asset('storage/' . auth()->user()?->profile_image) }}" class="w-full h-full object-cover">
                                            @else
                                                {{ strtoupper(substr(auth()->user()?->name, 0, 1)) }}
                                            @endif
                                        </div>
                                        <div x-show="profilePreview" class="w-full h-full">
                                            <img :src="profilePreview" class="w-full h-full object-cover">
                                        </div>
                                    </div>
                                    <label for="profile_image_input" class="absolute -bottom-2 -right-2 bg-yellow-400 text-[#004d26] p-3 rounded-2xl shadow-lg cursor-pointer hover:bg-yellow-300 transition transform hover:scale-110">
                                        <i class="fas fa-camera text-sm"></i>
                                        <input type="file" id="profile_image_input" name="profile_image" class="hidden" accept="image/*" 
                                               @change="const file = $event.target.files[0]; if (file) { const reader = new FileReader(); reader.onload = (e) => { profilePreview = e.target.result; }; reader.readAsDataURL(file); }">
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Username</label>
                                    <input type="text" name="username" value="{{ auth()->user()?->name }}" required
                                           class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-yellow-400 rounded-2xl font-bold text-[#163a24] outline-none transition">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Email Address</label>
                                    <input type="email" name="email" value="{{ auth()->user()?->email }}" required
                                           class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-yellow-400 rounded-2xl font-bold text-[#163a24] outline-none transition">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Phone Number</label>
                                    <input type="text" name="phone_number" value="{{ auth()->user()?->phone_number }}"
                                           placeholder="09123456789"
                                           maxlength="11"
                                           pattern="[0-9]{11}"
                                           oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                                           title="Please enter exactly 11 digits"
                                           class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-yellow-400 rounded-2xl font-bold text-[#163a24] outline-none transition">
                                </div>

                                @if(!auth()->user()?->isAdmin())
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Academic Course</label>
                                    <input type="text" name="course" value="{{ auth()->user()?->course }}" required
                                           class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-yellow-400 rounded-2xl font-bold text-[#163a24] outline-none transition">
                                </div>
                                @endif
                            </div>

                            <div class="pt-4 border-t-2 border-dashed border-gray-100 mt-4">
                                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-4">Security Update</p>
                                <div class="space-y-4">
                                    <div class="relative">
                                        <input :type="showCurrentPassword ? 'text' : 'password'" name="current_password" placeholder="Current Password"
                                               class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-yellow-400 rounded-2xl font-bold text-[#163a24] outline-none transition text-sm">
                                        <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-yellow-500 transition-colors">
                                            <i class="fas" :class="showCurrentPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                    <div class="relative">
                                        <input :type="showNewPassword ? 'text' : 'password'" name="password" placeholder="New Password (Optional)"
                                               class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-yellow-400 rounded-2xl font-bold text-[#163a24] outline-none transition text-sm">
                                        <button type="button" @click="showNewPassword = !showNewPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-yellow-500 transition-colors">
                                            <i class="fas" :class="showNewPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                    <input type="password" name="password_confirmation" placeholder="Confirm New Password"
                                           class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-yellow-400 rounded-2xl font-bold text-[#163a24] outline-none transition text-sm">
                                </div>
                            </div>

                            <div class="pt-4">
                                <button type="submit" class="w-full bg-yellow-400 text-[#004d26] py-5 rounded-2xl font-black uppercase tracking-widest shadow-[0_8px_0_rgb(202,138,4)] active:shadow-none active:translate-y-[8px] transition-all">
                                    Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Help Button
                <button class="fixed bottom-8 right-8 w-16 h-16 bg-[#004d26] text-white rounded-[1.5rem] flex items-center justify-center shadow-2xl hover:bg-[#003d1e] transition-all transform hover:rotate-6 active:scale-95 group border-4 border-white">
                    <i class="fas fa-question text-xl group-hover:animate-bounce"></i>
                </button> -->
                @endauth
            </main>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        function updateClock() {
            const clock = document.getElementById('institutional-clock');
            if (clock) {
                const now = new Date();
                const options = { 
                    weekday: 'short', 
                    month: 'short', 
                    day: 'numeric', 
                    hour: '2-digit', 
                    minute: '2-digit', 
                    second: '2-digit',
                    hour12: true 
                };
                clock.textContent = now.toLocaleString('en-US', options);
            }
        }
        setInterval(updateClock, 1000);
        updateClock();
    </script>
</body>
</html>
