@extends("layouts.app")

@section("content")
<div class="max-w-full mx-auto pb-20">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-10">
        <div>
            <h2 class="text-4xl font-black text-[#163a24] tracking-tight uppercase">Polls Management</h2>
            <p class="text-gray-500 font-bold text-sm mt-1">Create, monitor, and analyze campus-wide feedback initiatives.</p>
        </div>
        
        <a href="{{ route('admin.polls.create') }}" class="px-8 py-4 bg-[#163a24] text-white rounded-2xl hover:bg-[#1a442a] font-black uppercase tracking-widest shadow-xl transition-all flex items-center gap-3">
            <i class="fas fa-plus-circle"></i>
            <span>Create New Poll</span>
        </a>
    </div>

    <!-- Polls Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @forelse($polls as $poll)
        <div class="bg-white rounded-[2.5rem] shadow-xl p-10 border border-[#163a24]/5 flex flex-col h-full relative overflow-hidden group">
            <!-- Status Badge -->
            <div class="absolute top-0 right-0 mt-8 mr-8">
                @if($poll->status === 'active')
                    <span class="bg-green-50 text-green-600 px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest flex items-center gap-2">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                        Active
                    </span>
                @else
                    <span class="bg-gray-50 text-gray-400 px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest">
                        Closed
                    </span>
                @endif
            </div>

            <div class="mb-8">
                <h4 class="text-xl font-black text-[#163a24] leading-tight mb-2 group-hover:text-[#f3bc3e] transition-colors">{{ $poll->title }}</h4>
                <p class="text-xs font-bold text-gray-400 line-clamp-2 leading-relaxed">{{ $poll->description }}</p>
            </div>

            <!-- Stats Mini Grid -->
            <div class="grid grid-cols-2 gap-4 mb-8">
                <div class="bg-[#fef9e1] p-4 rounded-2xl">
                    <p class="text-[8px] font-black text-[#163a24]/40 uppercase tracking-widest mb-1">Total Votes</p>
                    <p class="text-lg font-black text-[#163a24]">{{ number_format($poll->getTotalVotes()) }}</p>
                </div>
                <div class="bg-[#fef9e1] p-4 rounded-2xl">
                    <p class="text-[8px] font-black text-[#163a24]/40 uppercase tracking-widest mb-1">Options</p>
                    <p class="text-lg font-black text-[#163a24]">{{ $poll->options->count() }}</p>
                </div>
            </div>

            <div class="mt-auto space-y-4 pt-6 border-t border-gray-50">
                <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest">
                    <span class="text-gray-300">Expires: {{ $poll->expires_at->format('M d, Y') }}</span>
                    <span class="text-[#163a24]">By: {{ $poll->creator->name }}</span>
                </div>

                <div class="flex items-center gap-2 pt-2">
                    @if($poll->status === 'active')
                    <form action="{{ route('admin.polls.close', $poll) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="w-full py-3 bg-[#f2e19d] text-[#163a24] rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-[#f3bc3e] transition-all">
                            Close Poll
                        </button>
                    </form>
                    @endif
                    <form action="{{ route('admin.polls.destroy', $poll) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this poll?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full py-3 bg-red-50 text-red-600 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-red-100 transition-all">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-[2.5rem] p-20 text-center border border-dashed border-gray-200">
            <i class="fas fa-poll-h text-4xl text-gray-100 mb-6"></i>
            <p class="text-gray-400 font-black uppercase tracking-widest">No polls have been created yet</p>
            <a href="{{ route('admin.polls.create') }}" class="mt-6 inline-block text-[#163a24] font-black text-xs uppercase tracking-widest hover:text-[#f3bc3e] transition">
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
