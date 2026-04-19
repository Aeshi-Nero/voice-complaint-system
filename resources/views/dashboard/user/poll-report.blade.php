@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <!-- Breadcrumbs/Back Button -->
    <div class="mb-8">
        <a href="{{ route('user.polls') }}" class="text-[10px] font-black text-gray-400 hover:text-[#163a24] uppercase tracking-widest flex items-center gap-2 transition">
            <i class="fas fa-arrow-left text-[8px]"></i> Back to Polls
        </a>
    </div>

    <!-- Header Section -->
    <div class="mb-12 bg-white rounded-[2.5rem] shadow-xl p-10 border border-[#163a24]/5">
        <div class="flex justify-between items-start mb-6">
            <div class="flex-1">
                <h2 class="text-3xl font-black text-[#163a24] tracking-tight uppercase mb-2">{{ $poll->title }}</h2>
                <p class="text-gray-500 font-bold text-sm leading-relaxed">{{ $poll->description }}</p>
            </div>
            <div class="flex flex-col items-end gap-2">
                @if($poll->status === 'active')
                    <span class="bg-[#f3bc3e] text-[#163a24] px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest whitespace-nowrap">
                        Active
                    </span>
                    @if($poll->expires_at)
                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">
                        Ends: {{ $poll->expires_at->format('M d, Y') }}
                    </span>
                    @endif
                @else
                    <span class="bg-gray-200 text-gray-500 px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest whitespace-nowrap">
                        Closed
                    </span>
                    <span class="text-[8px] font-black text-gray-400 uppercase tracking-widest">
                        Results Final
                    </span>
                @endif
            </div>
        </div>
    </div>

    <!-- Detailed Results -->
    <div class="bg-white rounded-[2.5rem] shadow-xl p-10 border border-[#163a24]/5">
        <h3 class="text-xl font-black text-[#163a24] uppercase tracking-widest mb-10 flex items-center gap-3">
            <i class="fas fa-chart-bar text-[#f3bc3e]"></i>
            Detailed Voting Breakdown
        </h3>

        @php
            $totalVotes = $poll->getTotalVotes();
        @endphp

        <div class="space-y-10">
            @foreach($poll->options as $option)
            @php
                $votesCount = $option->votes_count;
                $percentage = $totalVotes > 0 ? round(($votesCount / $totalVotes) * 100, 1) : 0;
            @endphp
            <div class="relative">
                <div class="flex justify-between items-end mb-4">
                    <div class="flex flex-col">
                        <span class="text-sm font-black text-[#163a24] uppercase tracking-widest">{{ $option->option_text }}</span>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ number_format($votesCount) }} votes</span>
                    </div>
                    <span class="text-lg font-black text-[#163a24]">{{ $percentage }}%</span>
                </div>
                
                <div class="w-full bg-[#fef9e1] rounded-full h-4 overflow-hidden shadow-inner border border-[#163a24]/5">
                    <div class="bg-[#163a24] h-full rounded-full transition-all duration-1000 flex items-center justify-end px-2" style="width: {{ $percentage }}%">
                        @if($percentage > 15)
                            <div class="w-1 h-1 bg-white/20 rounded-full"></div>
                        @endif
                    </div>
                </div>
                
                {{-- If user voted for this option, show a marker --}}
                @php
                    $userVote = \App\Models\PollVote::where('poll_id', $poll->id)
                        ->where('user_id', auth()->id())
                        ->where('poll_option_id', $option->id)
                        ->first();
                @endphp
                @if($userVote)
                    <div class="absolute -left-4 top-1/2 -translate-y-1/2 text-green-500" title="Your choice">
                        <i class="fas fa-check-circle text-xs"></i>
                    </div>
                @endif
            </div>
            @endforeach
        </div>

        <div class="mt-16 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-6">
            <div class="flex gap-8">
                <div class="text-center">
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Total Participation</p>
                    <p class="text-xl font-black text-[#163a24]">{{ number_format($totalVotes) }}</p>
                </div>
                <div class="text-center">
                    <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-1">Options Provided</p>
                    <p class="text-xl font-black text-[#163a24]">{{ $poll->options->count() }}</p>
                </div>
            </div>

            @if($poll->status === 'active' && !auth()->user()->hasVotedInPoll($poll->id))
                <a href="{{ route('user.polls') }}" class="bg-[#f3bc3e] text-[#163a24] px-8 py-3 rounded-xl font-black uppercase tracking-widest shadow-lg hover:bg-yellow-400 transition-all flex items-center gap-3">
                    Cast Your Vote <i class="fas fa-vote-yea"></i>
                </a>
            @elseif(auth()->user()->hasVotedInPoll($poll->id))
                <div class="bg-green-50 text-green-700 px-6 py-3 rounded-xl border border-green-100 flex items-center gap-3">
                    <i class="fas fa-check-circle"></i>
                    <span class="text-[10px] font-black uppercase tracking-widest">You have participated in this poll</span>
                </div>
            @endif
        </div>
    </div>
    
    <!-- Analysis Section (Optional Placeholder for Future) -->
    <div class="mt-8 bg-[#fef9e1] rounded-[2rem] p-8 border border-[#163a24]/10">
        <p class="text-[10px] font-black text-[#163a24] uppercase tracking-[0.2em] mb-4 flex items-center gap-2">
            <i class="fas fa-info-circle"></i> Transparency Note
        </p>
        <p class="text-xs font-bold text-[#163a24]/60 leading-relaxed">
            All polls are conducted with student privacy in mind. While your vote is recorded to ensure authenticity, individual choices remain confidential. The results shown here reflect the collective voice of the student body.
        </p>
    </div>
</div>
@endsection
