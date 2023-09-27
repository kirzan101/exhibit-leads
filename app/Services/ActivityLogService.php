<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ActivityLog;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActivityLogService
{
    public function indexActivityLogPaginate(array $request): Paginator
    {
        $activity_logs = ActivityLog::select('activity_logs.*')
            ->join('users', 'users.id', '=', 'activity_logs.causer_id');

        $per_page = (array_key_exists('per_page', $request) && $request['per_page'] != null) ? (int)$request['per_page'] : 5;
        $sort_by = (array_key_exists('sort_by', $request) && $request['sort_by'] != null) ? $request['sort_by'] : 'id';
        $sort = 'desc';
        if (array_key_exists('is_sort_desc', $request) && $request['is_sort_desc'] != null) {
            $sort = ($request['is_sort_desc'] == 'true') ? 'desc' : 'asc';
        }

        // search filter
        if (array_key_exists('search', $request) && !empty($request['search'])) {
            $activity_logs->where('activity_logs.name', 'LIKE', '%' . $request['search'] . '%')
                ->orWhere('activity_logs.description', 'LIKE', '%' . $request['search'] . '%')
                ->orWhere('users.first_name', 'LIKE', '%' . $request['search'] . '%')
                ->orWhere('users.last_name', 'LIKE', '%' . $request['search'] . '%');
        }

        if (array_key_exists('event', $request) && !empty($request['event'])) {
            $activity_logs->whereBetween('activity_logs.event', $request['event']);
        }

        if (array_key_exists('name', $request) && !empty($request['name'])) {
            $activity_logs->whereBetween('activity_logs.name', $request['name']);
        }

        if (array_key_exists('user_id', $request) && !empty($request['user_id'])) {
            $activity_logs->whereBetween('activity_logs.causer_id', $request['user_id']);
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

            ActivityLog::create([
                'name' => $request['name'],
                'description' => $request['description'],
                'event' => $request['event'],
                'browser' => json_encode(Helper::deviceInfo()),
                'properties' => $request['properties'],
                'causer_id' => Auth::user()->id,
                'subject_id' => $request['subject_id']
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage()];
        }

        DB::commit();

        return ['result' => 'success', 'message' => 'Successfully saved!'];
    }
}
