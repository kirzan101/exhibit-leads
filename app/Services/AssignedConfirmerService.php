<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ActivityLog;
use App\Models\AssignedConfirmer;
use App\Models\Lead;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignedConfirmerService
{
    public $module_name = 'assigned_confirmers';
    public $last_id = null;

    /**
     * index of assigned employee service
     *
     * @return Collection
     */
    public function indexLeadsOfAssignedConfirmer(): Collection
    {
        // $assigned_employees = Lead::where('is_confirm_assigned', true)->where('is_invited', false)->get();

        $assigned_leads = Lead::select('leads.*')
            ->join('assigned_confirmers', 'assigned_confirmers.lead_id', '=', 'leads.id')
            ->where('leads.is_confirm_assigned', true)
            ->where('leads.is_done', true)
            ->whereNull('assigned_confirmers.remarks')
            ->get();

        return $assigned_leads;
    }

    /**
     * index of leads of current assigned employee service
     *
     * @return Collection
     */
    public function indexLeadsOfCurrentAssignedConfirmer(): Collection
    {

        $assigned_leads = Lead::select('leads.*')
            ->join('assigned_confirmers', 'assigned_confirmers.lead_id', '=', 'leads.id')
            ->where('leads.is_confirm_assigned', true)
            ->where('leads.is_done', true)
            ->where('assigned_confirmers.employee_id', Auth::user()->employee->id)
            ->get();

        return $assigned_leads;
    }

    /**
     * create assigned employee service
     *
     * @param array $request
     * @return AssignedConfirmer
     */
    public function createAssignedConfirmer(array $request): array
    {
        try {
            DB::beginTransaction();

            foreach ($request['lead_ids'] as $lead_id) {
                $lead = Lead::find($lead_id);

                $lead = tap($lead)->update([
                    'is_confirm_assigned' => true,
                    'updated_by' => Auth::user()->employee->id
                ]);

                $assigned_confirmer = AssignedConfirmer::create([
                    'lead_id' => $lead->getKey(),
                    'employee_id' => $request['employee_id'],
                    'created_by' => Auth::user()->employee->id,
                ]);

                $this->last_id = $assigned_confirmer->id;

                $return_values = ['result' => 'success', 'message' => 'Successfully saved!', 'subject' => $this->last_id];

                //log activity
                ActivityLog::create([
                    'name' => $this->module_name,
                    'description' => $return_values['message'],
                    'event' => 'create',
                    'status' => $return_values['result'],
                    'browser' => json_encode(Helper::deviceInfo()),
                    'properties' => '{"lead_id":' . $lead_id . ',"employee_id":' . $request['employee_id'] . '}',
                    'causer_id' => Auth::user()->id,
                    'subject_id' => $return_values['subject']
                ]);
            }

            return $return_values;
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
                'properties' => json_encode($request),
                'causer_id' => Auth::user()->id,
                'subject_id' => $return_values['subject']
            ]);

            return $return_values;
        }
        DB::commit();
    }

    /**
     * update assigned employee service
     *
     * @param array $request
     * @param AssignedConfirmer $assignedConfirmer
     * @return AssignedConfirmer
     */
    public function updateAssignedConfirmer(array $request): array
    {
        try {
            foreach ($request['lead_ids'] as $lead_id) {
                $assigned_confirmer = AssignedConfirmer::where('lead_id', $lead_id)->first();

                if ($assigned_confirmer) {
                    $assigned_confirmer->update([
                        'employee_id' => $request['employee_id'],
                        'updated_by' => Auth::user()->employee->id
                    ]);

                    $this->last_id = $assigned_confirmer->id;

                    $return_values = ['result' => 'success', 'message' => 'Successfully reassigned!', 'subject' => $this->last_id];

                    //log activity
                    ActivityLog::create([
                        'name' => $this->module_name,
                        'description' => $return_values['message'],
                        'event' => 'update',
                        'status' => $return_values['result'],
                        'browser' => json_encode(Helper::deviceInfo()),
                        'properties' => '{"lead_id":' . $lead_id . ',"employee_id":' . $request['employee_id'] . '}',
                        'causer_id' => Auth::user()->id,
                        'subject_id' => $return_values['subject']
                    ]);
                }
            }

            return $return_values;
        } catch (Exception $e) {
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
    }

    /**
     * delete assigned employee service
     *
     * @param AssignedConfirmer $assignedConfirmer
     * @return boolean
     */
    public function deleteAssignedConfirmer(AssignedConfirmer $assignedConfirmer): bool
    {
        return $assignedConfirmer->delete;
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
            foreach ($request['lead_ids'] as $lead_id) {
                $lead = Lead::find($lead_id);

                // update lead information
                $lead->update([
                    'is_confirm_assigned' => false,
                    'updated_by' => Auth::user()->employee->id
                ]);

                $this->last_id = $lead->assignedConfirmer->id;

                $return_values = ['result' => 'success', 'message' => 'Successfully removed!', 'subject' => $this->last_id];

                // delete assigned confirmer
                $lead->assignedConfirmer->delete();

                //log activity
                ActivityLog::create([
                    'name' => $this->module_name,
                    'description' => $return_values['message'],
                    'event' => 'delete',
                    'status' => $return_values['result'],
                    'browser' => json_encode(Helper::deviceInfo()),
                    'properties' => '{"lead_id":' . $lead_id . ',"employee_id":""}',
                    'causer_id' => Auth::user()->id,
                    'subject_id' => $return_values['subject']
                ]);
            }

            return $return_values;
        } catch (Exception $e) {
            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];

            //log activity
            ActivityLog::create([
                'name' => $this->module_name,
                'description' => $return_values['message'],
                'event' => 'delete',
                'status' => $return_values['result'],
                'browser' => json_encode(Helper::deviceInfo()),
                'properties' => json_encode($request),
                'causer_id' => Auth::user()->id,
                'subject_id' => $return_values['subject']
            ]);

            return $return_values;
        }
    }

    /**
     * confirm lead service
     *
     * @param Lead $lead
     * @param array $request
     * @return array
     */
    public function confirmLead(array $request): array
    {
        try {
            DB::beginTransaction();

            $assigned_confirmer = AssignedConfirmer::where('lead_id', $request['lead_id'])->first();
            if ($assigned_confirmer) {
                $assigned_confirmer->update([
                    'remarks' => $request['remarks'],
                    'lead_status' => $request['lead_status'],
                    'updated_by' => Auth::user()->employee->id,
                ]);
            } else {
                $assigned_confirmer = AssignedConfirmer::create([
                    'lead_id' => $request['lead_id'],
                    'employee_id' => Auth::user()->employee->id,
                    'remarks' => $request['remarks'],
                    'lead_status' => $request['lead_status'],
                    'created_by' => Auth::user()->employee->id,
                ]);

                $assigned_confirmer->lead->update([
                    'is_confirm_assigned' => true,
                    'is_done_confirmed' => false,
                    'updated_by' => Auth::user()->employee->id
                ]);
            }

            $this->last_id = $assigned_confirmer->id;
            $return_values = ['result' => 'success', 'message' => 'Successfully confirmed!', 'subject' => $this->last_id];

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
    }

    /**
     * edit confirmed lead service
     *
     * @param array $request
     * @return array
     */
    public function editConfirmedLead(array $request): array
    {
        try {
            DB::beginTransaction();

            $assigned_confirmer = AssignedConfirmer::where('lead_id', $request['lead_id'])->first();
            $assigned_confirmer->update([
                'remarks' => $request['remarks'],
                'lead_status' => $request['lead_status'],
                'updated_by' => Auth::user()->employee->id,
            ]);

            $this->last_id = $assigned_confirmer->id;
            $return_values = ['result' => 'success', 'message' => 'Successfully confirmed!', 'subject' => $this->last_id];

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
    }

    /**
     * remove multiple confirmed lead
     *
     * @param array $request
     * @return array
     */
    public function removeConfirmedLead(array $request): array
    {
        try {
            DB::beginTransaction();

            foreach ($request['lead_ids'] as $lead_id) {
                $lead = Lead::find($lead_id);
                $lead = $lead->update([
                    'is_done_confirmed' => false,
                    'is_confirm_assigned' => false,
                    'updated_by' => Auth::user()->employee->id
                ]);

                $this->last_id = $lead->assignedConfirmer->id;
                $lead->assignedConfirmer->delete();

                $return_values = ['result' => 'success', 'message' => 'Successfully removed!', 'subject' => $this->last_id];

                //log activity
                ActivityLog::create([
                    'name' => $this->module_name,
                    'description' => $return_values['message'],
                    'event' => 'delete',
                    'status' => $return_values['result'],
                    'browser' => json_encode(Helper::deviceInfo()),
                    'properties' => '{"lead_id":' . $lead_id . '}',
                    'causer_id' => Auth::user()->id,
                    'subject_id' => $return_values['subject']
                ]);
            }

            return $return_values;
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
                'properties' => json_encode($request),
                'causer_id' => Auth::user()->id,
                'subject_id' => $return_values['subject']
            ]);

            return $return_values;
        }
        DB::commit();
    }
}
