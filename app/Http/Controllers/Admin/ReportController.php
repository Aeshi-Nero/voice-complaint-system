<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use App\Models\Poll;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $query = Complaint::with('user');

        if ($request->filled('start_date')) {
            $query->whereDate('submitted_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('submitted_at', '<=', $request->end_date);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Stats for reporting
        $stats = [
            'total' => (clone $query)->count(),
            'pending' => (clone $query)->where('status', 'pending')->count(),
            'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            'resolved' => (clone $query)->where('status', 'resolved')->count(),
            'rejected' => (clone $query)->where('status', 'rejected')->count(),
        ];

        // Category stats
        $categoryStats = (clone $query)->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        // Department stats
        $departmentStats = (clone $query)->join('users', 'complaints.user_id', '=', 'users.id')
            ->select('users.course as department', DB::raw('count(*) as total'))
            ->whereNotNull('users.course')
            ->groupBy('users.course')
            ->orderBy('total', 'desc')
            ->pluck('total', 'department')
            ->toArray();

        // Poll Report Data
        $polls = Poll::with(['options', 'votes'])->latest()->get();

        return view('dashboard.admin.reports.index', compact('stats', 'categoryStats', 'departmentStats', 'polls'));
    }

    public function exportCsv(Request $request)
    {
        $query = Complaint::with('user');

        if ($request->filled('start_date')) {
            $query->whereDate('submitted_at', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('submitted_at', '<=', $request->end_date);
        }

        $complaints = $query->get();

        // Summary Data
        $categoryStats = (clone $query)->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')->pluck('total', 'category')->toArray();
        
        $deptStats = (clone $query)->join('users', 'complaints.user_id', '=', 'users.id')
            ->select('users.course as department', DB::raw('count(*) as total'))
            ->groupBy('users.course')->pluck('total', 'department')->toArray();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=analytical_report_" . now()->format('Y-m-d') . ".csv",
        ];

        $callback = function() use($complaints, $categoryStats, $deptStats) {
            $file = fopen('php://output', 'w');
            
            // 1. Category Summary
            fputcsv($file, ['SUMMARY BY CATEGORY']);
            fputcsv($file, ['Category', 'Total Complaints']);
            foreach($categoryStats as $cat => $count) fputcsv($file, [$cat, $count]);
            fputcsv($file, []);

            // 2. Department Summary
            fputcsv($file, ['SUMMARY BY DEPARTMENT']);
            fputcsv($file, ['Department', 'Total Complaints']);
            foreach($deptStats as $dept => $count) fputcsv($file, [$dept ?: 'Unspecified', $count]);
            fputcsv($file, []);

            // 3. Raw Data
            fputcsv($file, ['DETAILED COMPLAINT LOG']);
            fputcsv($file, ['Complaint #', 'Student', 'Category', 'Status', 'Date']);

            foreach ($complaints as $complaint) {
                fputcsv($file, [
                    $complaint->complaint_number,
                    $complaint->user->name,
                    $complaint->category,
                    $complaint->status,
                    $complaint->created_at->format('Y-m-d')
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        // Re-use logic to get all data
        $data = $this->getReportData($request);
        return view('dashboard.admin.reports.print', $data);
    }

    public function exportPollsCsv(Request $request)
    {
        $ids = explode(',', $request->ids);
        $polls = Poll::with(['options'])->whereIn('id', $ids)->get();

        $headers = ["Content-type" => "text/csv", "Content-Disposition" => "attachment; filename=polls_report.csv"];

        return Response::stream(function() use($polls) {
            $file = fopen('php://output', 'w');
            foreach ($polls as $poll) {
                fputcsv($file, ['POLL: ' . $poll->title]);
                fputcsv($file, ['Status', $poll->status]);
                fputcsv($file, ['Total Votes', $poll->votes()->count()]);
                fputcsv($file, ['Option', 'Votes', 'Percentage']);
                $total = $poll->votes()->count();
                foreach($poll->options as $opt) {
                    fputcsv($file, [$opt->option_text, $opt->votes_count, $opt->getPercentage($total) . '%']);
                }
                fputcsv($file, []);
            }
            fclose($file);
        }, 200, $headers);
    }

    public function exportPollsPdf(Request $request)
    {
        $ids = explode(',', $request->ids);
        $polls = Poll::with(['options', 'votes'])->whereIn('id', $ids)->get();
        return view('dashboard.admin.reports.polls-print', compact('polls'));
    }

    private function getReportData($request)
    {
        $query = Complaint::with('user');
        if ($request->filled('start_date')) $query->whereDate('submitted_at', '>=', $request->start_date);
        if ($request->filled('end_date')) $query->whereDate('submitted_at', '<=', $request->end_date);
        
        return [
            'stats' => [
                'total' => (clone $query)->count(),
                'resolved' => (clone $query)->where('status', 'resolved')->count(),
                'pending' => (clone $query)->where('status', 'pending')->count(),
                'rejected' => (clone $query)->where('status', 'rejected')->count(),
                'in_progress' => (clone $query)->where('status', 'in_progress')->count(),
            ],
            'categoryStats' => (clone $query)->select('category', DB::raw('count(*) as total'))->groupBy('category')->pluck('total', 'category')->toArray(),
            'departmentStats' => (clone $query)->join('users', 'complaints.user_id', '=', 'users.id')->select('users.course as department', DB::raw('count(*) as total'))->groupBy('users.course')->pluck('total', 'department')->toArray(),
            'complaints' => $query->latest()->get()
        ];
    }
}
