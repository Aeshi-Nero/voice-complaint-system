<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaints Report - {{ now()->format('Y-m-d') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { background: white; }
            .print-container { width: 100%; max-width: none; padding: 0; }
            .page-break { page-break-before: always; }
        }
    </style>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-5xl mx-auto bg-white p-10 shadow-lg print-container">
        <div class="flex justify-between items-start border-b-2 border-[#163a24] pb-8 mb-8">
            <div>
                <h1 class="text-3xl font-black text-[#163a24] uppercase tracking-tighter">V.O.I.C.E.</h1>
                <p class="text-xs font-bold text-gray-500 uppercase tracking-widest mt-1">Institutional Complaint Report</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-black text-[#163a24] uppercase tracking-widest">Report Date</p>
                <p class="text-xl font-bold text-gray-800">{{ now()->format('F d, Y') }}</p>
            </div>
        </div>

        <div class="mb-12">
            <h2 class="text-xl font-black text-[#163a24] uppercase tracking-widest mb-6 border-b border-gray-100 pb-2">Status Summary</h2>
            <div class="grid grid-cols-4 gap-8">
                <div class="border-l-4 border-gray-800 pl-4">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Total</p>
                    <p class="text-2xl font-black text-[#163a24]">{{ $stats['total'] }}</p>
                </div>
                <div class="border-l-4 border-green-500 pl-4">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Resolved</p>
                    <p class="text-2xl font-black text-[#163a24]">{{ $stats['resolved'] }}</p>
                </div>
                <div class="border-l-4 border-yellow-500 pl-4">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Pending</p>
                    <p class="text-2xl font-black text-[#163a24]">{{ $stats['pending'] }}</p>
                </div>
                <div class="border-l-4 border-red-500 pl-4">
                    <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Rejected</p>
                    <p class="text-2xl font-black text-[#163a24]">{{ $stats['rejected'] }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-12 mb-12">
            <div>
                <h2 class="text-sm font-black text-[#163a24] uppercase tracking-widest mb-4">By Category</h2>
                <div class="space-y-2">
                    @foreach($categoryStats as $cat => $count)
                    <div class="flex justify-between items-center border-b border-gray-50 py-1">
                        <span class="text-xs font-bold text-gray-600">{{ $cat }}</span>
                        <span class="text-xs font-black text-[#163a24]">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
            <div>
                <h2 class="text-sm font-black text-[#163a24] uppercase tracking-widest mb-4">By Department</h2>
                <div class="space-y-2">
                    @foreach(array_slice($departmentStats, 0, 8) as $dept => $count)
                    <div class="flex justify-between items-center border-b border-gray-50 py-1">
                        <span class="text-xs font-bold text-gray-600 truncate max-w-[150px]">{{ $dept }}</span>
                        <span class="text-xs font-black text-[#163a24]">{{ $count }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="page-break no-print"></div>

        <h2 class="text-xl font-black text-[#163a24] uppercase tracking-widest mb-6 border-b border-gray-100 pb-2">Detailed Log</h2>
        <table class="w-full text-left mb-12">
            <thead>
                <tr class="border-b-2 border-gray-100">
                    <th class="py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">ID</th>
                    <th class="py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Submitter</th>
                    <th class="py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Category</th>
                    <th class="py-4 text-[10px] font-black uppercase tracking-widest text-gray-400">Status</th>
                    <th class="py-4 text-[10px] font-black uppercase tracking-widest text-gray-400 text-right">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50 text-[11px]">
                @foreach($complaints as $complaint)
                <tr>
                    <td class="py-3 font-black text-[#163a24]">{{ $complaint->complaint_number }}</td>
                    <td class="py-3">
                        <p class="font-bold text-gray-800">{{ $complaint->user->name }}</p>
                        <p class="text-[9px] text-gray-400">{{ $complaint->user->course }}</p>
                    </td>
                    <td class="py-3 font-bold text-gray-600 uppercase">{{ $complaint->category }}</td>
                    <td class="py-3 font-black text-[#163a24] uppercase">{{ $complaint->status }}</td>
                    <td class="py-3 font-medium text-gray-600 text-right">{{ $complaint->created_at->format('Y-m-d') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-gray-100 pt-8 flex justify-between items-center no-print">
            <p class="text-xs font-bold text-gray-400 uppercase italic">Confidential Institutional Document</p>
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
