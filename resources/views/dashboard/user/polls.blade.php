@extends('layouts.app')

@section('content')
<div class="max-w-full mx-auto pb-20">
    <!-- Header Section -->
    <div class="mb-12">
        <h2 class="text-4xl font-black text-[#163a24] tracking-tight uppercase mb-2">Polls & Feedback</h2>
        <p class="text-gray-500 font-bold text-sm leading-relaxed max-w-2xl">Your voice shapes our future. Participate in active discussions regarding campus improvements, policy changes, and student life initiatives.</p>
    </div>

    <!-- Active Polls Section -->
    <div class="mb-16">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-lg font-black text-[#163a24] flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-[#f3bc3e]"></span>
                Active Polls
            </h3>
            <a href="#" class="text-[10px] font-black text-gray-400 hover:text-[#163a24] uppercase tracking-widest transition">View All</a>
        </div>

        @if($active_polls->isEmpty())
        <div class="bg-white rounded-[2.5rem] shadow-xl p-16 border border-[#163a24]/5 flex flex-col items-center justify-center text-center">
            <div class="w-20 h-20 bg-[#fef9e1] rounded-3xl flex items-center justify-center mb-8 shadow-inner group-hover:scale-110 transition-transform duration-500">
                <i class="fas fa-poll-h text-3xl text-[#163a24] opacity-20"></i>
            </div>
            <p class="text-2xl font-black text-[#163a24] uppercase tracking-[0.2em] italic">No active polls for today</p>
            <p class="text-gray-400 font-bold text-xs mt-4 uppercase tracking-widest">Check back later for new updates</p>
        </div>
        @else
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @foreach($active_polls as $poll)
            @php
                $flagshipImage = $poll->options->whereNotNull('image_path')->first()?->image_path;
            @endphp
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-[#163a24]/5 flex flex-col h-full overflow-hidden group">
                <div class="relative h-40 w-full overflow-hidden bg-[#163a24]/5">
                    @if($flagshipImage)
                        <img src="{{ asset('storage/' . $flagshipImage) }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-[#fef9e1]">
                            <i class="fas fa-poll text-4xl text-[#163a24]/10"></i>
                        </div>
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-white via-white/40 to-transparent"></div>
                </div>

                <div class="p-8 pt-6 flex flex-col h-full">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex-1">
                        <h4 class="text-xl font-black text-[#163a24] leading-tight mb-2 group-hover:text-[#f3bc3e] transition-colors">{{ $poll->title }}</h4>
                        <p class="text-xs font-bold text-gray-400 leading-relaxed">{{ $poll->description }}</p>
                    </div>
                    @if($poll->expires_at)
                    <span class="bg-[#112d1c] text-[#f3bc3e] px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest whitespace-nowrap ml-4">
                        Ends: {{ $poll->expires_at->format('M d') }}
                    </span>
                    @endif
                </div>

                @if(auth()->user()->hasVotedInPoll($poll->id))
                    {{-- Show Results if already voted --}}
                    <div class="space-y-6 mb-10 flex-1">
                        @foreach($poll->options as $option)
                        @php
                            $percentage = $poll->getTotalVotes() > 0 ? round(($option->votes_count / $poll->getTotalVotes()) * 100) : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-end mb-2">
                                <span class="text-[10px] font-black text-[#163a24] uppercase tracking-widest">{{ $option->option_text }}</span>
                                <span class="text-[10px] font-black text-[#163a24]">{{ $percentage }}%</span>
                            </div>
                            <div class="w-full bg-[#fef9e1] rounded-full h-3 overflow-hidden shadow-inner">
                                <div class="bg-[#163a24] h-full rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>
                        @endforeach
                        <div class="mt-8 flex items-center justify-between pt-6 border-t border-gray-100">
                            <div class="flex items-center gap-3">
                                <i class="fas fa-check-circle text-green-500"></i>
                                <p class="text-[10px] font-black text-green-700 uppercase tracking-widest">You have already voted</p>
                            </div>
                            <a href="{{ route('user.polls.report', $poll) }}" class="text-[10px] font-black text-[#163a24] uppercase tracking-widest flex items-center gap-2 hover:text-[#f3bc3e] transition">
                                Full Report <i class="fas fa-arrow-right text-[8px]"></i>
                            </a>
                        </div>
                    </div>
                @else
                    {{-- Show Vote Form --}}
                    <form action="{{ route('user.polls.vote', $poll) }}" method="POST" class="flex flex-col flex-1">
                        @csrf
                        <div class="space-y-3 mb-10 flex-1">
                            @foreach($poll->options as $option)
                            <label class="flex items-center gap-4 p-5 bg-[#fef9e1] rounded-2xl cursor-pointer hover:bg-[#f2e19d] transition-colors group">
                                <input type="radio" name="option_id" value="{{ $option->id }}" class="w-5 h-5 text-[#163a24] focus:ring-[#f3bc3e] border-none bg-white" required>
                                <span class="text-sm font-black text-[#163a24] group-hover:translate-x-1 transition-transform">{{ $option->option_text }}</span>
                            </label>
                            @endforeach
                        </div>

                        <div class="flex items-center justify-between mt-auto pt-6 border-t border-gray-50">
                            <button type="submit" class="bg-[#f3bc3e] text-[#163a24] px-10 py-4 rounded-xl font-black uppercase tracking-widest shadow-lg hover:bg-yellow-400 transition-all">
                                Vote Now
                            </button>
                        </div>
                    </form>
                @endif
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

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($closed_polls as $poll)
            @php
                $flagshipImage = $poll->options->whereNotNull('image_path')->first()?->image_path;
            @endphp
            <div class="bg-white rounded-[2.5rem] shadow-xl border border-[#163a24]/5 overflow-hidden group flex flex-col h-full transition-transform hover:-translate-y-1">
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
                
                <div class="p-8 pt-6 flex-1 flex flex-col">
                <div class="flex justify-between items-start mb-6">
                    <h4 class="text-md font-black text-[#163a24] leading-tight group-hover:text-[#f3bc3e] transition-colors line-clamp-2">{{ $poll->title }}</h4>
                    <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest whitespace-nowrap ml-4">Closed</span>
                </div>

                <div class="space-y-6 mb-10">
                    @foreach($poll->options as $option)
                    @php
                        $percentage = $poll->getTotalVotes() > 0 ? round(($option->votes_count / $poll->getTotalVotes()) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[10px] font-black text-[#163a24] uppercase tracking-widest">{{ $option->option_text }}</span>
                            <span class="text-[10px] font-black text-[#163a24]">{{ $percentage }}%</span>
                        </div>
                        <div class="w-full bg-[#fef9e1] rounded-full h-2 overflow-hidden">
                            <div class="bg-[#112d1c] h-full rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ number_format($poll->getTotalVotes() ?? 0) }} total votes</p>
                    <a href="{{ route('user.polls.report', $poll) }}" class="text-[10px] font-black text-[#163a24] uppercase tracking-widest flex items-center gap-2 hover:text-[#f3bc3e] transition">
                        Full Report <i class="fas fa-arrow-right text-[8px]"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
