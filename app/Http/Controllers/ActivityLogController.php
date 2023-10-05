<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityLogResource;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    private ActivityLogService $activityLogService;

    public function __construct(ActivityLogService $activityLogService)
    {
        $this->activityLogService = $activityLogService;
    }

    public function index(Request $request)
    {
        $this->authorize('read', ActivityLog::class);

        $sort_by = $request->sort_by;
        if ($request->sort_by == 'causer_full_name') {
            $request->merge(['sort_by' => 'employees.last_name']);
        }

        // set default to desc
        if ($request->is_sort_desc == null) {
            $request->merge(['is_sort_desc' => true]);
        }

        $activity_logs = ActivityLogResource::collection($this->activityLogService->indexActivityLogPaginate($request->toArray()));

        return Inertia::render('ActivityLogs/IndexPaginateActivityLog',[
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'module' => 'activity-logs',
            'items' => $activity_logs,
        ]);
    }
}
