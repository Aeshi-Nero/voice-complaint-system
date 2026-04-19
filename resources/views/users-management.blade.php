@extends("layouts.app")

@section("content")
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-[#163a24]">Users Management</h2>
        <p class="text-gray-500 mt-1">Monitor activity, department distribution, and manage user accounts.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Online Users -->
        <div class="bg-white rounded-[2rem] shadow-sm p-6 border-2 border-[#163a24]/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Active Now</p>
                    <p class="text-3xl font-black text-[#163a24] mt-1">{{ $onlineUsersCount }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center">
                    <div class="relative">
                        <i class="fas fa-users text-green-600 text-xl"></i>
                        <span class="absolute -top-1 -right-1 flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                        </span>
                    </div>
                </div>
            </div>
            <p class="text-[10px] text-green-600 font-bold mt-4 uppercase">Users active in last 5 min</p>
        </div>

        <!-- Banned Users -->
        <div class="bg-white rounded-[2rem] shadow-sm p-6 border-2 border-[#163a24]/5">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-400 text-[10px] font-black uppercase tracking-widest">Banned Users</p>
                    <p class="text-3xl font-black text-red-600 mt-1">{{ $bannedUsers->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-2xl flex items-center justify-center">
                    <i class="fas fa-user-slash text-red-600 text-xl"></i>
                </div>
            </div>
            <p class="text-[10px] text-red-600 font-bold mt-4 uppercase tracking-tighter">Currently restricted accounts</p>
        </div>
<!-- Import Section -->
<div class="lg:col-span-2 bg-[#163a24] rounded-[2rem] shadow-xl p-6 text-white overflow-hidden relative group">
    <div class="relative z-10 flex items-center justify-between h-full">
        <div>
            <h3 class="text-xl font-black uppercase tracking-tight">Bulk Import</h3>
            <p class="text-white/60 text-xs mt-1">Add or update users via JSON list</p>
        </div>
        <form action="{{ route('admin.users.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-3">
            @csrf
            <!-- Excel/CSV Button -->
            <label class="bg-white/10 hover:bg-white/20 border-2 border-dashed border-white/30 rounded-2xl px-6 py-4 cursor-pointer transition-all flex items-center gap-3">
                <i class="fas fa-file-excel text-green-400"></i>
                <span class="text-xs font-black uppercase tracking-widest">Choose Excel/CSV</span>
                <input type="file" name="file" class="hidden" accept=".csv" onchange="this.form.submit()">
            </label>

            <!-- JSON Button -->
            <label class="bg-white/10 hover:bg-white/20 border-2 border-dashed border-white/30 rounded-2xl px-6 py-4 cursor-pointer transition-all flex items-center gap-3">
                <i class="fas fa-file-code text-yellow-400"></i>
                <span class="text-xs font-black uppercase tracking-widest">Choose JSON</span>
                <input type="file" name="file" class="hidden" accept=".json" onchange="this.form.submit()">
            </label>
        </form>    </div>
    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-32 h-32 bg-yellow-400/10 rounded-full blur-2xl"></div>
</div>
</div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Departments Stats -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-[2.5rem] shadow-sm p-8 border-2 border-[#163a24]/5">
                <h3 class="text-lg font-black text-[#163a24] uppercase tracking-tight mb-6 flex items-center gap-3">
                    <i class="fas fa-university text-yellow-400"></i>
                    Departments
                </h3>
                <div class="space-y-4">
                    @forelse($usersPerDepartment as $dept)
                    <div class="group">
                        <div class="flex justify-between text-xs font-black uppercase tracking-widest text-gray-400 mb-2">
                            <span>{{ $dept->course ?: 'No Department' }}</span>
                            <span class="text-[#163a24]">{{ $dept->count }}</span>
                        </div>
                        <div class="h-3 w-full bg-gray-50 rounded-full overflow-hidden border border-gray-100">
                            @php $percentage = ($usersPerDepartment->sum('count') > 0) ? ($dept->count / $usersPerDepartment->sum('count')) * 100 : 0; @endphp
                            <div class="h-full bg-yellow-400 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @empty
                    <p class="text-gray-400 text-center py-4 text-xs font-black uppercase">No department data found</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Banned Users List -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-[2.5rem] shadow-sm overflow-hidden border-2 border-[#163a24]/5">
                <div class="p-8 border-b border-gray-50">
                    <h3 class="text-lg font-black text-[#163a24] uppercase tracking-tight flex items-center gap-3">
                        <i class="fas fa-ban text-red-500"></i>
                        Restricted Accounts
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr class="text-left">
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">User</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-8 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($bannedUsers as $user)
                            <tr class="hover:bg-gray-50/50 transition">
                                <td class="px-8 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl bg-[#163a24]/5 flex items-center justify-center font-black text-[#163a24] text-xs">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <p class="text-xs font-black text-[#163a24] uppercase">{{ $user->name }}</p>
                                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">{{ $user->id_number }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-4">
                                    <div class="flex flex-col">
                                        @if($user->is_blocked)
                                            <span class="text-[10px] font-black text-red-600 uppercase">Permanently Blocked</span>
                                        @elseif($user->banned_until)
                                            <span class="text-[10px] font-black text-orange-600 uppercase">Temporary Ban</span>
                                            <span class="text-[9px] text-gray-400 uppercase tracking-tighter">Until: {{ $user->banned_until->format('M d, Y') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <form action="{{ route('admin.users.unblock', $user) }}" method="POST">
                                        @csrf
                                        <button class="px-4 py-2 bg-[#163a24] text-white text-[9px] font-black uppercase tracking-widest rounded-xl hover:bg-yellow-400 hover:text-[#163a24] transition-all">
                                            Unblock
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-8 py-12 text-center text-gray-400 text-xs font-black uppercase">No restricted accounts found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
