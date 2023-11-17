<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ActivityLog;
use App\Models\AssignedExhibitor;
use App\Models\Lead;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignedExhibitorService
{
    public $last_id = null;
    public $module_name = 'assigned_exhibitors';

    /**
     * list of leads that has assigned to an exhibitor
     *
     * @return void
     */
    public function indexLeadsAssignedExhibitor(): Collection
    {
        $leads = Lead::select('leads.*')
            ->join('assigned_exhibitors', 'assigned_exhibitors.lead_id', '=', 'leads.id')
            ->where('leads.is_exhibitor_assigned', true)
            ->where('leads.is_booker_assigned', false)
            ->get();

        return $leads;
    }

    /**
     * create assigned exhibitor service
     *
     * @param array $request
     * @return array
     */
    public function createAssignedExhbitor(array $request): array
    {
        try {
            foreach ($request['lead_ids'] as $lead) {
                $lead = Lead::find($lead);

                if (!$lead->is_exhibitor_assigned) {
                    $assigned_exhibitor = AssignedExhibitor::create([
                        'lead_id' => $lead->getKey(),
                        'employee_id' => $request['employee_id'],
                        'created_by' => Auth::user()->employee->id,
                    ]);

                    $lead->update([
                        'is_exhibitor_assigned' => true,
                        'updated_by' => Auth::user()->employee->id
                    ]);

                    $this->last_id = $assigned_exhibitor->id;

                    $return_values = ['result' => 'success', 'message' => 'Succefully assigned!', 'subject' => $this->last_id];

                    //log activity
                    ActivityLog::create([
                        'name' => $this->module_name,
                        'description' => $return_values['message'],
                        'event' => 'create',
                        'status' => $return_values['result'],
                        'browser' => json_encode(Helper::deviceInfo()),
                        'properties' => '{"lead_id":' . $lead->id . ',"employee_id":' . $request['employee_id'] . '}',
                        'causer_id' => Auth::user()->id,
                        'subject_id' => $return_values['subject']
                    ]);
                }
            }
        } catch (Exception $e) {
            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];

            //log activity
            ActivityLog::create([
                'name' => $this->module_name,
                'description' => $return_values['message'],
                'event' => 'delete',
                'status' => $return_values['result'],
                'browser' => json_encode(Helper::deviceInfo()),
                'properties' => '{"lead_id":' . $lead->id . ',"employee_id":' . $request['employee_id'] . '}',
                'causer_id' => Auth::user()->id,
                'subject_id' => $return_values['subject']
            ]);

            return $return_values;
        }

        return $return_values;
    }

    /**
     * update assigned exhibitor service
     *
     * @param array $request
     * @return array
     */
    public function updateAssignedExhibitor(array $request): array
    {
        try {
            foreach ($request['lead_ids'] as $lead) {
                $assignedExhibitor = AssignedExhibitor::where('lead_id', $lead)->first();

                if ($assignedExhibitor) {
                    $assignedExhibitor->update([
                        'employee_id' => $request['employee_id'],
                        'updated_by' => Auth::user()->employee->id
                    ]);

                    // update the record in lead
                    $lead = Lead::find($lead);

                    $lead->update([
                        'is_exhibitor_assigned' => true,
                        'updated_by' => Auth::user()->employee->id
                    ]);

                    $this->last_id = $assignedExhibitor->id;

                    $return_values = ['result' => 'success', 'message' => 'Succefully updated!', 'subject' => $this->last_id];

                    //log activity
                    ActivityLog::create([
                        'name' => $this->module_name,
                        'description' => $return_values['message'],
                        'event' => 'update',
                        'status' => $return_values['result'],
                        'browser' => json_encode(Helper::deviceInfo()),
                        'properties' => '{"lead_id":' . $lead->id . ',"employee_id":' . $request['employee_id'] . '}',
                        'causer_id' => Auth::user()->id,
                        'subject_id' => $return_values['subject']
                    ]);
                }
            }
        } catch (Exception $e) {

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];

            //log activity
            ActivityLog::create([
                'name' => $this->module_name,
                'description' => $return_values['message'],
                'event' => 'update',
                'status' => $return_values['result'],
                'browser' => json_encode(Helper::deviceInfo()),
                'properties' => '{"lead_id":' . $lead->id . ',"employee_id":' . $request['employee_id'] . '}',
                'causer_id' => Auth::user()->id,
                'subject_id' => $return_values['subject']
            ]);

            return $return_values;
        }

        return $return_values;
    }

    /**
     * delete assigned exhibitor service
     *
     * @param AssignedExhibitor $assignedExhibitor
     * @return array
     */
    public function deleteAssignedExhibitor(AssignedExhibitor $assignedExhibitor): array
    {
        $this->last_id = $assignedExhibitor->id;
        $employee_id = $assignedExhibitor->employee_id;

        try {
            DB::beginTransaction();

            $lead = Lead::find($assignedExhibitor->lead_id);

            $lead->update([
                'is_exhibitor_assigned' => false,
                'updated_by' => Auth::user()->employee->id
            ]);

            $assignedExhibitor->delete();

            $return_values = ['result' => 'error', 'message' => 'Successfully Deleted', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();
            
            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        //log activity
        ActivityLog::create([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'delete',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => '{"lead_id":' . $lead->id . ',"employee_id":' . $employee_id . '}',
            'causer_id' => Auth::user()->id,
            'subject_id' => $return_values['subject']
        ]);

        return $return_values;
    }

    /**
     * removed assigned employee in a lead
     *
     * @param array $request
     * @return array
     */
    public function removedAssignedExhibitor(array $request): array
    {
        try {
            DB::beginTransaction();
            foreach ($request['lead_ids'] as $lead) {
                $lead = Lead::find($lead);

                $lead->update([
                    'is_exhibitor_assigned' => false,
                    'updated_by' => Auth::user()->employee->id
                ]);

                $assigned_exhibitor = AssignedExhibitor::where('lead_id', $lead->id)->first();
                $this->last_id = $assigned_exhibitor->id;

                $return_values = ['result' => 'success', 'message' => 'Successfully Removed assignment', 'subject' => $this->last_id];


                $assigned_exhibitor->delete();
                //log activity
                ActivityLog::create([
                    'name' => $this->module_name,
                    'description' => $return_values['message'],
                    'event' => 'delete',
                    'status' => $return_values['result'],
                    'browser' => json_encode(Helper::deviceInfo()),
                    'properties' => '{"lead_id":' . $lead->id . ',"employee_id":""}',
                    'causer_id' => Auth::user()->id,
                    'subject_id' => $return_values['subject']
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];

            //log activity
            ActivityLog::create([
                'name' => $this->module_name,
                'description' => $return_values['message'],
                'event' => 'delete',
                'status' => $return_values['result'],
                'browser' => json_encode(Helper::deviceInfo()),
                'properties' => '{"lead_id":' . $lead->id . ',"employee_id":""}',
                'causer_id' => Auth::user()->id,
                'subject_id' => $return_values['subject']
            ]);
        }
        DB::commit();

        return $return_values;
    }

    /**
     * index of assigned employee service
     *
     * @return Paginator
     */
    public function indexAssignedExhibitorPaginate(array $request): Paginator
    {
        $assigned_leads = Lead::select('leads.*')
            ->join('assigned_exhibitors', 'assigned_exhibitors.lead_id', '=', 'leads.id')
            ->where('leads.is_exhibitor_assigned', true)
            ->where('leads.is_booker_assigned', false);

        //set default values
        $per_page = (array_key_exists('per_page', $request) && $request['per_page'] != null) ? (int)$request['per_page'] : 5;
        $sort_by = (array_key_exists('sort_by', $request) && $request['sort_by'] != null) ? $request['sort_by'] : 'id';
        $sort = 'desc';
        if (array_key_exists('is_sort_desc', $request) && $request['is_sort_desc'] != null) {
            $sort = ($request['is_sort_desc'] == 'true') ? 'desc' : 'asc';
        }

        // search filter
        if (array_key_exists('search', $request) && !empty($request['search'])) {
            $assigned_leads->where(function ($query) use ($request) {
                $query->where('leads.first_name', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('leads.last_name', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('leads.occupation', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('leads.mobile_number_one', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('leads.mobile_number_two', 'LIKE', '%' . $request['search'] . '%');
            });
        }

        // venue filter
        if (array_key_exists('venue_id', $request) && !empty($request['venue_id'])) {
            $assigned_leads->where('leads.venue_id', $request['venue_id']);
        }

        // source filter
        if (array_key_exists('source_name', $request) && !empty($request['source_name'])) {
            [$prefix, $suffix] = explode("-", $request['source_name'], 2);
            $assigned_leads->where('leads.source_prefix', $prefix)
                ->where('leads.source', $suffix);
        }

        // occupation
        if (array_key_exists('occupation', $request) && !empty($request['occupation'])) {
            $assigned_leads->where('leads.occupation', $request['occupation']);
        }

        // // lead_status
        // if (array_key_exists('lead_status', $request) && !empty($request['lead_status'])) {
        //     $assigned_leads->where('assigned_exhibitors.lead_status', $request['lead_status']);
        // }

        //date filter
        if ((array_key_exists('start_to', $request) && !empty($request['start_to'])) && (array_key_exists('end_to', $request) && !empty($request['end_to']))) {
            $assigned_leads->whereBetween('assigned_exhibitors.created_at', [Carbon::parse($request['start_to'])->startOfDay()->format('Y-m-d H:i:s'), Carbon::parse($request['end_to'])->endOfDay()->format('Y-m-d H:i:s')]);
        }

        return $assigned_leads->orderBy($sort_by, $sort)->paginate($per_page);
    }
}
