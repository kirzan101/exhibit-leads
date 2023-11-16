<?php

namespace App\Http\Controllers;

use App\Http\Resources\ActivityLogResource;
use App\Http\Resources\EmployeeResource;
use App\Services\ActivityLogService;
use App\Services\EmployeeService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    private ActivityLogService $activityLogService;
    private EmployeeService $employeeService;

    public function __construct(
        ActivityLogService $activityLogService,
        EmployeeService $employeeService
    )
    {
        $this->activityLogService = $activityLogService;
        $this->employeeService = $employeeService;
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

        //add default start & end to
        if ($request->start_to == null) {
            $request->merge(['start_to' => Carbon::now()->startOfDay()->format('Y-m-d H:i:s')]);
        }

        if ($request->end_to == null) {
            $request->merge(['end_to' => Carbon::now()->endOfDay()->format('Y-m-d H:i:s')]);
        }

        $activity_logs = ActivityLogResource::collection($this->activityLogService->indexActivityLogPaginate($request->toArray()));

        return Inertia::render('ActivityLogs/IndexPaginateActivityLog',[
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'module' => 'activity-logs',
            'items' => $activity_logs,
            'start_to' => Carbon::parse($request->start_to)->format('Y-m-d'),
            'end_to' => Carbon::parse($request->end_to)->format('Y-m-d'),
            'causers' => EmployeeResource::collection($this->employeeService->indexCausers()),
            'name' => $request->name,
            'user_id' => $request->user_id
        ]);
    }
}
