<?php

namespace App\Http\Controllers;

use App\Services\ActivityLogService;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    /**
     * Display all activity logs
     */
    public function index(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $search = $request->query('search', '');

        $query = \App\Models\ActivityLog::with('user')
            ->orderBy('created_at', 'desc');

        // Filter by action
        if ($filter !== 'all') {
            $query->where('action', $filter);
        }

        // Search by user name or description
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%");
                })->orWhere('description', 'like', "%{$search}%");
            });
        }

        $activityLogs = $query->paginate(50);

        // Get unique actions for filter dropdown
        $actions = \App\Models\ActivityLog::distinct()
            ->orderBy('action')
            ->pluck('action')
            ->toArray();

        return view('management.activity-logs', compact('activityLogs', 'actions', 'filter', 'search'));
    }

    /**
     * Display activity logs for specific user
     */
    public function userLogs($userId, Request $request)
    {
        $user = \App\Models\User::findOrFail($userId);
        $search = $request->query('search', '');

        $query = \App\Models\ActivityLog::where('user_id', $userId)
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where('description', 'like', "%{$search}%");
        }

        $activityLogs = $query->paginate(50);

        return view('management.user-activity-logs', compact('user', 'activityLogs', 'search'));
    }

    /**
     * Export activity logs as CSV
     */
    public function export(Request $request)
    {
        $filter = $request->query('filter', 'all');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $query = \App\Models\ActivityLog::with('user');

        if ($filter !== 'all') {
            $query->where('action', $filter);
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate . ' 23:59:59']);
        }

        $logs = $query->orderBy('created_at', 'desc')->get();

        $csvContent = "Date,Time,User,Action,Description,Model Type,IP Address\n";
        foreach ($logs as $log) {
            $date = $log->created_at->format('Y-m-d');
            $time = $log->created_at->format('H:i:s');
            $user = $log->user ? $log->user->name : 'Unknown';
            $csvContent .= "\"$date\",\"$time\",\"$user\",\"$log->action\",\"" . str_replace('"', '""', $log->description) . "\",\"$log->model_type\",\"$log->ip_address\"\n";
        }

        return response($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="activity-logs-' . now()->format('Y-m-d-H-i-s') . '.csv"',
        ]);
    }
}
