<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Polls Engagement Report - {{ now()->format('Y-m-d') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .print-container { width: 100%; max-width: none; padding: 0; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-5xl mx-auto bg-white p-10 shadow-lg print-container">
        <div class="flex justify-between items-start border-b-2 border-[#163a24] pb-8 mb-8">
            <div>
                <h1 class="text-3xl font-black text-[#163a24] uppercase tracking-tighter">V.O.I.C.E.</h1>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">Student Sentiment & Polls Report</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-black text-[#163a24] uppercase tracking-widest">Report Date</p>
                <p class="text-xl font-bold text-gray-800">{{ now()->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="space-y-12">
            @foreach($polls as $poll)
            <div class="border-b border-gray-100 pb-10 last:border-0">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-gray-400">
                            Poll ID: #{{ str_pad($poll->id, 4, '0', STR_PAD_LEFT) }} &bull; {{ strtoupper($poll->status) }}
                        </span>
                        <h2 class="text-2xl font-black text-[#163a24] mt-1">{{ $poll->title }}</h2>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 rounded-2xl text-center">
                        <p class="text-2xl font-black text-[#163a24]">{{ $poll->votes->count() }}</p>
                        <p class="text-[8px] font-black text-gray-400 uppercase tracking-widest">Total Votes</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6">
                    @php $totalVotes = $poll->votes->count(); @endphp
                    @foreach($poll->options as $option)
                    <div>
                        <div class="flex justify-between items-center mb-2 px-1">
                            <span class="text-sm font-bold text-[#163a24]">{{ $option->option_text }}</span>
                            <span class="text-xs font-black text-[#163a24]">{{ $option->getPercentage($totalVotes) }}% ({{ $option->votes_count }})</span>
                        </div>
                        <div class="h-4 w-full bg-gray-100 rounded-full overflow-hidden border border-gray-200">
                            <div class="h-full bg-[#163a24] rounded-full" 
                                 style="width: {{ $option->getPercentage($totalVotes) }}%"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        <div class="border-t border-gray-100 pt-8 mt-12 flex justify-between items-center no-print">
            <p class="text-xs font-bold text-gray-400 uppercase italic">Computer Generated Institutional Analytics</p>
            <button onclick="window.print()" class="bg-[#163a24] text-white px-8 py-3 rounded-xl font-black uppercase tracking-widest text-sm shadow-xl hover:bg-[#1a4d2a] transition-all">
                Print Report / Save as PDF
            </button>
        </div>
    </div>

    <script>
        window.onload = () => {
            if (window.location.search.includes('print=true')) {
                window.print();
            }
        };
    </script>
</body>
</html>
