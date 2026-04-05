@extends("layouts.app")

@section("content")
<div class="max-w-7xl mx-auto">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6 mb-8">
        <div>
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">All Complaints</h2>
            <p class="text-gray-500 font-medium">Manage and moderate submitted complaints.</p>
        </div>
        
        <div class="flex flex-wrap items-center gap-2 bg-white p-1.5 rounded-2xl shadow-sm border border-gray-100">
            <a href="{{ route("admin.complaints", ["status" => "all"]) }}" 
               class="px-5 py-2 rounded-xl text-xs font-bold transition {{ $status === "all" ? "bg-[#00a651] text-white shadow-md" : "text-gray-400 hover:text-gray-600 hover:bg-gray-50" }}">
                ALL
            </a>
            <a href="{{ route("admin.complaints", ["status" => "pending"]) }}" 
               class="px-5 py-2 rounded-xl text-xs font-bold transition {{ $status === "pending" ? "bg-orange-500 text-white shadow-md" : "text-gray-400 hover:text-orange-500 hover:bg-orange-50" }}">
                PENDING
            </a>
            <a href="{{ route("admin.complaints", ["status" => "in_progress"]) }}" 
               class="px-5 py-2 rounded-xl text-xs font-bold transition {{ $status === "in_progress" ? "bg-blue-500 text-white shadow-md" : "text-gray-400 hover:text-blue-500 hover:bg-blue-50" }}">
                IN PROGRESS
            </a>
            <a href="{{ route("admin.complaints", ["status" => "resolved"]) }}" 
               class="px-5 py-2 rounded-xl text-xs font-bold transition {{ $status === "resolved" ? "bg-green-600 text-white shadow-md" : "text-gray-400 hover:text-green-600 hover:bg-green-50" }}">
                RESOLVED
            </a>
            <a href="{{ route("admin.complaints", ["status" => "rejected"]) }}" 
               class="px-5 py-2 rounded-xl text-xs font-bold transition {{ $status === "rejected" ? "bg-red-500 text-white shadow-md" : "text-gray-400 hover:text-red-500 hover:bg-red-50" }}">
                REJECTED
            </a>
        </div>
    </div>
    
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Complaint ID</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Category</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Title</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Submitter</th>
                        <th class="px-8 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($complaints as $complaint)
                    <tr class="hover:bg-gray-50/50 transition group cursor-pointer" onclick="window.location='{{ route("admin.complaints.show", $complaint) }}'">
                        <td class="px-8 py-6">
                            <span class="text-xs font-bold text-gray-300 tracking-wider uppercase group-hover:text-gray-500 transition">{{ $complaint->complaint_number }}</span>
                        </td>
                        <td class="px-8 py-6">
                            <span class="px-3 py-1 bg-green-50 text-green-600 text-[10px] font-black uppercase rounded-lg border border-green-100">
                                {{ $complaint->category }}
                            </span>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-800 group-hover:text-[#00a651] transition">{{ $complaint->title }}</span>
                                <span class="text-[10px] font-bold text-gray-400 mt-1 uppercase tracking-tighter">{{ $complaint->created_at->format("M d, Y") }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500">
                                    {{ strtoupper(substr($complaint->user->name, 0, 1)) }}
                                </div>
                                <span class="text-sm font-bold text-gray-600">{{ $complaint->user->name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-6 text-right">
                            <div class="flex items-center justify-end gap-3">
                                @if($complaint->status === 'pending')
                                    <span class="w-2 h-2 rounded-full bg-orange-500 shadow-[0_0_8px_rgba(249,115,22,0.5)]"></span>
                                @elseif($complaint->status === 'in_progress')
                                    <span class="w-2 h-2 rounded-full bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]"></span>
                                @elseif($complaint->status === 'resolved')
                                    <span class="w-2 h-2 rounded-full bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.5)]"></span>
                                @else
                                    <span class="w-2 h-2 rounded-full bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.5)]"></span>
                                @endif
                                <i class="fas fa-chevron-right text-gray-200 group-hover:text-[#00a651] transition group-hover:translate-x-1"></i>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-20 text-center">
                            <div class="bg-gray-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-search text-gray-200 text-xl"></i>
                            </div>
                            <p class="text-gray-400 font-bold uppercase tracking-widest text-xs">No complaints found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="px-8 py-6 bg-gray-50/30 border-t border-gray-100">
            {{ $complaints->appends(["status" => $status])->links() }}
        </div>
    </div>
</div>
@endsection
