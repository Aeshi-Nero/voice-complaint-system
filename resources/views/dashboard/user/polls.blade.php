@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto pb-20">
    <!-- Header Section -->
    <div class="mb-8 lg:mb-12">
        <h2 class="text-2xl lg:text-4xl font-black text-[#163a24] tracking-tight uppercase mb-2">Polls & Feedback</h2>
        <p class="text-gray-500 font-bold text-[10px] lg:text-sm leading-relaxed max-w-2xl">Your voice shapes our future. Participate in discussions regarding campus improvements and student life.</p>
    </div>

    <!-- Active Polls Section -->
    <div class="mb-12 lg:mb-16">
        <div class="flex items-center justify-between mb-6 lg:mb-8">
            <h3 class="text-base lg:text-lg font-black text-[#163a24] flex items-center gap-2 lg:gap-3">
                <span class="w-1.5 lg:w-2 h-1.5 lg:h-2 rounded-full bg-[#f3bc3e]"></span>
                Active Polls
            </h3>
            <a href="#" class="text-[9px] lg:text-[10px] font-black text-gray-400 hover:text-[#163a24] uppercase tracking-widest transition">View All</a>
        </div>

        @if($active_polls->isEmpty())
        <div class="bg-white rounded-3xl lg:rounded-[2.5rem] shadow-xl p-12 lg:p-16 border border-[#163a24]/5 flex flex-col items-center justify-center text-center">
            <div class="w-16 lg:w-20 h-16 lg:h-20 bg-[#fef9e1] rounded-2xl lg:rounded-3xl flex items-center justify-center mb-6 lg:mb-8 shadow-inner group-hover:scale-110 transition-transform duration-500">
                <i class="fas fa-poll-h text-2xl lg:text-3xl text-[#163a24] opacity-20"></i>
            </div>
            <p class="text-lg lg:text-2xl font-black text-[#163a24] uppercase tracking-[0.2em] italic">No active polls</p>
            <p class="text-gray-400 font-bold text-[10px] lg:text-xs mt-2 lg:mt-4 uppercase tracking-widest">Check back later for updates</p>
        </div>
        @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 items-start">
            @foreach($active_polls as $poll)
            @php
                $flagshipImage = $poll->options->whereNotNull('image_path')->first()?->image_path;
            @endphp
            <div class="bg-white rounded-3xl shadow-xl border border-[#163a24]/5 overflow-hidden group flex flex-col"
                 x-data="{ 
                    votes: { @foreach($poll->options as $option) '{{ $option->id }}': {{ $option->votes_count }}, @endforeach },
                    total: {{ $poll->getTotalVotes() }},
                    getPercentage(optionId) {
                        if (this.total === 0) return 0;
                        return Math.round((this.votes[optionId] / this.total) * 100);
                    }
                 }"
                 x-init="
                    window.Echo.channel('poll.{{ $poll->id }}')
                        .listen('PollVoteCast', (e) => {
                            this.votes = e.results;
                            this.total = Object.values(e.results).reduce((a, b) => a + b, 0);
                        });
                 ">
                <!-- Image Header -->
                <div class="relative h-32 lg:h-40 w-full overflow-hidden bg-[#163a24]/5">
                    @if($flagshipImage)
                        <img src="{{ asset('storage/' . $flagshipImage) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-[#fef9e1]">
                            <i class="fas fa-poll text-3xl lg:text-4xl text-[#163a24]/10"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-white via-white/40 to-transparent"></div>
                </div>

                <div class="p-5 lg:p-6 pt-4 flex flex-col">
                    <!-- Title & Badge -->
                    <div class="flex justify-between items-start mb-4 lg:mb-6">
                        <div class="flex-1 min-w-0">
                            <h4 class="text-base lg:text-xl font-black text-[#163a24] leading-tight mb-1 lg:mb-2 group-hover:text-[#f3bc3e] transition-colors truncate">{{ $poll->title }}</h4>
                            <p class="text-[11px] lg:text-xs font-bold text-gray-400 leading-relaxed line-clamp-2">{{ $poll->description }}</p>
                        </div>
                        @if($poll->expires_at)
                        <span class="bg-[#112d1c] text-[#f3bc3e] px-3 lg:px-4 py-1 rounded-lg lg:rounded-xl text-[7px] lg:text-[8px] font-black uppercase tracking-widest whitespace-nowrap ml-3 lg:ml-4">
                            Ends: {{ $poll->expires_at->format('M d') }}
                        </span>
                        @endif
                    </div>

                    @if(auth()->user()->hasVotedInPoll($poll->id))
                        {{-- Results View --}}
                        <div class="space-y-4 lg:space-y-5 mb-6">
                            @foreach($poll->options as $option)
                            <div>
                                <div class="flex justify-between items-end mb-1">
                                    <span class="text-[9px] lg:text-[10px] font-black text-[#163a24] uppercase tracking-widest truncate max-w-[80%]">{{ $option->option_text }}</span>
                                    <span class="text-[9px] lg:text-[10px] font-black text-[#163a24]" x-text="getPercentage('{{ $option->id }}') + '%'"></span>
                                </div>
                                <div class="w-full bg-[#fef9e1] rounded-full h-2 overflow-hidden shadow-inner">
                                    <div class="bg-[#163a24] h-full rounded-full transition-all duration-1000" :style="'width: ' + getPercentage('{{ $option->id }}') + '%'"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>

                        <!-- Footer -->
                        <div class="mt-4 flex items-center justify-between pt-4 border-t border-gray-100">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-green-500 text-xs lg:text-sm"></i>
                                <p class="text-[9px] lg:text-[10px] font-black text-green-700 uppercase tracking-widest">Voted</p>
                            </div>
                            <a href="{{ route('user.polls.report', $poll) }}" class="text-[9px] lg:text-[10px] font-black text-[#163a24] uppercase tracking-widest flex items-center gap-1 hover:text-[#f3bc3e] transition">
                                Report <i class="fas fa-arrow-right text-[7px]"></i>
                            </a>
                        </div>
                    @else
                        {{-- Voting Form --}}
                        <form action="{{ route('user.polls.vote', $poll) }}" method="POST" class="space-y-3">
                            @csrf
                            <div class="space-y-2 mb-6">
                                @foreach($poll->options as $option)
                                <label class="flex items-center gap-3 p-3 lg:p-4 bg-[#fef9e1] rounded-xl lg:rounded-2xl cursor-pointer hover:bg-[#f2e19d] transition-colors group">
                                    <input type="radio" name="option_id" value="{{ $option->id }}" class="w-4 h-4 text-[#163a24] focus:ring-[#f3bc3e] border-none bg-white" required>
                                    <span class="text-[11px] lg:text-sm font-black text-[#163a24] group-hover:translate-x-1 transition-transform">{{ $option->option_text }}</span>
                                </label>
                                @endforeach
                            </div>

                            <div class="pt-4 border-t border-gray-100">
                                <button type="submit" class="w-full lg:w-auto bg-[#f3bc3e] text-[#163a24] px-6 py-2.5 rounded-xl font-black uppercase tracking-widest shadow-lg hover:bg-yellow-400 transition-all text-[10px] lg:text-xs">
                                    Vote Now
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <!-- Closed Polls Section -->
    <div class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-lg font-black text-[#163a24] flex items-center gap-3">
                <i class="fas fa-history text-[#f3bc3e]"></i>
                Closed Polls & Results
            </h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 items-start">
            @foreach($closed_polls as $poll)
            @php
                $flagshipImage = $poll->options->whereNotNull('image_path')->first()?->image_path;
            @endphp
            <div class="bg-white rounded-3xl shadow-xl border border-[#163a24]/5 overflow-hidden group flex flex-col transition-transform hover:-translate-y-1">
                <div class="relative h-32 w-full overflow-hidden bg-[#163a24]/5">
                    @if($flagshipImage)
                        <img src="{{ asset('storage/' . $flagshipImage) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 opacity-60 grayscale">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-[#fef9e1]">
                            <i class="fas fa-poll text-3xl text-[#163a24]/10"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-white via-white/5 to-transparent"></div>
                </div>
                
                <div class="p-5 lg:p-6 pt-4 flex flex-col">
                    <div class="flex justify-between items-start mb-4 lg:mb-6">
                        <h4 class="text-sm lg:text-base font-black text-[#163a24] leading-tight group-hover:text-[#f3bc3e] transition-colors line-clamp-2">{{ $poll->title }}</h4>
                        <span class="text-[7px] lg:text-[8px] font-black text-gray-300 uppercase tracking-widest whitespace-nowrap ml-4">Closed</span>
                    </div>

                    <div class="space-y-4 mb-6">
                        @foreach($poll->options as $option)
                        @php
                            $percentage = $poll->getTotalVotes() > 0 ? round(($option->votes_count / $poll->getTotalVotes()) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-end mb-1">
                                <span class="text-[9px] lg:text-[10px] font-black text-[#163a24] uppercase tracking-widest truncate max-w-[80%]">{{ $option->option_text }}</span>
                                <span class="text-[9px] lg:text-[10px] font-black text-[#163a24]">{{ $percentage }}%</span>
                            </div>
                            <div class="w-full bg-[#fef9e1] rounded-full h-1.5 lg:h-2 overflow-hidden">
                                <div class="bg-[#112d1c] h-full rounded-full" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="mt-auto flex items-center justify-between pt-4 border-t border-gray-50">
                        <p class="text-[9px] lg:text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ number_format($poll->getTotalVotes() ?? 0) }} votes</p>
                        <a href="{{ route('user.polls.report', $poll) }}" class="text-[9px] lg:text-[10px] font-black text-[#163a24] uppercase tracking-widest flex items-center gap-1 hover:text-[#f3bc3e] transition">
                            Report <i class="fas fa-arrow-right text-[7px]"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
