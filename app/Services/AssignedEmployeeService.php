<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ActivityLog;
use App\Models\AssignedEmployee;
use App\Models\AssignedExhibitor;
use App\Models\Contract;
use App\Models\Lead;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignedEmployeeService
{
    public $last_id = null;
    public $module_name = 'assigned_employees';

    /**
     * index of assigned employee service
     *
     * @return Collection
     */
    public function indexAssignedEmployee(): Collection
    {
        $assigned_leads = Lead::select('leads.*')
            ->join('assigned_employees', 'assigned_employees.lead_id', '=', 'leads.id')
            ->where('leads.is_booker_assigned', true)
            ->where('leads.is_done', false)
            ->get();

        return $assigned_leads;
    }

    /**
     * index of current assigned employee service
     *
     * @return Collection
     */
    public function indexCurrentAssignedEmployee(): Collection
    {
        $assigned_leads = Lead::select('leads.*')
            ->join('assigned_employees', 'assigned_employees.lead_id', '=', 'leads.id')
            ->where('leads.is_booker_assigned', true)
            ->where('leads.is_done', false)
            ->where('assigned_employees.employee_id', Auth::user()->employee->id)
            ->get();

        return $assigned_leads;
    }

    /**
     * index of assigned employee service
     *
     * @return Paginator
     */
    public function indexAssignedEmployeePaginate(array $request): Paginator
    {
        $assigned_leads = Lead::select('leads.*')
            ->join('assigned_employees', 'assigned_employees.lead_id', '=', 'leads.id')
            ->where('leads.is_booker_assigned', true)
            ->where('leads.is_done', false);

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
                    ->orWhere('leads.mobile_number_two', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('leads.refer_by', 'LIKE', '%' . $request['search'] . '%');
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

        // lead_status
        if (array_key_exists('lead_status', $request) && !empty($request['lead_status'])) {
            $assigned_leads->where('assigned_employees.lead_status', $request['lead_status']);
        }

        //date filter
        if ((array_key_exists('start_to', $request) && !empty($request['start_to'])) && (array_key_exists('end_to', $request) && !empty($request['end_to']))) {
            $assigned_leads->whereBetween('assigned_employees.created_at', [Carbon::parse($request['start_to'])->startOfDay()->format('Y-m-d H:i:s'), Carbon::parse($request['end_to'])->endOfDay()->format('Y-m-d H:i:s')]);
        }

        //assigned to
        if (array_key_exists('assigned_to', $request) && !empty($request['assigned_to'])) {
            $assigned_leads->where('assigned_employees.employee_id', $request['assigned_to']);
        }

        return $assigned_leads->orderBy($sort_by, $sort)->paginate($per_page);
    }

    /**
     * index of current assigned employee service
     *
     * @return Paginator
     */
    public function indexCurrentAssignedEmployeePaginate(array $request): Paginator
    {
        $assigned_leads = Lead::select('leads.*')
            ->join('assigned_employees', 'assigned_employees.lead_id', '=', 'leads.id')
            ->where('leads.is_booker_assigned', true)
            ->where('leads.is_done', false)
            ->where('assigned_employees.employee_id', Auth::user()->employee->id);

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
                    ->orWhere('leads.mobile_number_two', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('leads.refer_by', 'LIKE', '%' . $request['search'] . '%');
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

        // lead_status
        if (array_key_exists('lead_status', $request) && !empty($request['lead_status'])) {
            $assigned_leads->where('assigned_employees.lead_status', $request['lead_status']);
        }

        //date filter
        if ((array_key_exists('start_to', $request) && !empty($request['start_to'])) && (array_key_exists('end_to', $request) && !empty($request['end_to']))) {
            $assigned_leads->whereBetween('assigned_employees.created_at', [Carbon::parse($request['start_to'])->startOfDay()->format('Y-m-d H:i:s'), Carbon::parse($request['end_to'])->endOfDay()->format('Y-m-d H:i:s')]);
        }

        return $assigned_leads->orderBy($sort_by, $sort)->paginate($per_page);
    }

    /**
     * create assigned employee service
     *
     * @param array $request
     * @return AssignedEmployee
     */
    public function createAssignedEmployee(array $request): array
    {
        try {
            DB::beginTransaction();

            foreach ($request['lead_ids'] as $lead) {
                $lead = Lead::find($lead);

                if (!$lead->is_booker_assigned) {
                    $assignedEmployee = AssignedEmployee::create([
                        'lead_id' => $lead->getKey(),
                        'employee_id' => $request['employee_id'],
                        'created_by' => Auth::user()->employee->id,
                    ]);

                    $lead = tap($lead)->update([
                        'is_booker_assigned' => true,
                        'updated_by' => Auth::user()->employee->id
                    ]);

                    // if lead is from roi or survey
                    //&& (Auth::user()->employee->user_group_id == 7 || Auth::user()->employee->user_group_id == 8)
                    if (!$lead->is_exhibitor_assigned) {
                        AssignedExhibitor::create([
                            'lead_id' => $lead->getKey(),
                            'employee_id' => $request['employee_id'],
                            'created_by' => Auth::user()->employee->id,
                        ]);

                        $lead->update([
                            'is_exhibitor_assigned' => true,
                        ]);
                    }

                    $this->last_id = $assignedEmployee->id;
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
            DB::rollBack();

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];

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

            return $return_values;
        }
        DB::commit();

        return $return_values;
    }

    /**
     * update assigned employee service
     *
     * @param array $request
     * @return AssignedEmployee
     */
    public function updateAssignedEmployee(array $request): array
    {
        try {
            DB::beginTransaction();

            foreach ($request['lead_ids'] as $lead) {
                $assignedEmployee = AssignedEmployee::where('lead_id', $lead)->first();

                if ($assignedEmployee) {
                    $assignedEmployee->update([
                        'employee_id' => $request['employee_id'],
                        'updated_by' => Auth::user()->employee->id
                    ]);

                    // update the record in lead
                    $lead = Lead::find($lead);
                    $lead->update([
                        'is_exhibitor_assigned' => true,
                        'updated_by' => Auth::user()->employee->id
                    ]);

                    $this->last_id = $assignedEmployee->id;
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
            DB::rollBack();

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
        DB::commit();

        return $return_values;
    }

    /**
     * removed assigned employee in a lead
     *
     * @param array $request
     * @return array
     */
    public function removedAssigned(array $request): array
    {
        try {
            DB::beginTransaction();

            foreach ($request['lead_ids'] as $lead) {
                $lead = Lead::find($lead);
                $lead->update([
                    'is_booker_assigned' => false,
                    'updated_by' => Auth::user()->employee->id
                ]);

                $assignedEmployee = $lead->assignedEmployee;
                $lead->assignedEmployee->delete();

                $this->last_id = $assignedEmployee->id;
                $return_values = ['result' => 'success', 'message' => 'Succefully removed assignment!', 'subject' => $this->last_id];

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

            return $return_values;
        }

        DB::commit();
        return $return_values;
    }

    /**
     * modify employee assigned remarks service
     *
     * @param array $request
     * @return array
     */
    public function modifyRemarks(array $request): array
    {
        try {
            DB::beginTransaction();

            $assigned_employee = AssignedEmployee::where('lead_id', $request['lead_id'])->first();

            $assigned_employee = tap($assigned_employee)->update([
                'remarks' => $request['remarks'],
                'lead_status' => $request['lead_status'],
                'updated_by' => Auth::user()->employee->id
            ]);

            $assigned_employee->lead->update([
                'venue_id' => $request['venue_id'],
                'presentation_date' => $request['presentation_date'],
                'presentation_time' => $request['presentation_time'],
                'updated_by' => Auth::user()->employee->id
            ]);

            $this->last_id = $assigned_employee->id;
            // $return_values = ['result' => 'success', 'message' => 'Successfully saved modified!', 'subject' => $this->last_id];
            $return_values = ['result' => 'success', 'message' => 'Successfully set "' . $request['lead_status'] . '" status', 'subject' => $this->last_id];

            //log activity
            ActivityLog::create([
                'name' => $this->module_name,
                'description' => $return_values['message'],
                'event' => 'update',
                'status' => $return_values['result'],
                'browser' => json_encode(Helper::deviceInfo()),
                'properties' => json_encode($request),
                'causer_id' => Auth::user()->id,
                'subject_id' => $return_values['subject']
            ]);
        } catch (Exception $e) {
            DB::rollBack();

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];

            //log activity
            ActivityLog::create([
                'name' => $this->module_name,
                'description' => $return_values['message'],
                'event' => 'update',
                'status' => $return_values['result'],
                'browser' => json_encode(Helper::deviceInfo()),
                'properties' => json_encode($request),
                'causer_id' => Auth::user()->id,
                'subject_id' => $return_values['subject']
            ]);

            return $return_values;
        }
        DB::commit();

        return $return_values;
    }
}
