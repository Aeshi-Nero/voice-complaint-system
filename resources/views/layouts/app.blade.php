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
<body class="bg-[#f3f4f6]">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen flex flex-col">
        <!-- Top Navigation Bar -->
        <header class="bg-[#00a651] text-white shadow-md z-40 fixed top-0 left-0 right-0 h-16 flex items-center justify-between px-4 lg:px-8">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden p-2 rounded-lg hover:bg-white/10 transition">
                    <i class="fas fa-bars"></i>
                </button>
                <div class="flex items-center gap-2">
                    <div class="bg-white/20 p-2 rounded-full">
                        <i class="fas fa-exclamation-circle text-white"></i>
                    </div>
                    <span class="text-xl font-bold tracking-tight">ComplainTrack</span>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <span class="bg-orange-400 text-white text-[10px] px-2 py-0.5 rounded-full font-bold ml-1 uppercase">Admin</span>
                        @endif
                    @endauth
                </div>
            </div>
        </header>

        <div class="flex flex-1 pt-16 h-screen overflow-hidden">
            <!-- Sidebar Navigation -->
            @auth
            <aside 
                x-show="true"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
                class="fixed inset-y-0 left-0 z-30 w-72 bg-[#006633] text-white transition-transform duration-300 transform lg:static lg:inset-auto overflow-y-auto"
            >
                <div class="flex flex-col h-full">
                    <!-- Profile Info in Sidebar -->
                    <div class="p-8 pb-4">
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-16 h-16 rounded-full bg-orange-400 flex items-center justify-center text-2xl font-bold text-white shadow-xl border-4 border-[#007a3d]">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                @if(strlen(auth()->user()->name) > 1 && strpos(auth()->user()->name, ' ') !== false)
                                    {{ strtoupper(substr(auth()->user()->name, strpos(auth()->user()->name, ' ') + 1, 1)) }}
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-lg leading-tight">{{ auth()->user()->name }}</h3>
                                <p class="text-[11px] opacity-70 mb-1 uppercase tracking-wider">{{ auth()->user()->id_number }}</p>
                                @if(auth()->user()->isAdmin())
                                    <span class="bg-orange-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold">Administrator</span>
                                @else
                                    <span class="bg-[#00a651] text-white text-[10px] px-2 py-0.5 rounded-full font-bold">Student</span>
                                    <p class="text-[10px] mt-1 text-cyan-400 font-medium">{{ auth()->user()->course }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Links -->
                    <nav class="flex-1 px-4 py-4 space-y-1">
                        @if(auth()->user()->isAdmin())
                            <div class="mb-6">
                                <p class="px-4 text-[11px] font-bold text-white/50 uppercase tracking-widest mb-3">Quick Stats</p>
                                <div class="space-y-2 px-4">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-white/80">Pending Review</span>
                                        <span class="font-bold text-orange-400">3</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-white/80">In Progress</span>
                                        <span class="font-bold text-blue-400">2</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-white/80">Resolved</span>
                                        <span class="font-bold text-green-400">1</span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('admin.dashboard') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ Request::is('admin/dashboard') ? 'bg-[#00a651] text-white shadow-lg' : 'hover:bg-white/5 text-white/80' }}">
                                <i class="fas fa-chart-pie w-5"></i>
                                <span class="font-semibold">Overview</span>
                            </a>
                            <a href="{{ route('admin.complaints') }}" 
                               class="flex items-center justify-between px-4 py-3 rounded-xl transition {{ Request::is('admin/complaints') ? 'bg-[#00a651] text-white shadow-lg' : 'hover:bg-white/5 text-white/80' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-clipboard-list w-5"></i>
                                    <span class="font-semibold">All Complaints</span>
                                </div>
                                <span class="bg-white/20 text-[11px] px-2 py-0.5 rounded-full">7</span>
                            </a>

                            <div class="pt-6 pb-2">
                                <p class="px-4 text-[11px] font-bold text-white/50 uppercase tracking-widest mb-3">Filter by Status</p>
                                <a href="{{ route('admin.complaints', ['status' => 'pending']) }}" class="flex items-center justify-between px-4 py-2 text-sm hover:text-white transition group">
                                    <div class="flex items-center gap-3">
                                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                        <span class="text-white/70 group-hover:text-white">Pending</span>
                                    </div>
                                    <span class="text-white/50 text-xs">3</span>
                                </a>
                                <a href="{{ route('admin.complaints', ['status' => 'in_progress']) }}" class="flex items-center justify-between px-4 py-2 text-sm hover:text-white transition group">
                                    <div class="flex items-center gap-3">
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        <span class="text-white/70 group-hover:text-white">In Progress</span>
                                    </div>
                                    <span class="text-white/50 text-xs">2</span>
                                </a>
                                <a href="{{ route('admin.complaints', ['status' => 'resolved']) }}" class="flex items-center justify-between px-4 py-2 text-sm hover:text-white transition group">
                                    <div class="flex items-center gap-3">
                                        <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                        <span class="text-white/70 group-hover:text-white">Resolved</span>
                                    </div>
                                    <span class="text-white/50 text-xs">1</span>
                                </a>
                                <a href="{{ route('admin.complaints', ['status' => 'rejected']) }}" class="flex items-center justify-between px-4 py-2 text-sm hover:text-white transition group">
                                    <div class="flex items-center gap-3">
                                        <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                        <span class="text-white/70 group-hover:text-white">Rejected</span>
                                    </div>
                                    <span class="text-white/50 text-xs">1</span>
                                </a>
                            </div>
                        @else
                            <div class="flex gap-4 px-4 mb-6">
                                <div class="flex-1 bg-white/10 rounded-xl p-3 text-center border border-white/10">
                                    <div class="text-xl font-bold">2</div>
                                    <div class="text-[10px] uppercase tracking-tighter opacity-70">Total</div>
                                </div>
                                <div class="flex-1 bg-white/10 rounded-xl p-3 text-center border border-white/10">
                                    <div class="text-xl font-bold text-orange-400">1</div>
                                    <div class="text-[10px] uppercase tracking-tighter opacity-70">Pending</div>
                                </div>
                            </div>

                            <a href="{{ route('user.dashboard') }}" 
                               class="flex items-center justify-between px-4 py-3 rounded-xl transition {{ Request::is('user/dashboard') ? 'bg-[#00a651] text-white shadow-lg' : 'hover:bg-white/5 text-white/80' }}">
                                <div class="flex items-center gap-3">
                                    <i class="fas fa-folder-open w-5"></i>
                                    <span class="font-semibold">My Complaints</span>
                                </div>
                                <span class="bg-white/20 text-[11px] px-2 py-0.5 rounded-full">2</span>
                            </a>
                            <a href="{{ route('user.complaints.create') }}" 
                               class="flex items-center gap-3 px-4 py-3 rounded-xl transition {{ Request::is('user/complaints/create') ? 'bg-[#00a651] text-white shadow-lg' : 'hover:bg-white/5 text-white/80' }}">
                                <i class="fas fa-plus-circle w-5 text-white/60"></i>
                                <span class="font-semibold text-white/90">Submit Complaint</span>
                            </a>
                        @endif
                    </nav>

                    <!-- Footer in Sidebar -->
                    <div class="p-4 border-t border-white/10">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="flex items-center gap-3 px-4 py-2 w-full text-white/70 hover:text-white transition">
                                <i class="fas fa-sign-out-alt"></i>
                                <span class="font-medium">Sign Out</span>
                            </button>
                        </form>
                    </div>
                </div>
            </aside>
            @endauth

            <!-- Main Content Area -->
            <main class="flex-1 overflow-y-auto bg-[#f3f4f6] relative">
                <div class="p-4 lg:p-8">
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

                <!-- Help Button -->
                <button class="fixed bottom-6 right-6 w-12 h-12 bg-gray-800 text-white rounded-full flex items-center justify-center shadow-2xl hover:bg-gray-700 transition">
                    <i class="fas fa-question text-lg"></i>
                </button>
            </main>
        </div>
    </div>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
