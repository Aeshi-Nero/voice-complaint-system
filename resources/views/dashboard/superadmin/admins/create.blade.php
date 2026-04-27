@extends('layouts.superadmin')

@section('title', 'Register New Administrative Account | V.O.I.C.E.')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Breadcrumbs -->
    <nav class="flex items-center gap-2 text-xs font-medium text-outline mb-6">
        <a href="{{ route('superadmin.dashboard') }}" class="hover:text-primary transition-colors">Dashboard</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <a href="{{ route('superadmin.admins.index') }}" class="hover:text-primary transition-colors">User Management</a>
        <span class="material-symbols-outlined text-[14px]">chevron_right</span>
        <span class="text-primary font-bold">New Administrator</span>
    </nav>

    <div class="flex flex-col lg:flex-row gap-12">
        <!-- Introduction Column -->
        <div class="lg:w-1/3">
            <h2 class="text-3xl lg:text-4xl font-black text-primary leading-none tracking-tight mb-6">Register New Administrative Account</h2>
            <p class="text-on-surface-variant leading-relaxed mb-8 text-sm lg:text-base">
                Deploying a new administrative terminal requires precise institutional alignment. Ensure all credentials match the official Faculty Registry.
            </p>
            <div class="bg-surface-container p-6 rounded-xl space-y-4 shadow-sm border border-outline-variant/10">
                <div class="flex items-start gap-4">
                    <span class="material-symbols-outlined text-primary active-pill">verified_user</span>
                    <div>
                        <h4 class="font-bold text-sm text-primary uppercase tracking-wider">Encrypted Records</h4>
                        <p class="text-xs text-on-surface-variant mt-1 leading-relaxed">All administrative actions are logged and timestamped for the institutional audit trail.</p>
                    </div>
                </div>
                <div class="flex items-start gap-4">
                    <span class="material-symbols-outlined text-primary active-pill">security</span>
                    <div>
                        <h4 class="font-bold text-sm text-primary uppercase tracking-wider">Role-Based Access</h4>
                        <p class="text-xs text-on-surface-variant mt-1 leading-relaxed">Permissions define the operational scope within the V.O.I.C.E. ecosystem.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="lg:w-2/3">
            <form action="{{ route('superadmin.admins.store') }}" method="POST" class="ivory-glass p-6 lg:p-10 rounded-2xl shadow-2xl shadow-primary/5 space-y-8 border border-outline-variant/10">
                @csrf
                <!-- Personal Details Section -->
                <section class="space-y-6">
                    <div class="flex items-center gap-2 mb-2">
                        <div class="h-px flex-1 bg-outline-variant/30"></div>
                        <span class="text-[10px] font-bold uppercase tracking-[0.2em] text-outline px-4">Institutional Identity</span>
                        <div class="h-px flex-1 bg-outline-variant/30"></div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="label-md font-bold text-[11px] text-primary uppercase tracking-wider ml-1">Full Name</label>
                            <input name="name" class="w-full bg-surface-container-highest border-b-2 border-transparent focus:border-primary focus:bg-surface-bright transition-all p-4 rounded-t-lg outline-none text-on-surface placeholder:text-outline-variant font-medium" placeholder="Dr. Arthur V. Gate" type="text" required/>
                        </div>
                        <div class="space-y-1">
                            <label class="label-md font-bold text-[11px] text-primary uppercase tracking-wider ml-1">Institutional ID</label>
                            <input name="id_number" class="w-full bg-surface-container-highest border-b-2 border-transparent focus:border-primary focus:bg-surface-bright transition-all p-4 rounded-t-lg outline-none text-on-surface placeholder:text-outline-variant font-medium" placeholder="AC-2024-0000" type="text" required/>
                        </div>
                    </div>
                    <div class="space-y-1">
                        <label class="label-md font-bold text-[11px] text-primary uppercase tracking-wider ml-1">Email Address</label>
                        <input name="email" class="w-full bg-surface-container-highest border-b-2 border-transparent focus:border-primary focus:bg-surface-bright transition-all p-4 rounded-t-lg outline-none text-on-surface placeholder:text-outline-variant font-medium" placeholder="administrator@aldersgate.edu.ph" type="email" required/>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1">
                            <label class="label-md font-bold text-[11px] text-primary uppercase tracking-wider ml-1">Department</label>
                            <select name="course" class="w-full bg-surface-container-highest border-b-2 border-transparent focus:border-primary focus:bg-surface-bright transition-all p-4 rounded-t-lg outline-none text-on-surface appearance-none font-bold">
                                <option value="">General Administration</option>
                                <option value="BSED">Academic Affairs (BSED)</option>
                                <option value="BSIT">IT Bureau (BSIT)</option>
                                <option value="Finance">Finance Office</option>
                            </select>
                        </div>
                        <div class="space-y-1">
                            <label class="label-md font-bold text-[11px] text-primary uppercase tracking-wider ml-1">Initial Password</label>
                            <input name="password" class="w-full bg-surface-container-highest border-b-2 border-transparent focus:border-primary focus:bg-surface-bright transition-all p-4 rounded-t-lg outline-none text-on-surface font-medium" type="password" required/>
                        </div>
                    </div>
                </section>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-4 pt-4">
                    <button class="w-full sm:w-auto px-8 py-3 text-sm font-bold text-primary hover:bg-surface-container-highest transition-colors rounded-lg uppercase tracking-widest" type="button">
                        Cancel
                    </button>
                    <button class="w-full sm:w-auto px-10 py-3 signature-texture text-white text-sm font-bold rounded-lg shadow-lg shadow-primary/20 active:scale-95 transition-all uppercase tracking-widest" type="submit">
                        Create Admin Account
                    </button>
                </div>
            </form>
            <!-- Metadata -->
            <div class="mt-6 flex flex-wrap items-center justify-center gap-6 text-[10px] text-outline font-medium uppercase tracking-[0.1em]">
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">lock</span> 256-bit Encrypted</span>
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">history</span> Auto-Logged</span>
                <span class="flex items-center gap-1"><span class="material-symbols-outlined text-[12px]">gavel</span> Policy Compliant</span>
            </div>
        </div>
    </div>
</div>
@endsection
