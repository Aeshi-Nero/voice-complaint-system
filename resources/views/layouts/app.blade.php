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
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-[#fef9e1]">
    <div x-data="{ sidebarOpen: false, profileModalOpen: {{ $errors->any() ? 'true' : 'false' }}, profilePreview: null }" class="min-h-screen flex flex-col">
        <!-- Top Navigation Bar -->
        <header class="bg-[#fef9e1] text-[#163a24] z-40 fixed top-0 left-0 right-0 h-20 flex items-center justify-between px-8 lg:pl-80 transition-all duration-300">
            <div class="flex items-center gap-12 flex-1">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg hover:bg-[#163a24]/5 transition">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                
                @if(auth()->user()?->isAdmin())
                    <h1 class="text-lg font-black text-[#163a24] tracking-tight uppercase">Administrative Console</h1>
                @else
                    <!-- Nav Links -->
                    <nav class="hidden md:flex items-center gap-8">
                        <a href="{{ route('user.dashboard') }}" class="font-black text-sm uppercase tracking-widest {{ Request::is('user/dashboard') ? 'text-[#163a24]' : 'text-gray-400 hover:text-[#163a24]' }}">Dashboard</a>
                        <a href="{{ route('user.complaints.index') }}" class="font-black text-sm uppercase tracking-widest {{ Request::is('user/complaints') ? 'text-[#163a24]' : 'text-gray-400 hover:text-[#163a24]' }}">My Complaints</a>
                        <a href="#" class="font-black text-sm uppercase tracking-widest text-gray-400 hover:text-[#163a24]">Support</a>
                        <a href="#" class="font-black text-sm uppercase tracking-widest text-gray-400 hover:text-[#163a24]">Guidelines</a>
                    </nav>
                @endif

                <!-- Search Bar -->
                <div class="hidden lg:flex flex-1 max-w-md relative">
                    <span class="absolute inset-y-0 left-4 flex items-center text-gray-400">
                        <i class="fas fa-search text-xs"></i>
                    </span>
                    <input type="text" placeholder="Search records..." class="w-full bg-[#f2e19d]/40 border-none rounded-xl py-3 pl-10 pr-4 text-sm font-bold placeholder-gray-400 focus:ring-2 focus:ring-[#f3bc3e] transition">
                </div>
            </div>

            @auth
            <div class="flex items-center gap-6">
                <button class="text-gray-400 hover:text-[#163a24] transition relative">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-[#f3bc3e] rounded-full border-2 border-[#fef9e1]"></span>
                </button>
                <button class="text-gray-400 hover:text-[#163a24] transition">
                    <i class="fas fa-question-circle text-xl"></i>
                </button>
                
                @if(auth()->user()?->isAdmin())
                    <button class="bg-[#112d1c] text-white px-6 py-2.5 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-[#1a442a] transition-all shadow-lg">
                        Create New
                    </button>
                @endif

                <div @click="profileModalOpen = true" class="w-10 h-10 rounded-xl bg-[#163a24] overflow-hidden cursor-pointer border-2 border-[#163a24]/10 shadow-md">
                    @if(auth()->user()?->profile_image)
                        <img src="{{ asset('storage/' . auth()->user()?->profile_image) }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-white font-black text-xs uppercase">
                            {{ substr(auth()->user()?->name, 0, 1) }}
                        </div>
                    @endif
                </div>
            </div>
            @endauth
        </header>

        <div class="flex flex-1 pt-20">
            <!-- Sidebar Navigation -->
            @auth
            <aside 
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed inset-y-0 left-0 z-50 w-72 bg-[#163a24] text-white transition-transform duration-300 transform overflow-y-auto flex flex-col shadow-2xl"
            >
                <!-- Logo -->
                <div class="p-8 mb-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-[#f3bc3e] p-2 rounded-xl">
                            <i class="fas fa-graduation-cap text-[#163a24] text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-black tracking-tighter leading-none uppercase">V.O.I.C.E.</h2>
                            <p class="text-[8px] font-bold text-[#f3bc3e] tracking-[0.3em] uppercase opacity-80 mt-1">University Portal</p>
                        </div>
                    </div>
                </div>

                <!-- Nav -->
                <nav class="flex-1 px-4 space-y-1">
                    <p class="px-4 text-[10px] font-black text-white/30 uppercase tracking-[0.2em] mb-4 mt-8">Main Menu</p>
                    
                    @if(auth()->user()?->isAdmin())
                        <a href="{{ route('admin.dashboard') }}" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group {{ Request::is('admin/dashboard') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-th-large w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">Dashboard</span>
                        </a>
                        <a href="{{ route('admin.complaints') }}" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group {{ Request::is('admin/complaints*') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-list-alt w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">All Complaints</span>
                        </a>
                        <a href="{{ route('admin.polls.index') }}" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group {{ Request::is('admin/polls*') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-poll-h w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">Polls Management</span>
                        </a>
                    @else
                        <a href="{{ route('user.dashboard') }}" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group {{ Request::is('user/dashboard') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-th-large w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">Dashboard</span>
                        </a>
                        <a href="{{ route('user.complaints.create') }}" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group {{ Request::is('user/complaints/create') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-plus-square w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">New Complaint</span>
                        </a>
                        <a href="{{ route('user.complaints.index') }}" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group {{ Request::is('user/complaints') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-list-ul w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">My Complaints</span>
                        </a>
                        <a href="{{ route('user.polls') }}" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group {{ Request::is('user/polls') ? 'bg-white/10 text-white shadow-lg' : 'text-white/40 hover:text-white hover:bg-white/5' }}">
                            <i class="fas fa-chart-bar w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">Polls</span>
                        </a>
                        <a href="#" 
                           class="flex items-center gap-4 px-6 py-4 rounded-2xl transition group text-white/40 hover:text-white hover:bg-white/5">
                            <i class="fas fa-cog w-5 text-center"></i>
                            <span class="text-sm font-black uppercase tracking-widest">Settings</span>
                        </a>
                    @endif
                </nav>

                <!-- Bottom Sidebar -->
                <div class="p-4 mt-auto space-y-4">
                    @if(!auth()->user()?->isAdmin())
                        <a href="{{ route('user.complaints.create') }}" 
                           class="flex items-center justify-center gap-3 w-full bg-[#112d1c] hover:bg-[#1a442a] text-[#f3bc3e] py-4 rounded-2xl transition font-black text-xs uppercase tracking-widest shadow-xl">
                            <i class="fas fa-plus"></i>
                            <span>New Complaint</span>
                        </a>
                    @endif

                    <div class="pt-6 pb-4 space-y-1">
                        <button @click="profileModalOpen = true" class="flex items-center gap-4 px-6 py-3 w-full text-white/40 hover:text-white transition group">
                            <i class="fas fa-user-circle w-5 text-xl"></i>
                            <span class="text-xs font-black uppercase tracking-widest">Profile</span>
                        </button>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-4 px-6 py-3 w-full text-white/40 hover:text-red-400 transition group">
                                <i class="fas fa-sign-out-alt w-5 text-xl"></i>
                                <span class="text-xs font-black uppercase tracking-widest">Logout</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
            @endauth

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-[#fef9e1] relative lg:ml-0 transition-all duration-300">
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

                        @if($errors->any())
                            <div class="px-8 pt-6">
                                <div class="p-4 bg-red-50 border-2 border-red-100 text-red-600 text-[10px] font-black uppercase rounded-2xl">
                                    <ul class="list-disc list-inside">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif
                        
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
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Full Name</label>
                                    <input type="text" name="name" value="{{ auth()->user()?->name }}" required
                                           class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-yellow-400 rounded-2xl font-bold text-[#004d26] outline-none transition">
                                </div>

                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Email Address</label>
                                    <input type="email" name="email" value="{{ auth()->user()?->email }}" required
                                           class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-yellow-400 rounded-2xl font-bold text-[#004d26] outline-none transition">
                                </div>

                                @if(!auth()->user()?->isAdmin())
                                <div>
                                    <label class="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2 ml-1">Academic Course</label>
                                    <input type="text" name="course" value="{{ auth()->user()?->course }}" required
                                           class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-yellow-400 rounded-2xl font-bold text-[#004d26] outline-none transition">
                                </div>
                                @endif
                            </div>

                            <div class="pt-4 border-t-2 border-dashed border-gray-100 mt-4">
                                <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-4">Security Update</p>
                                <div class="space-y-4">
                                    <input type="password" name="password" placeholder="New Password (Optional)"
                                           class="w-full px-6 py-3.5 bg-gray-50 border-2 border-transparent focus:border-yellow-400 rounded-2xl font-bold text-[#004d26] outline-none transition text-sm">
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

                <!-- Help Button -->
                <button class="fixed bottom-8 right-8 w-16 h-16 bg-[#004d26] text-white rounded-[1.5rem] flex items-center justify-center shadow-2xl hover:bg-[#003d1e] transition-all transform hover:rotate-6 active:scale-95 group border-4 border-white">
                    <i class="fas fa-question text-xl group-hover:animate-bounce"></i>
                </button>
                @endauth
            </main>
        </div>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
