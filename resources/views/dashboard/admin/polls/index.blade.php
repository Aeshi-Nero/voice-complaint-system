@extends("layouts.app")

@section("content")
<div class="max-w-full mx-auto pb-20">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8 lg:mb-10">
        <div>
            <h2 class="text-2xl lg:text-4xl font-black text-[#163a24] tracking-tight uppercase">Polls Management</h2>
            <p class="text-gray-500 font-bold text-xs lg:text-sm mt-1">Create, monitor, and analyze campus-wide feedback initiatives.</p>
        </div>
        
        <a href="{{ route('admin.polls.create') }}" class="w-full lg:w-auto px-6 lg:px-8 py-3 lg:py-4 bg-[#163a24] text-white rounded-xl lg:rounded-2xl hover:bg-[#1a442a] font-black uppercase tracking-widest shadow-xl transition-all flex items-center justify-center gap-3">
            <i class="fas fa-plus-circle"></i>
            <span class="text-xs lg:text-base">Create New Poll</span>
        </a>
    </div>

    <!-- Polls Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
        @forelse($polls as $poll)
        <div class="bg-white rounded-3xl lg:rounded-[2.5rem] shadow-xl p-6 lg:p-10 border border-[#163a24]/5 flex flex-col h-full relative overflow-hidden group">
            <!-- Status Badge -->
            <div class="absolute top-0 right-0 mt-6 lg:mt-8 mr-6 lg:mr-8">
                @if($poll->status === 'active')
                    <span class="bg-green-50 text-green-600 px-3 lg:px-4 py-1.5 rounded-xl text-[7px] lg:text-[8px] font-black uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        Active
                    </span>
                @else
                    <span class="bg-gray-50 text-gray-400 px-3 lg:px-4 py-1.5 rounded-xl text-[7px] lg:text-[8px] font-black uppercase tracking-widest">
                        Closed
                    </span>
                @endif
            </div>

            <div class="mb-6 lg:mb-8">
                <h4 class="text-lg lg:text-xl font-black text-[#163a24] leading-tight mb-2 group-hover:text-[#f3bc3e] transition-colors">{{ $poll->title }}</h4>
                <p class="text-[10px] lg:text-xs font-bold text-gray-400 line-clamp-2 leading-relaxed">{{ $poll->description }}</p>
            </div>

            <!-- Stats Mini Grid -->
            <div class="grid grid-cols-2 gap-3 lg:gap-4 mb-6 lg:mb-8">
                <div class="bg-[#fef9e1] p-3 lg:p-4 rounded-xl lg:rounded-2xl">
                    <p class="text-[7px] lg:text-[8px] font-black text-[#163a24]/40 uppercase tracking-widest mb-1">Total Votes</p>
                    <p class="text-base lg:text-lg font-black text-[#163a24]">{{ number_format($poll->getTotalVotes()) }}</p>
                </div>
                <div class="bg-[#fef9e1] p-3 lg:p-4 rounded-xl lg:rounded-2xl">
                    <p class="text-[7px] lg:text-[8px] font-black text-[#163a24]/40 uppercase tracking-widest mb-1">Options</p>
                    <p class="text-base lg:text-lg font-black text-[#163a24]">{{ $poll->options->count() }}</p>
                </div>
            </div>

            <div class="mt-auto space-y-4 pt-4 lg:pt-6 border-t border-gray-50">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 text-[8px] lg:text-[10px] font-black uppercase tracking-widest">
                    <span class="text-gray-300">Expires: {{ $poll->expires_at->format('M d, Y') }}</span>
                    <span class="text-[#163a24] truncate max-w-full">By: {{ $poll->creator->name }}</span>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    @if($poll->status === 'active')
                    <form action="{{ route('admin.polls.close', $poll) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-2.5 lg:py-3 bg-[#f2e19d] text-[#163a24] rounded-xl font-black text-[9px] lg:text-[10px] uppercase tracking-widest hover:bg-[#f3bc3e] transition-all">
                            Close
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('admin.polls.destroy', $poll) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this poll?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-2.5 lg:py-3 bg-red-50 text-red-600 rounded-xl font-black text-[9px] lg:text-[10px] uppercase tracking-widest hover:bg-red-100 transition-all">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-3xl lg:rounded-[2.5rem] p-12 lg:p-20 text-center border border-dashed border-gray-200">
            <i class="fas fa-poll-h text-3xl lg:text-4xl text-gray-100 mb-6"></i>
            <p class="text-gray-400 font-black uppercase tracking-widest text-xs lg:text-sm">No polls have been created yet</p>
            <a href="{{ route('admin.polls.create') }}" class="mt-6 inline-block text-[#163a24] font-black text-[10px] lg:text-xs uppercase tracking-widest hover:text-[#f3bc3e] transition">
                Create your first poll <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-12">
        {{ $polls->links() }}
    </div>
</div>
@endsection
