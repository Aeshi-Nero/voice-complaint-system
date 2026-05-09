@extends("layouts.superadmin")

@section("title", "Complaints Management | Superadmin")

@section("content")
<div class="max-w-full mx-auto space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div>
            <h2 class="text-2xl lg:text-4xl font-black text-[#163a24] tracking-tight uppercase mb-2">Global Complaints</h2>
            <p class="text-gray-500 font-medium text-sm lg:text-base">Monitor and assign complaints across all departments.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('superadmin.complaints.auto_assign') }}" method="POST">
                @csrf
                <button type="submit" class="bg-[#f3bc3e] text-[#163a24] px-6 py-3 rounded-xl font-black uppercase tracking-widest text-xs shadow-lg hover:bg-yellow-400 transition-all flex items-center gap-2">
                    <i class="fas fa-magic"></i> Auto-Assign Load
                </button>
            </form>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('superadmin.complaints.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="relative">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                    <i class="fas fa-search"></i>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search ID, Title or Name..." class="w-full pl-12 pr-4 py-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-[#00a651]">
            </div>
            
            <select name="status" onchange="this.form.submit()" class="bg-gray-50 border-none rounded-xl px-4 py-3 focus:ring-2 focus:ring-[#00a651] font-bold text-[#163a24]">
                <option value="all" {{ request('status') === 'all' ? 'selected' : '' }}>All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolved</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>

            <button type="submit" class="bg-[#163a24] text-white py-3 rounded-xl font-black uppercase tracking-widest text-xs hover:bg-[#1a442a] transition-all">
                Filter Results
            </button>
        </form>
    </div>

    <!-- Complaints Display -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <!-- Desktop Table View -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">ID</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Complaint Info</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Submitter</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Assigned To</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Assignment</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($complaints as $complaint)
                    <tr class="hover:bg-gray-50/50 transition group">
                        <td class="px-8 py-6">
                            <span class="text-[10px] font-black text-gray-300 uppercase tracking-widest">#{{ $complaint->complaint_number }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="font-black text-[#163a24] text-sm">{{ $complaint->title }}</span>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[9px] font-bold text-gray-400 uppercase px-2 py-0.5 bg-gray-100 rounded">{{ $complaint->category }}</span>
                                    <span class="text-[9px] font-bold text-white uppercase px-2 py-0.5 rounded
                                        @if($complaint->status === 'pending') bg-orange-500 @elseif($complaint->status === 'in_progress') bg-blue-500 @elseif($complaint->status === 'resolved') bg-green-500 @else bg-red-500 @endif">
                                        {{ $complaint->status }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <p class="text-xs font-bold text-gray-700">{{ $complaint->user->name }}</p>
                            <p class="text-[10px] text-gray-400">{{ $complaint->user->course }}</p>
                        </td>
                        <td class="px-8 py-6">
                            @if($complaint->assignedTo)
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-primary/10 flex items-center justify-center text-[10px] font-black text-primary border border-primary/20">
                                        {{ strtoupper(substr($complaint->assignedTo->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs font-bold text-primary">{{ $complaint->assignedTo->name }}</span>
                                </div>
                            @else
                                <span class="text-[10px] font-black text-red-400 uppercase tracking-tighter flex items-center gap-1">
                                    <i class="fas fa-exclamation-circle"></i> Unassigned
                                </span>
                            @endif
                        </td>
                        <td class="px-8 py-6 text-right">
                            <form action="{{ route('superadmin.complaints.assign', $complaint) }}" method="POST" class="inline-flex items-center gap-2">
                                @csrf
                                <select name="admin_id" required class="text-[10px] border-gray-200 rounded-lg py-1 px-2 focus:ring-primary">
                                    <option value="">Assign to...</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ $complaint->assigned_to == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="p-2 bg-primary text-white rounded-lg hover:bg-emerald-800 transition-colors shadow-sm">
                                    <i class="fas fa-user-plus text-[10px]"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center text-gray-400 italic">No complaints found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Mobile Card View -->
        <div class="lg:hidden divide-y divide-gray-50">
            @forelse($complaints as $complaint)
                <div class="p-6 space-y-6">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[10px] font-black text-gray-300 uppercase tracking-widest mb-1">ID: #{{ $complaint->complaint_number }}</p>
                            <h3 class="font-black text-[#163a24] text-lg leading-tight">{{ $complaint->title }}</h3>
                        </div>
                        <span class="text-[9px] font-black text-white uppercase px-3 py-1.5 rounded-xl shadow-sm
                            @if($complaint->status === 'pending') bg-orange-500 shadow-orange-500/20 @elseif($complaint->status === 'in_progress') bg-blue-500 shadow-blue-500/20 @elseif($complaint->status === 'resolved') bg-green-500 shadow-green-500/20 @else bg-red-500 shadow-red-500/20 @endif">
                            {{ $complaint->status }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-6 bg-gray-50/50 p-4 rounded-2xl">
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Submitter</p>
                            <p class="text-xs font-black text-[#163a24] truncate">{{ $complaint->user->name }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mb-1">Assigned To</p>
                            @if($complaint->assignedTo)
                                <p class="text-xs font-black text-primary truncate">{{ $complaint->assignedTo->name }}</p>
                            @else
                                <p class="text-[10px] font-black text-red-400 uppercase tracking-tighter italic">Unassigned</p>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('superadmin.complaints.assign', $complaint) }}" method="POST" class="space-y-3">
                        @csrf
                        <div class="flex flex-col gap-3">
                            <label class="text-[9px] font-black text-gray-400 uppercase tracking-widest">Update Assignment</label>
                            <div class="flex gap-2">
                                <select name="admin_id" required class="flex-1 text-xs font-bold border-gray-100 bg-gray-50 rounded-xl py-3 px-4 focus:ring-primary">
                                    <option value="">Select Administrator</option>
                                    @foreach($admins as $admin)
                                        <option value="{{ $admin->id }}" {{ $complaint->assigned_to == $admin->id ? 'selected' : '' }}>{{ $admin->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="w-12 h-12 bg-primary text-white rounded-xl shadow-lg shadow-primary/20 flex items-center justify-center transition-transform active:scale-90">
                                    <i class="fas fa-user-plus text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @empty
                <div class="p-16 text-center text-gray-400 font-black uppercase tracking-widest text-xs italic">No complaints found.</div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $complaints->links() }}
    </div>
</div>
@endsection
