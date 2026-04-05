@extends("layouts.app")

@section("content")
<div class="max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Dashboard Overview</h2>
        <p class="text-gray-500 mt-1">Monitor and manage all submitted complaints.</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Pending Review</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats["pending"] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">In Progress</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats["in_progress"] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-spinner text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">Resolved</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats["resolved"] ?? 0 }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6 mb-8">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Complaints by Category</h3>
        <canvas id="categoryChart" height="300"></canvas>
    </div>
    
    <div class="bg-white rounded-xl shadow-sm p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Recent Complaints</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="border-b">
                    <tr class="text-left text-gray-600">
                        <th class="pb-3">Complaint</th>
                        <th class="pb-3">Submitter</th>
                        <th class="pb-3">Date</th>
                        <th class="pb-3">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentComplaints as $complaint)
                    <tr class="border-b hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route("admin.complaints.show", $complaint) }}'">
                        <td class="py-3">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                                    <span class="text-sm font-semibold">{{ substr($complaint->user->name, 0, 2) }}</span>
                                </div>
                                <div>
                                    <p class="font-medium">{{ $complaint->title }}</p>
                                    <p class="text-xs text-gray-500">{{ $complaint->category }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">{{ $complaint->user->name }}</td>
                        <td class="py-3">{{ $complaint->created_at->format("Y-m-d") }}</td>
                        <td class="py-3">
                            <span class="px-2 py-1 text-xs rounded-full @if($complaint->status === "pending") bg-yellow-100 text-yellow-800 @elseif($complaint->status === "in_progress") bg-blue-100 text-blue-800 @elseif($complaint->status === "resolved") bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                {{ ucfirst($complaint->status) }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-8 text-center text-gray-500">No complaints found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const ctx = document.getElementById("categoryChart").getContext("2d");
        new Chart(ctx, {
            type: "doughnut",
            data: {
                labels: ["Academic", "Facility", "Administrative", "IT/Technical", "Health & Safety"],
                datasets: [{
                    data: [
                        {{ $categoryStats["Academic"] ?? 0 }},
                        {{ $categoryStats["Facility"] ?? 0 }},
                        {{ $categoryStats["Administrative"] ?? 0 }},
                        {{ $categoryStats["IT/Technical"] ?? 0 }},
                        {{ $categoryStats["Health & Safety"] ?? 0 }}
                    ],
                    backgroundColor: ["#3B82F6", "#10B981", "#F59E0B", "#EF4444", "#8B5CF6"],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { position: "bottom" }
                }
            }
        });
    });
</script>
@endsection
