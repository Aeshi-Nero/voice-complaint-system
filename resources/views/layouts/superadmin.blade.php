<!DOCTYPE html>
<html class="light" lang="en">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'V.O.I.C.E. Superadmin')</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&amp;display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
    <script>
        tailwind.config = {
          darkMode: "class",
          theme: {
            extend: {
              "colors": {
                      "primary": "#003616",
                      "primary-container": "#1a4d2a",
                      "secondary": "#2d6a3d",
                      "tertiary": "#ffdf95",
                      "accent": "#f3bc3e",
                      "surface": "#ffffff",
                      "on-surface": "#201c02",
                      "on-surface-variant": "#414941",
                      "outline-variant": "#c1c9be",
                      "error": "#ba1a1a"
              },
              "borderRadius": {
                      "DEFAULT": "0.125rem",
                      "lg": "0.25rem",
                      "xl": "0.5rem",
                      "full": "0.75rem"
              },
              "fontFamily": {
                      "headline": ["Inter"],
                      "display": ["Inter"],
                      "body": ["Inter"],
                      "label": ["Inter"]
              }
            },
          },
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Inter', sans-serif; background-color: #fff9ec; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        .active-pill { font-variation-settings: 'FILL' 1; }
        .academic-gradient {
            background: linear-gradient(135deg, #003616 0%, #1a4d2a 100%);
        }
        .ivory-glass {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(193, 201, 190, 0.15);
        }
        .signature-texture {
            background: linear-gradient(135deg, #003616 0%, #1a4d2a 100%);
        }
    </style>
    @yield('styles')
</head>
<body class="text-on-surface antialiased overflow-x-hidden" x-data="{ sidebarOpen: false, profileModalOpen: false, profilePreview: null, showCurrentPassword: false, showNewPassword: false }">

    <!-- Mobile Header -->
    <div class="lg:hidden bg-[#163a24] text-white p-4 flex items-center justify-between sticky top-0 z-[60] shadow-lg">
        @auth
            <button @click="sidebarOpen = true" class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition">
                <i class="fas fa-bars"></i>
            </button>

            <div class="flex items-center gap-3">
                <span class="text-xs font-black uppercase tracking-widest text-white">Welcome, {{ auth()->user()->name }}</span>
                <button class="w-10 h-10 flex items-center justify-center rounded-xl bg-white/10 hover:bg-white/20 transition">
                    <i class="fas fa-bell"></i>
                </button>
            </div>
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
            class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[65] lg:hidden"
            x-cloak>
    </div>

    <!-- Sidebar Navigation -->
    <aside 
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        class="fixed inset-y-0 left-0 z-[70] w-64 bg-[#163a24] text-white transition-transform duration-300 transform overflow-y-auto flex flex-col shadow-2xl lg:translate-x-0"
    >
        <!-- Logo -->
        <div class="p-6 lg:p-8 mb-4">
            <div class="flex justify-between items-center lg:block">
                <div class="flex items-center gap-4">
                    <div class="bg-white w-10 h-10 lg:w-12 lg:h-12 rounded-full flex items-center justify-center overflow-hidden border-2 border-[#f3bc3e] shadow-inner shrink-0">
                        <img src="data:image/png;base64,{{ $logoBase64 ?? '' }}" alt="Logo" class="w-full h-full object-cover">
                    </div>
                    <div>
                        <h2 class="text-2xl font-black tracking-tighter leading-none uppercase text-white">V.O.I.C.E.</h2>
                        <p class="text-[9px] font-bold text-[#f3bc3e] tracking-tight uppercase opacity-80 mt-1">Virtual Outlet for Institutional Complaint Engagement</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-white/60 hover:text-white">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 lg:px-4 space-y-1">
            <p class="px-4 text-[9px] lg:text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-2 lg:mb-4 mt-4 lg:mt-8">Superadmin Menu</p>
            
            <a href="{{ route('superadmin.dashboard') }}" 
               class="flex items-center gap-4 px-4 lg:px-6 py-3 lg:py-4 rounded-2xl transition group {{ Request::is('superadmin/dashboard*') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-chart-pie w-5 text-center"></i>
                <span class="text-sm font-black uppercase tracking-widest">Analytics</span>
            </a>
            
            <a href="{{ route('superadmin.complaints.index') }}" 
               class="flex items-center gap-4 px-4 lg:px-6 py-3 lg:py-4 rounded-2xl transition group {{ Request::is('superadmin/complaints*') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-list-alt w-5 text-center"></i>
                <span class="text-sm font-black uppercase tracking-widest">Complaints</span>
            </a>

            <a href="{{ route('superadmin.admins.index') }}" 
               class="flex items-center gap-4 px-4 lg:px-6 py-3 lg:py-4 rounded-2xl transition group {{ Request::is('superadmin/admins*') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-users-cog w-5 text-center"></i>
                <span class="text-sm font-black uppercase tracking-widest">Admins</span>
            </a>

            <a href="{{ route('admin.users.index') }}" 
               class="flex items-center gap-4 px-4 lg:px-6 py-3 lg:py-4 rounded-2xl transition group {{ Request::is('admin/users*') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-users w-5 text-center"></i>
                <span class="text-sm font-black uppercase tracking-widest">Users</span>
            </a>

            <a href="{{ route('superadmin.dashboard') }}" 
               class="flex items-center gap-4 px-4 lg:px-6 py-3 lg:py-4 rounded-2xl transition group {{ Request::is('superadmin/performance*') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                <i class="fas fa-tachometer-alt w-5 text-center"></i>
                <span class="text-sm font-black uppercase tracking-widest">Performance</span>
            </a>
        </nav>

        <!-- Bottom Sidebar -->
        <div class="p-4 mt-auto space-y-4">
            <!-- Divider line -->
            <div class="border-t border-white/10 mx-4 mb-4"></div>

            <div class="px-4 pb-4">
                <button @click="profileModalOpen = true" class="flex items-center gap-4 w-full text-left transition group mb-4">
                    <div class="relative shrink-0">
                        @if(auth()->user()?->profile_image)
                            <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-2xl overflow-hidden border-2 border-white/20 group-hover:border-accent transition-colors shadow-lg">
                                <img src="{{ asset('storage/' . auth()->user()?->profile_image) }}" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-10 h-10 lg:w-12 lg:h-12 rounded-2xl bg-white/10 border-2 border-white/20 group-hover:border-accent flex items-center justify-center transition-colors shadow-lg">
                                <i class="fas fa-user-shield text-lg lg:text-xl text-white/40 group-hover:text-accent"></i>
                            </div>
                        @endif
                        <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-accent rounded-lg flex items-center justify-center shadow-md">
                            <i class="fas fa-pen text-[8px] text-primary"></i>
                        </div>
                    </div>
                    
                    <div class="flex-1 overflow-hidden">
                        <p class="text-xs font-black text-accent uppercase tracking-widest truncate">{{ auth()->user()?->name }}</p>
                        <p class="text-[9px] font-bold text-white/30 uppercase tracking-[0.2em] mt-0.5">
                            Super Administrator
                        </p>
                    </div>
                </button>

                <form method="POST" action="{{ route('logout') }}" class="mt-2">
                    @csrf
                    <button type="submit" class="flex items-center gap-3 px-4 py-2 w-full text-white/30 hover:text-red-400 transition group rounded-xl hover:bg-white/5">
                        <i class="fas fa-sign-out-alt text-sm"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest">Sign Out</span>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Content Canvas -->
    <main class="lg:ml-64 flex-1 overflow-y-auto bg-[#fff9ec] relative transition-all duration-300 min-h-screen">
        <!-- PC Header -->
        <div class="hidden lg:flex bg-[#163a24] text-white p-6 sticky top-0 z-[60] shadow-lg items-center justify-between gap-6">
            @auth
                <span class="text-lg font-black uppercase tracking-widest text-white">Welcome, {{ auth()->user()->name }}</span>
                <button class="w-12 h-12 flex items-center justify-center rounded-2xl bg-white/10 hover:bg-white/20 transition shadow-lg border border-white/5 group">
                    <i class="fas fa-bell text-lg group-hover:text-yellow-400 transition-colors"></i>
                </button>
            @endauth
        </div>

        <div class="p-4 lg:p-8">
            @if(session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-lg shadow-sm">
                    <div class="flex items-center gap-3">
                        <i class="fas fa-check-circle"></i>
                        <p class="font-medium">{{ session('success') }}</p>
                    </div>
                </div>
            @endif
            @yield('content')
        </div>
    </main>

    @auth
    <!-- Profile Modal -->
    <div x-show="profileModalOpen" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-primary/80 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-cloak>
        <div class="bg-white rounded-[3rem] shadow-2xl w-full max-w-md overflow-hidden border-4 border-primary/10" @click.away="profileModalOpen = false">
            <div class="p-8 flex justify-between items-center bg-primary text-white">
                <h3 class="text-xl font-black uppercase tracking-tight text-tertiary">Edit Profile</h3>
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
                            <div x-show="profilePreview" class="w-full h-full" x-cloak>
                                <img :src="profilePreview" class="w-full h-full object-cover">
                            </div>
                        </div>
                        <label for="profile_image_input" class="absolute -bottom-2 -right-2 bg-accent text-primary p-3 rounded-2xl shadow-lg cursor-pointer hover:bg-yellow-300 transition transform hover:scale-110">
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
                               class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-accent rounded-2xl font-bold text-primary outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Email Address</label>
                        <input type="email" name="email" value="{{ auth()->user()?->email }}" required
                               class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-accent rounded-2xl font-bold text-primary outline-none transition">
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Phone Number</label>
                        <input type="text" name="phone_number" value="{{ auth()->user()?->phone_number }}"
                               placeholder="09123456789"
                               maxlength="11"
                               pattern="[0-9]{11}"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                               title="Please enter exactly 11 digits"
                               class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-accent rounded-2xl font-bold text-primary outline-none transition">
                    </div>
                </div>

                <div class="pt-4 border-t-2 border-dashed border-gray-100 mt-4">
                    <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-4">Security Update</p>
                    <div class="space-y-4">
                        <div class="relative">
                            <input :type="showCurrentPassword ? 'text' : 'password'" name="current_password" placeholder="Current Password"
                                   class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-accent rounded-2xl font-bold text-primary outline-none transition text-sm">
                            <button type="button" @click="showCurrentPassword = !showCurrentPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-accent transition-colors">
                                <i class="fas" :class="showCurrentPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <div class="relative">
                            <input :type="showNewPassword ? 'text' : 'password'" name="password" placeholder="New Password (Optional)"
                                   class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-accent rounded-2xl font-bold text-primary outline-none transition text-sm">
                            <button type="button" @click="showNewPassword = !showNewPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-accent transition-colors">
                                <i class="fas" :class="showNewPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                        <input type="password" name="password_confirmation" placeholder="Confirm New Password"
                               class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-accent rounded-2xl font-bold text-primary outline-none transition text-sm">
                    </div>
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full bg-accent text-primary py-5 rounded-2xl font-black uppercase tracking-widest shadow-[0_8px_0_rgb(202,138,4)] active:shadow-none active:translate-y-[8px] transition-all">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endauth

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @yield('scripts')
</body>
</html>
