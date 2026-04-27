@extends('layouts.superadmin')

@section('title', 'Administrator Management | V.O.I.C.E.')

@section('content')
<div class="space-y-8">
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
    <div class="flex flex-col lg:flex-row justify-between items-center gap-6 mb-8">
        <div class="flex flex-wrap items-center gap-4 w-full lg:w-auto">
            <div class="flex bg-white/50 rounded-xl p-1 shadow-inner border border-outline-variant/10">
                <button class="px-4 lg:px-6 py-2 bg-white text-primary text-xs lg:text-sm font-bold rounded-lg shadow-sm">All Administrators</button>
                <button class="px-4 lg:px-6 py-2 text-outline text-xs lg:text-sm font-bold hover:text-primary transition-colors">By Department</button>
                <button class="px-4 lg:px-6 py-2 text-outline text-xs lg:text-sm font-bold hover:text-primary transition-colors">Pending Access</button>
            </div>
            <div class="hidden lg:block h-10 w-px bg-outline-variant/30"></div>
            <div class="flex items-center gap-3 w-full lg:w-auto">
                <select class="flex-1 lg:flex-none bg-white border-none text-sm font-semibold rounded-lg px-4 py-2 focus:ring-1 focus:ring-primary cursor-pointer shadow-sm">
                    <option>All Departments</option>
                    <option>Academic Affairs</option>
                    <option>Student Services</option>
                    <option>Finance</option>
                </select>
                <select class="flex-1 lg:flex-none bg-white border-none text-sm font-semibold rounded-lg px-4 py-2 focus:ring-1 focus:ring-primary cursor-pointer shadow-sm">
                    <option>Active Status</option>
                    <option>Inactive</option>
                </select>
            </div>
        </div>
        <div class="flex justify-end gap-3 w-full lg:w-auto">
            <div class="bg-white px-4 py-2 rounded-xl flex items-center gap-3 border border-outline-variant/10 shadow-sm">
                <div class="w-2 h-2 rounded-full bg-secondary animate-pulse"></div>
                <span class="text-xs font-bold text-on-surface-variant">{{ $admins->count() }} Active Admins</span>
            </div>
        </div>
    </div>

    <!-- Admin Directory Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-outline-variant/10">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse min-w-[900px]">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-outline-variant/5">
                        <th class="px-6 py-4 text-[11px] uppercase tracking-widest text-outline font-black">Administrator</th>
                        <th class="px-6 py-4 text-[11px] uppercase tracking-widest text-outline font-black">Department</th>
                        <th class="px-6 py-4 text-[11px] uppercase tracking-widest text-outline font-black">Role</th>
                        <th class="px-6 py-4 text-[11px] uppercase tracking-widest text-outline font-black">Last Active</th>
                        <th class="px-6 py-4 text-[11px] uppercase tracking-widest text-outline font-black">Status</th>
                        <th class="px-6 py-4 text-[11px] uppercase tracking-widest text-outline font-black text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-outline-variant/10">
                    @forelse($admins as $admin)
                    <tr class="hover:bg-gray-50/50 transition-colors group">
                        <td class="px-6 py-5">
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
                        <td class="px-6 py-5">
                            <span class="text-sm font-medium">{{ $admin->course ?: 'General Administration' }}</span>
                        </td>
                        <td class="px-6 py-5">
                            <span class="bg-primary-container/10 text-primary-container text-[10px] font-black uppercase tracking-wider px-2 py-1 rounded">
                                {{ $admin->role === 'superadmin' ? 'Superadmin' : 'Lead Admin' }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-sm text-on-surface-variant font-medium">
                            {{ $admin->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-5">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-secondary-container text-on-secondary-container text-[11px] font-bold">
                                <span class="w-1.5 h-1.5 rounded-full bg-secondary {{ $admin->is_blocked ? '' : 'animate-pulse' }}"></span> 
                                {{ $admin->is_blocked ? 'Suspended' : 'Active' }}
                            </span>
                        </td>
                        <td class="px-6 py-5 text-right">
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
                        <td colspan="6" class="px-6 py-10 text-center text-zinc-400 italic">No administrators found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50/30 flex justify-between items-center border-t border-outline-variant/10">
            <p class="text-xs font-bold text-outline uppercase tracking-widest">Showing {{ $admins->count() }} Administrators</p>
            <div class="flex gap-2">
                {{ $admins->links() }}
            </div>
        </div>
    </div>

    <!-- Activity Insight (Bento Grid Style) -->
    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-primary text-tertiary-fixed p-6 rounded-2xl shadow-lg relative overflow-hidden group">
            <div class="relative z-10">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] opacity-60 mb-2">System Load</p>
                <h3 class="text-2xl font-black mb-4">Lead Response Time</h3>
                <div class="flex items-end gap-2">
                    <span class="text-4xl font-black">1.2h</span>
                    <span class="text-xs font-bold bg-secondary/30 text-secondary-fixed-dim px-2 py-0.5 rounded-full mb-1">▼ 15%</span>
                </div>
            </div>
            <span class="material-symbols-outlined absolute -right-4 -bottom-4 text-9xl opacity-5 group-hover:scale-110 transition-transform duration-500">speed</span>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-outline-variant/20 shadow-sm flex flex-col justify-between">
            <div>
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-outline mb-2">Pending Audits</p>
                <h3 class="text-2xl font-black text-primary mb-1">3 Accounts</h3>
                <p class="text-sm text-on-surface-variant leading-tight">Require permission elevation approval by end of week.</p>
            </div>
            <div class="mt-4 flex -space-x-2">
                <div class="w-8 h-8 rounded-full border-2 border-white bg-primary text-white flex items-center justify-center text-[10px] font-black">A</div>
                <div class="w-8 h-8 rounded-full border-2 border-white bg-secondary text-white flex items-center justify-center text-[10px] font-black">B</div>
                <div class="w-8 h-8 rounded-full bg-primary-container text-white text-[10px] flex items-center justify-center font-bold border-2 border-white">+1</div>
            </div>
        </div>
        <div class="bg-white p-6 rounded-2xl border border-outline-variant/20 shadow-sm">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-[0.2em] text-outline mb-2">Security Note</p>
                    <h3 class="text-xl font-black text-primary leading-tight">MFA Compliance</h3>
                </div>
                <div class="p-2 bg-secondary/10 rounded-lg">
                    <span class="material-symbols-outlined text-secondary">verified_user</span>
                </div>
            </div>
            <div class="mt-4 w-full bg-primary/5 h-1.5 rounded-full overflow-hidden">
                <div class="bg-secondary h-full rounded-full" style="width: 92%"></div>
            </div>
            <p class="mt-2 text-[11px] font-bold text-on-surface-variant">92% of admins have enabled Multi-Factor Authentication.</p>
        </div>
    </div>
</div>
@endsection
