@extends('layouts.superadmin')

@section('title', 'Administrator Management | V.O.I.C.E.')

@section('content')
<div class="space-y-8" x-data="adminRatings()">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-6 mb-10">
        <div>
            <nav class="flex text-[10px] uppercase tracking-widest text-outline mb-2 font-bold">
                <span>Administration</span>
                <span class="mx-2">/</span>
                <span class="text-primary">User Management</span>
            </nav>
            <h2 class="text-3xl lg:text-4xl font-black text-primary tracking-tight">Administrator Management</h2>
            <p class="text-on-surface-variant mt-2 max-w-2xl leading-relaxed text-sm lg:text-base">
                Overview and control panel for institution-wide administrative accounts. Manage access levels, monitor activity, and assign departmental leads.
            </p>
        </div>
        <a href="{{ route('superadmin.admins.create') }}" class="w-full lg:w-auto bg-gradient-to-r from-primary to-primary-container text-tertiary-fixed px-6 py-3.5 rounded-xl font-extrabold flex items-center justify-center gap-2 shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-95 transition-all uppercase tracking-widest text-xs">
            <span class="material-symbols-outlined">person_add</span>
            Create New Admin
        </a>
    </div>

    <!-- Filter & Stats Bar -->
    <div class="flex flex-col gap-6 mb-8">
        <div class="overflow-x-auto pb-2 -mx-4 px-4 sm:mx-0 sm:px-0 scrollbar-hide">
            <div class="flex items-center gap-2 min-w-max">
                <div class="flex bg-white/50 rounded-xl p-1 shadow-inner border border-outline-variant/10">
                    <button class="px-5 py-2 bg-white text-primary text-[10px] font-black uppercase tracking-widest rounded-lg shadow-sm">All Admins</button>
                    <button class="px-5 py-2 text-outline text-[10px] font-black uppercase tracking-widest hover:text-primary transition-colors">By Dept</button>
                    <button class="px-5 py-2 text-outline text-[10px] font-black uppercase tracking-widest hover:text-primary transition-colors">Pending</button>
                </div>
                <div class="flex items-center gap-2">
                    <select class="bg-white border-none text-[10px] font-black uppercase tracking-widest rounded-xl px-4 py-2.5 focus:ring-1 focus:ring-primary cursor-pointer shadow-sm">
                        <option>All Departments</option>
                    </select>
                    <select class="bg-white border-none text-[10px] font-black uppercase tracking-widest rounded-xl px-4 py-2.5 focus:ring-1 focus:ring-primary cursor-pointer shadow-sm">
                        <option>Active Status</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="flex justify-between items-center bg-white px-6 py-3 rounded-2xl border border-outline-variant/10 shadow-sm lg:hidden">
            <span class="text-[10px] font-black text-on-surface-variant uppercase tracking-widest">Active Admins</span>
            <div class="flex items-center gap-2">
                <div class="w-2 h-2 rounded-full bg-secondary animate-pulse"></div>
                <span class="text-xs font-black text-primary">{{ $admins->count() }}</span>
            </div>
        </div>
    </div>

    <!-- Admin Directory -->
    <div class="bg-white rounded-[2rem] shadow-sm overflow-hidden border border-outline-variant/10">
        <!-- Mobile Card View -->
        <div class="lg:hidden divide-y divide-outline-variant/10">
            @forelse($admins as $admin)
            <div class="p-6">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex items-center gap-4">
                        <div class="h-12 w-12 rounded-2xl bg-primary/5 flex items-center justify-center font-black text-xs border border-primary/20 overflow-hidden shadow-inner">
                            @if($admin->profile_image)
                                <img src="{{ asset('storage/' . $admin->profile_image) }}" class="h-full w-full object-cover">
                            @else
                                {{ strtoupper(substr($admin->name, 0, 2)) }}
                            @endif
                        </div>
                        <div>
                            <p class="font-black text-primary uppercase tracking-tight text-sm">{{ $admin->name }}</p>
                            <p class="text-[9px] font-bold text-on-surface-variant uppercase tracking-widest">{{ $admin->course ?: config('departments.default_label') }}</p>
                        </div>
                    </div>
                    <span class="bg-primary-container/10 text-primary-container text-[8px] font-black uppercase tracking-wider px-2 py-1 rounded">
                        {{ config('roles.' . $admin->role, $admin->role) }}
                    </span>
                </div>

                <div class="flex justify-between items-center mb-3">
                    <div class="space-y-1">
                        <p class="text-[9px] font-bold text-outline uppercase tracking-widest">Overall Rating</p>
                        <p class="text-sm font-bold text-accent">{{ number_format((float) $admin->avg_rating, 1) }} <span class="text-yellow-400">★</span></p>
                    </div>
                    <div class="space-y-1 text-right">
                        <p class="text-[9px] font-bold text-outline uppercase tracking-widest">Last Active</p>
                        <p class="text-[10px] font-black text-primary">{{ $admin->updated_at->diffForHumans() }}</p>
                    </div>
                </div>
                <div class="flex justify-between items-center mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-secondary-container text-on-secondary-container text-[10px] font-black uppercase tracking-widest">
                        <span class="w-1.5 h-1.5 rounded-full bg-secondary {{ $admin->is_blocked ? '' : 'animate-pulse' }}"></span> 
                        {{ $admin->is_blocked ? 'Suspended' : 'Active' }}
                    </span>
                </div>

                <div class="flex gap-2 pt-4 border-t border-dashed border-outline-variant/10">
                    <a href="#" class="flex-1 py-2.5 bg-gray-50 text-center rounded-xl text-[10px] font-black uppercase tracking-widest text-primary">Edit</a>
                    <a href="{{ route('superadmin.admins.performance', $admin) }}" class="flex-1 py-2.5 bg-gray-50 text-center rounded-xl text-[10px] font-black uppercase tracking-widest text-primary">Score</a>
                    @if(!$admin->is_blocked)
                        <form method="POST" action="{{ route('superadmin.admins.block', $admin) }}" class="flex-none">
                            @csrf
                            <button class="w-10 h-10 bg-red-50 text-error rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">block</span>
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('superadmin.admins.unblock', $admin) }}" class="flex-none">
                            @csrf
                            <button class="w-10 h-10 bg-green-50 text-secondary rounded-xl flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-12 text-center text-zinc-400 font-black uppercase tracking-widest text-[10px] italic">No administrators found.</div>
            @endforelse
        </div>

        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-outline-variant/5">
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-outline">Administrator</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-outline">Department</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-outline">Role</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-outline">Overall Rating</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-outline">Last Active</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-outline">Status</th>
                        <th class="px-8 py-5 text-[10px] font-black uppercase tracking-widest text-outline text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($admins as $admin)
                     <tr class="hover:bg-gray-50/50 transition-colors group" :id="'admin-row-{{ $admin->id }}'">
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-primary/10 flex items-center justify-center font-black text-xs border border-primary/20 overflow-hidden shadow-inner">
                                    @if($admin->profile_image)
                                        <img src="{{ asset('storage/' . $admin->profile_image) }}" class="h-full w-full object-cover">
                                    @else
                                        {{ strtoupper(substr($admin->name, 0, 2)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-primary">{{ $admin->name }}</p>
                                    <p class="text-xs text-on-surface-variant">{{ $admin->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <span class="text-sm font-medium">{{ $admin->course ?: config('departments.default_label') }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="bg-primary-container/10 text-primary-container text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded">
                                {{ config('roles.' . $admin->role, $admin->role) }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <span x-text="ratings[{{ $admin->id }}] !== undefined ? ratings[{{ $admin->id }}] : '{{ number_format((float) $admin->avg_rating, 1) }}'"
                                  class="text-sm font-bold text-accent">
                                {{ number_format((float) $admin->avg_rating, 1) }}
                            </span>
                            <span class="text-yellow-400 ml-1">★</span>
                        </td>
                        <td class="px-8 py-6 text-sm text-on-surface-variant font-medium">
                            {{ $admin->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-8 py-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-secondary-container text-on-secondary-container text-[11px] font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary {{ $admin->is_blocked ? '' : 'animate-pulse' }}"></span> 
                                {{ $admin->is_blocked ? 'Suspended' : 'Active' }}
                            </span>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex justify-end items-center gap-4">
                                <a class="text-xs font-black uppercase tracking-widest text-outline hover:text-primary transition-colors" href="#">Edit</a>
                                <a class="text-xs font-black uppercase tracking-widest text-outline hover:text-primary transition-colors" href="{{ route('superadmin.admins.performance', $admin) }}">Performance</a>
                                @if(!$admin->is_blocked)
                                    <form method="POST" action="{{ route('superadmin.admins.block', $admin) }}" onsubmit="return confirm('Block this administrator?')">
                                        @csrf
                                        <button class="p-1 hover:bg-error-container/20 rounded-md text-outline hover:text-error transition-colors">
                                            <span class="material-symbols-outlined text-lg">block</span>
                                        </button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('superadmin.admins.unblock', $admin) }}" onsubmit="return confirm('Unblock this administrator?')">
                                        @csrf
                                        <button class="p-1 hover:bg-secondary-container/20 rounded-md text-outline hover:text-secondary transition-colors">
                                            <span class="material-symbols-outlined text-lg">check_circle</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-8 py-10 text-center text-zinc-400 italic">No administrators found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-8 py-6 bg-gray-50/30 flex flex-col md:flex-row justify-between items-center gap-6 border-t border-outline-variant/10">
            <p class="text-[10px] font-black text-outline uppercase tracking-widest">Showing {{ $admins->count() }} Administrators</p>
            <div class="flex gap-2">
                {{ $admins->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('adminRatings', () => ({
            ratings: {},
            init() {
                // Initialize ratings from server data
                @foreach($admins as $admin)
                    this.ratings[{{ $admin->id }}] = '{{ number_format((float) $admin->avg_rating, 1) }}';
                @endforeach

                // Listen for live rating updates
                window.addEventListener('complaint-rated', (e) => {
                    const complaint = e.detail.complaint;
                    if (complaint.assigned_to && complaint.rating) {
                        // Mark this admin's rating as needing refresh
                        // We'll refetch the avg rating via a simple fetch
                        this.refreshAdminRating(complaint.assigned_to);
                    }
                });
            },
            refreshAdminRating(adminId) {
                fetch('{{ route('superadmin.admins.avg_rating', ['admin' => ':adminId']) }}'.replace(':adminId', adminId))
                    .then(r => r.json())
                    .then(data => {
                        if (data.avg_rating !== undefined) {
                            this.ratings[adminId] = data.avg_rating;
                        }
                    })
                    .catch(() => {});
            }
        }));
    });
</script>
@endsection
