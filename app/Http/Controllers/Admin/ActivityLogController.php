<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ActivityLogController extends Controller
{
    public function index(Request $request): View
    {
        $query = ActivityLog::query()->with('user');

        if ($search = trim((string) $request->query('search', ''))) {
            $escapedSearch = addcslashes($search, '%_');

            $query->where(function ($q) use ($escapedSearch) {
                $q->where('description', 'like', "%{$escapedSearch}%")
                    ->orWhere('action', 'like', "%{$escapedSearch}%")
                    ->orWhereHas('user', function ($userQuery) use ($escapedSearch) {
                        $userQuery->where('name', 'like', "%{$escapedSearch}%")
                            ->orWhere('email', 'like', "%{$escapedSearch}%");
                    });
            });
        }

        if ($action = trim((string) $request->query('action', ''))) {
            $query->where('action', $action);
        }

        if ($userId = $request->query('user_id')) {
            if (is_numeric($userId)) {
                $query->where('user_id', (int) $userId);
            }
        }

        if ($startDate = $request->query('start_date')) {
            if (Carbon::hasFormat($startDate, 'Y-m-d')) {
                $query->whereDate('created_at', '>=', $startDate);
            }
        }

        if ($endDate = $request->query('end_date')) {
            if (Carbon::hasFormat($endDate, 'Y-m-d')) {
                $query->whereDate('created_at', '<=', $endDate);
            }
        }

        $logs = $query
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        $actions = ActivityLog::query()
            ->select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $users = User::query()
            ->whereHas('activityLogs')
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('admin.activity-log.index', [
            'logs'    => $logs,
            'actions' => $actions,
            'users'   => $users,
            'filters' => $request->only(['search', 'action', 'user_id', 'start_date', 'end_date']),
        ]);
    }

    public function show(ActivityLog $activity_log): View
    {
        $activity_log->loadMissing('user');

        return view('admin.activity-log.show', [
            'log' => $activity_log,
        ]);
    }
}