@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto pb-20">
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

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @foreach($active_polls as $poll)
            <div class="bg-white rounded-[2.5rem] shadow-xl p-10 border border-[#163a24]/5 flex flex-col h-full">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex-1">
                        <h4 class="text-2xl font-black text-[#163a24] leading-tight mb-2">{{ $poll->title }}</h4>
                        <p class="text-sm font-bold text-gray-400 leading-relaxed">{{ $poll->description }}</p>
                    </div>
                    @if($poll->expires_at)
                    <span class="bg-[#112d1c] text-[#f3bc3e] px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest whitespace-nowrap ml-4">
                        Ends: {{ $poll->expires_at->format('M d') }}
                    </span>
                    @endif
                </div>

                <div class="space-y-3 mb-10 flex-1">
                    @foreach($poll->options as $option)
                    <label class="flex items-center gap-4 p-5 bg-[#fef9e1] rounded-2xl cursor-pointer hover:bg-[#f2e19d] transition-colors group">
                        <input type="radio" name="poll_{{ $poll->id }}" value="{{ $option->id }}" class="w-5 h-5 text-[#163a24] focus:ring-[#f3bc3e] border-none bg-white">
                        <span class="text-sm font-black text-[#163a24] group-hover:translate-x-1 transition-transform">{{ $option->label }}</span>
                    </label>
                    @endforeach
                </div>

                <div class="flex items-center justify-between mt-auto pt-6 border-t border-gray-50">
                    <div class="flex -space-x-3 overflow-hidden">
                        <img class="inline-block h-10 w-10 rounded-xl ring-4 ring-white object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                        <img class="inline-block h-10 w-10 rounded-xl ring-4 ring-white object-cover" src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=facearea&facepad=2.25&w=256&h=256&q=80" alt="">
                        <img class="inline-block h-10 w-10 rounded-xl ring-4 ring-white object-cover" src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=facearea&facepad=2&w=256&h=256&q=80" alt="">
                        <div class="flex items-center justify-center h-10 w-10 rounded-xl bg-[#112d1c] text-[#f3bc3e] text-[10px] font-black ring-4 ring-white">+124</div>
                    </div>
                    <button class="bg-[#f3bc3e] text-[#163a24] px-10 py-4 rounded-xl font-black uppercase tracking-widest shadow-lg hover:bg-yellow-400 transition-all">
                        Vote Now
                    </button>
                </div>
            </div>
            @endforeach

            <!-- Placeholder if no active polls (to match design) -->
            @if($active_polls->isEmpty())
            <div class="bg-white rounded-[2.5rem] shadow-xl p-10 border border-[#163a24]/5">
                <div class="flex justify-between items-start mb-6">
                    <div class="flex-1">
                        <h4 class="text-2xl font-black text-[#163a24] leading-tight mb-2">New Cafeteria Menu Options</h4>
                        <p class="text-sm font-bold text-gray-400 leading-relaxed">Choose the primary focus for next semester's culinary rotation at the Central Hub.</p>
                    </div>
                    <span class="bg-[#112d1c] text-[#f3bc3e] px-4 py-1.5 rounded-xl text-[8px] font-black uppercase tracking-widest whitespace-nowrap ml-4">
                        Ends: Oct 24
                    </span>
                </div>

                <div class="space-y-3 mb-10 flex-1">
                    <label class="flex items-center gap-4 p-5 bg-[#fef9e1] rounded-2xl cursor-pointer hover:bg-[#f2e19d] transition-colors group">
                        <div class="w-5 h-5 rounded-full border-4 border-[#163a24] bg-white flex items-center justify-center">
                            <div class="w-2 h-2 rounded-full bg-[#163a24]"></div>
                        </div>
                        <span class="text-sm font-black text-[#163a24]">Healthy Bowls & Salads</span>
                    </label>
                    <label class="flex items-center gap-4 p-5 bg-[#fef9e1] rounded-2xl cursor-pointer hover:bg-[#f2e19d] transition-colors group">
                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 bg-white"></div>
                        <span class="text-sm font-black text-[#163a24]/60">Authentic Pasta Bar</span>
                    </label>
                    <label class="flex items-center gap-4 p-5 bg-[#fef9e1] rounded-2xl cursor-pointer hover:bg-[#f2e19d] transition-colors group">
                        <div class="w-5 h-5 rounded-full border-2 border-gray-300 bg-white"></div>
                        <span class="text-sm font-black text-[#163a24]/60">Gourmet Street Food</span>
                    </label>
                </div>

                <div class="flex items-center justify-between mt-auto pt-6 border-t border-gray-50">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">428 students have voted</p>
                    <button class="bg-[#f3bc3e] text-[#163a24] px-10 py-4 rounded-xl font-black uppercase tracking-widest shadow-lg hover:bg-yellow-400 transition-all">
                        Vote Now
                    </button>
                </div>
            </div>
            @endif
        </div>
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
            <div class="bg-white rounded-[2.5rem] shadow-xl p-10 border border-[#163a24]/5">
                <div class="flex justify-between items-start mb-8">
                    <h4 class="text-lg font-black text-[#163a24] leading-tight">{{ $poll->title }}</h4>
                    <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest">Closed</span>
                </div>

                <div class="space-y-6 mb-10">
                    @foreach($poll->options as $option)
                    @php
                        $percentage = $poll->total_votes > 0 ? round(($option->votes_count / $poll->total_votes) * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[10px] font-black text-[#163a24] uppercase tracking-widest">{{ $option->label }}</span>
                            <span class="text-[10px] font-black text-[#163a24]">{{ $percentage }}%</span>
                        </div>
                        <div class="w-full bg-[#fef9e1] rounded-full h-2 overflow-hidden">
                            <div class="bg-[#112d1c] h-full rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{{ number_format($poll->total_votes ?? 0) }} total votes</p>
                    <a href="#" class="text-[10px] font-black text-[#163a24] uppercase tracking-widest flex items-center gap-2 hover:text-[#f3bc3e] transition">
                        Full Report <i class="fas fa-arrow-right text-[8px]"></i>
                    </a>
                </div>
            </div>
            @endforeach

            <!-- Placeholder results to match design -->
            <div class="bg-white rounded-[2.5rem] shadow-xl p-10 border border-[#163a24]/5">
                <div class="flex justify-between items-start mb-8">
                    <h4 class="text-lg font-black text-[#163a24] leading-tight">Library 24/7 Access Policy</h4>
                    <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest">Closed</span>
                </div>
                <div class="space-y-6 mb-10">
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[10px] font-black text-[#163a24] uppercase tracking-widest">Support 24/7 access</span>
                            <span class="text-[10px] font-black text-[#163a24]">82%</span>
                        </div>
                        <div class="w-full bg-[#fef9e1] rounded-full h-2 overflow-hidden">
                            <div class="bg-[#112d1c] h-full rounded-full" style="width: 82%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[10px] font-black text-[#163a24] uppercase tracking-widest">Keep current hours</span>
                            <span class="text-[10px] font-black text-[#163a24]">18%</span>
                        </div>
                        <div class="w-full bg-[#fef9e1] rounded-full h-2 overflow-hidden">
                            <div class="bg-[#112d1c] h-full rounded-full" style="width: 18%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">1,204 total votes</p>
                    <a href="#" class="text-[10px] font-black text-[#163a24] uppercase tracking-widest flex items-center gap-2 hover:text-[#f3bc3e] transition">
                        Full Report <i class="fas fa-arrow-right text-[8px]"></i>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-[2.5rem] shadow-xl p-10 border border-[#163a24]/5">
                <div class="flex justify-between items-start mb-8">
                    <h4 class="text-lg font-black text-[#163a24] leading-tight">Campus Shuttle Route Update</h4>
                    <span class="text-[8px] font-black text-gray-300 uppercase tracking-widest">Closed</span>
                </div>
                <div class="space-y-6 mb-10">
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[10px] font-black text-[#163a24] uppercase tracking-widest">Route A (Dorms)</span>
                            <span class="text-[10px] font-black text-[#163a24]">45%</span>
                        </div>
                        <div class="w-full bg-[#fef9e1] rounded-full h-2 overflow-hidden">
                            <div class="bg-[#112d1c] h-full rounded-full" style="width: 45%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-[10px] font-black text-[#163a24] uppercase tracking-widest">Route B (West Campus)</span>
                            <span class="text-[10px] font-black text-[#163a24]">55%</span>
                        </div>
                        <div class="w-full bg-[#fef9e1] rounded-full h-2 overflow-hidden">
                            <div class="bg-[#112d1c] h-full rounded-full" style="width: 55%"></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-center justify-between pt-6 border-t border-gray-50">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">856 total votes</p>
                    <a href="#" class="text-[10px] font-black text-[#163a24] uppercase tracking-widest flex items-center gap-2 hover:text-[#f3bc3e] transition">
                        Full Report <i class="fas fa-arrow-right text-[8px]"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Banner -->
    <div class="bg-[#112d1c] rounded-[3rem] p-12 relative overflow-hidden group shadow-xl flex flex-col md:flex-row items-center justify-between gap-10">
        <div class="relative z-10">
            <h3 class="text-4xl font-black text-white mb-4">Can't find a topic you care about?</h3>
            <p class="text-white/60 text-lg font-bold max-w-xl mb-10 leading-relaxed">Submit a suggestion for our next community poll. We review all student requests for campus-wide feedback weekly.</p>
            <button class="bg-white text-[#163a24] px-10 py-4 rounded-xl font-black uppercase tracking-widest hover:bg-yellow-400 transition-all shadow-lg">
                Propose a Poll
            </button>
        </div>
        <div class="relative w-full max-w-md h-64 rounded-3xl overflow-hidden bg-black/20 flex items-center justify-center border border-white/5">
            <div class="absolute inset-0 opacity-30 bg-gradient-to-br from-[#f3bc3e] to-transparent"></div>
            <div class="text-center">
                <p class="text-[10px] font-black text-[#f3bc3e] uppercase tracking-[0.4em] mb-2">Student</p>
                <h4 class="text-4xl font-black text-white uppercase tracking-tighter">Forum</h4>
            </div>
        </div>
    </div>
</div>
@endsection
