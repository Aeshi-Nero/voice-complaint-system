@extends(auth()->user()->role === 'superadmin' ? 'layouts.superadmin' : 'layouts.app')

@section("content")
<div class="max-w-7xl mx-auto pb-20" x-data="userManagement()">
    <div class="flex flex-col gap-8 mb-10">
        <div>
            <h2 class="text-3xl lg:text-5xl font-black text-[#163a24] tracking-tight uppercase mb-2">User Registry</h2>
            <p class="text-gray-500 font-medium text-sm lg:text-base italic">Institutional directory and activity monitoring.</p>
        </div>
        
        <div class="bg-[#163a24] rounded-[2rem] md:rounded-3xl shadow-xl p-6 md:p-8 text-white overflow-hidden relative group w-full">
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="text-center md:text-left">
                    <h3 class="text-lg md:text-xl font-black uppercase tracking-tight">Bulk Import</h3>
                    <p class="text-white/40 text-[10px] uppercase font-bold tracking-widest mt-1">Excel, CSV, or JSON</p>
                </div>
                <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="flex gap-2 w-full md:w-auto">
                    @csrf
                    <label class="w-full md:w-auto bg-white/10 hover:bg-white/20 border-2 border-dashed border-white/30 rounded-2xl p-5 cursor-pointer transition-all flex items-center justify-center gap-3 group">
                        <i class="fas fa-file-upload text-yellow-400 text-xl group-hover:scale-110 transition-transform"></i>
                        <span class="text-[10px] font-black uppercase tracking-widest md:hidden">Choose File</span>
                        <input type="file" name="file" class="hidden" accept=".csv,.json" onchange="this.form.submit()">
                    </label>
                </form>
            </div>
            <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-yellow-400/10 rounded-full blur-2xl"></div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
        <div class="bg-white rounded-[2rem] md:rounded-[2.5rem] shadow-sm p-8 border-4 border-white group hover:shadow-xl transition-all">
            <div class="flex items-center justify-between mb-6">
                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Active Now</p>
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center text-green-500 shadow-inner">
                    <i class="fas fa-signal"></i>
                </div>
            </div>
            <div class="flex items-end gap-3">
                <p class="text-4xl md:text-5xl font-black text-[#163a24]">{{ $onlineUsersCount }}</p>
                <span class="text-[10px] font-bold text-green-500 uppercase mb-1.5">Live Session</span>
            </div>
        </div>

        <div class="bg-white rounded-[2rem] md:rounded-[2.5rem] shadow-sm p-8 border-4 border-white group hover:shadow-xl transition-all">
            <div class="flex items-center justify-between mb-6">
                <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Restricted</p>
                <div class="w-10 h-10 bg-red-50 rounded-xl flex items-center justify-center text-red-500 shadow-inner">
                    <i class="fas fa-user-slash"></i>
                </div>
            </div>
            <div class="flex items-end gap-3">
                <p class="text-4xl md:text-5xl font-black text-red-600" x-text="bannedCount">{{ $bannedUsersCount }}</p>
                <span class="text-[10px] font-bold text-red-400 uppercase mb-1.5">Accounts</span>
            </div>
        </div>

        <div class="bg-[#163a24] rounded-[2rem] md:rounded-[2.5rem] shadow-xl p-8 border-4 border-white/5 text-white sm:col-span-2 lg:col-span-1">
            <div class="flex items-center justify-between mb-6">
                <p class="text-white/40 text-[10px] font-black uppercase tracking-widest">Global Reach</p>
                <div class="w-10 h-10 bg-white/10 rounded-xl flex items-center justify-center text-[#f3bc3e]">
                    <i class="fas fa-globe"></i>
                </div>
            </div>
            <div class="flex items-end gap-3">
                <p class="text-4xl md:text-5xl font-black">{{ $usersPerDepartment->count() }}</p>
                <span class="text-[10px] font-bold text-white/40 uppercase mb-1.5">Departments</span>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-6 md:p-8 rounded-[2rem] md:rounded-[2.5rem] shadow-sm border border-gray-100 mb-8">
        <form action="{{ route('admin.users.index') }}" method="GET" class="flex flex-col lg:flex-row gap-4">
            <div class="flex-1 relative">
                <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="search" value="{{ $search }}" placeholder="Search users..." 
                       class="w-full pl-14 pr-6 py-4 bg-gray-50 border-none rounded-2xl font-bold text-[#163a24] focus:ring-2 focus:ring-[#00a651] outline-none text-sm md:text-base">
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 lg:w-1/2">
                <select name="department" class="px-6 py-4 bg-gray-50 border-none rounded-2xl font-bold text-[#163a24] focus:ring-2 focus:ring-[#00a651] outline-none appearance-none text-sm cursor-pointer">
                    <option value="">All Departments</option>
                    @foreach($usersPerDepartment as $dept)
                        <option value="{{ $dept->course }}" {{ $department === $dept->course ? 'selected' : '' }}>{{ $dept->course ?: 'Unknown' }}</option>
                    @endforeach
                </select>

                <select name="status" class="px-6 py-4 bg-gray-50 border-none rounded-2xl font-bold text-[#163a24] focus:ring-2 focus:ring-[#00a651] outline-none appearance-none text-sm cursor-pointer">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All Status</option>
                    <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active Only</option>
                    <option value="banned" {{ $status === 'banned' ? 'selected' : '' }}>Restricted Only</option>
                </select>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        <!-- Main User List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2rem] md:rounded-[3rem] shadow-sm border border-outline-variant/10 overflow-hidden">
                <div class="p-6 md:p-8 border-b border-gray-50 flex justify-between items-center">
                    <h3 class="text-lg md:text-xl font-black text-[#163a24] uppercase tracking-tight">System Users</h3>
                    <span class="text-[10px] font-black text-gray-300 uppercase tracking-[0.2em]">{{ $allUsers->total() }} Records</span>
                </div>
                
                <!-- Mobile User Cards -->
                <div class="lg:hidden divide-y divide-gray-50">
                    @forelse($allUsers as $user)
                    <div class="p-6" x-data="{ isBlocked: {{ $user->is_blocked || ($user->banned_until && $user->banned_until->isFuture()) ? 'true' : 'false' }} }">
                        <div class="flex items-center justify-between mb-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-[#163a24]/5 flex items-center justify-center font-black text-[#163a24] text-sm overflow-hidden">
                                    @if($user->profile_image)
                                        <img src="{{ asset('storage/' . $user->profile_image) }}" class="w-full h-full object-cover">
                                    @else
                                        {{ substr($user->name, 0, 2) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="text-sm font-black text-[#163a24] uppercase">{{ $user->name }}</p>
                                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $user->course ?: 'General' }}</p>
                                </div>
                            </div>
                            
                            <template x-if="isBlocked">
                                <button @click="toggleUserStatus('{{ route('admin.users.unblock', $user) }}', 'unblock', $data)" class="w-10 h-10 bg-[#00a651] text-white rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/20">
                                    <i class="fas fa-undo-alt text-xs"></i>
                                </button>
                            </template>
                            <template x-if="!isBlocked">
                                <button @click="if(confirm('Restrict access for this user?')) toggleUserStatus('{{ route('admin.users.block', $user) }}', 'block', $data)" class="w-10 h-10 bg-red-50 text-red-500 rounded-xl flex items-center justify-center">
                                    <i class="fas fa-user-slash text-xs"></i>
                                </button>
                            </template>
                        </div>
                        <div class="flex justify-between items-center pl-16">
                            <div class="space-y-0.5">
                                <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">{{ $user->id_number }}</p>
                                <p class="text-[9px] text-gray-300 uppercase tracking-tighter">{{ $user->email }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-20 text-center text-gray-300 font-bold italic uppercase tracking-widest text-xs">No matching users</div>
                    @endforelse
                </div>

                <!-- Desktop Table View -->
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Petitioner</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest">Registry ID</th>
                                <th class="px-8 py-5 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Access</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($allUsers as $user)
                            <tr class="hover:bg-gray-50/30 transition group" x-data="{ isBlocked: {{ $user->is_blocked || ($user->banned_until && $user->banned_until->isFuture()) ? 'true' : 'false' }} }">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-[#163a24]/5 flex items-center justify-center font-black text-[#163a24] text-sm shadow-inner group-hover:bg-[#163a24] group-hover:text-white transition-all overflow-hidden">
                                            @if($user->profile_image)
                                                <img src="{{ asset('storage/' . $user->profile_image) }}" class="w-full h-full object-cover">
                                            @else
                                                {{ substr($user->name, 0, 2) }}
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-black text-[#163a24] uppercase">{{ $user->name }}</p>
                                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ $user->course ?: 'General' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="text-xs font-bold text-gray-500">{{ $user->id_number }}</span>
                                    <p class="text-[9px] text-gray-300 mt-0.5">{{ $user->email }}</p>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <template x-if="isBlocked">
                                        <button @click="toggleUserStatus('{{ route('admin.users.unblock', $user) }}', 'unblock', $data)" class="px-4 py-2 bg-[#00a651] text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-emerald-600 transition-all shadow-lg shadow-emerald-500/20">
                                            Restore
                                        </button>
                                    </template>
                                    <template x-if="!isBlocked">
                                        <button @click="if(confirm('Restrict access for this user?')) toggleUserStatus('{{ route('admin.users.block', $user) }}', 'block', $data)" class="px-4 py-2 bg-red-50 text-red-500 text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-red-500 hover:text-white transition-all">
                                            Restrict
                                        </button>
                                    </template>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="p-20 text-center text-gray-300 font-bold italic uppercase tracking-widest">No matching users found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="p-8 bg-gray-50/50 border-t border-gray-100">
                    {{ $allUsers->links() }}
                </div>
            </div>
        </div>


        <!-- Sidebar Section -->
        <div class="space-y-8">
            <!-- Department Distribution -->
            <div class="bg-[#163a24] rounded-[2.5rem] shadow-2xl p-10 text-white relative overflow-hidden group">
                <div class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full -translate-y-16 translate-x-16 blur-2xl"></div>
                <h3 class="text-xl font-black uppercase tracking-tight mb-8">Departmental Load</h3>
                <div class="space-y-6">
                    @foreach($usersPerDepartment as $dept)
                    @php 
                        $total = $usersPerDepartment->sum('count');
                        $percent = $total > 0 ? ($dept->count / $total) * 100 : 0;
                    @endphp
                    <div class="space-y-2">
                        <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                            <span class="text-white/60 truncate pr-4">{{ $dept->course ?: 'Unknown' }}</span>
                            <span class="text-yellow-400">{{ round($percent) }}%</span>
                        </div>
                        <div class="h-1.5 w-full bg-white/10 rounded-full overflow-hidden">
                            <div class="h-full bg-yellow-400 transition-all duration-1000 shadow-[0_0_10px_rgba(243,188,62,0.4)]" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Registry Note -->
            <div class="bg-white rounded-[2.5rem] p-10 border border-outline-variant/10 shadow-sm">
                <div class="w-12 h-12 rounded-2xl bg-primary/5 flex items-center justify-center text-primary text-xl mb-6">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4 class="text-lg font-black text-[#163a24] uppercase tracking-tight mb-4 leading-tight">Data Integrity Protocol</h4>
                <p class="text-sm text-gray-500 leading-relaxed italic">
                    All registry modifications are logged for institutional audit. Restricting access prevents user interaction but preserves historical records.
                </p>
            </div>
        </div>
    </div>
</div>

<script>
function userManagement() {
    return {
        bannedCount: {{ $bannedUsersCount }},
        async toggleUserStatus(url, type, rowData) {
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.ok) {
                    rowData.isBlocked = (type === 'block');
                    if (type === 'block') {
                        this.bannedCount++;
                    } else {
                        this.bannedCount--;
                    }
                } else {
                    const data = await response.json();
                    alert(data.message || 'Action failed');
                }
            } catch (e) {
                console.error('Toggle status error:', e);
            }
        }
    }
}
</script>
@endsection
