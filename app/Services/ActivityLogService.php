<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ActivityLog;
use App\Models\Employee;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityLogService
{
    public $last_id = null;

    public function indexActivityLogPaginate(array $request): Paginator
    {
        $activity_logs = ActivityLog::select('activity_logs.*')
            ->join('users', 'users.id', '=', 'activity_logs.causer_id')
            ->join('employees', 'employees.user_id', '=', 'users.id');

        $per_page = (array_key_exists('per_page', $request) && $request['per_page'] != null) ? (int)$request['per_page'] : 5;
        $sort_by = (array_key_exists('sort_by', $request) && $request['sort_by'] != null) ? $request['sort_by'] : 'id';
        $sort = 'desc';
        if (array_key_exists('is_sort_desc', $request) && $request['is_sort_desc'] != null) {
            $sort = ($request['is_sort_desc'] == 'true') ? 'desc' : 'asc';
        }

        // search filter
        if (array_key_exists('search', $request) && !empty($request['search'])) {
            $activity_logs->where('activity_logs.description', 'LIKE', '%' . $request['search'] . '%');
        }

        if (array_key_exists('event', $request) && !empty($request['event'])) {
            $activity_logs->where('activity_logs.event', $request['event']);
        }

        if (array_key_exists('name', $request) && !empty($request['name'])) {
            $activity_logs->where('activity_logs.name', 'LIKE', '%' . $request['name'] . '%');
        }

        if (array_key_exists('user_id', $request) && !empty($request['user_id'])) {
            $activity_logs->where('activity_logs.causer_id', $request['user_id']);
        }

        //date filter
        if ((array_key_exists('start_to', $request) && !empty($request['start_to'])) && (array_key_exists('end_to', $request) && !empty($request['end_to']))) {
            $activity_logs->whereBetween('activity_logs.created_at', [Carbon::parse($request['start_to'])->startOfDay()->format('Y-m-d H:i:s'), Carbon::parse($request['end_to'])->endOfDay()->format('Y-m-d H:i:s')]);
        }

        return $activity_logs->orderBy($sort_by, $sort)->paginate($per_page);
    }

    /**
     * Create activity log service
     *
     * @param array $request
     * @return array
     */
    public function createActivityLog(array $request): array
    {
        try {
            DB::beginTransaction();

            $activityLog = ActivityLog::create([
                'name' => $request['name'],
                'description' => $request['description'],
                'event' => $request['event'],
                'status' => $request['status'],
                'browser' => json_encode(Helper::deviceInfo()),
                'properties' => $request['properties'],
                'causer_id' => (Auth::id()) ? Auth::id() : 1,
                'subject_id' => $request['subject_id']
            ]);

            $this->last_id = $activityLog->id;
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }

        DB::commit();

        return ['result' => 'success', 'message' => 'Successfully saved!', 'subject' => $this->last_id];
    }
}
