<?php

namespace App\Services;

use App\Models\AssignedEmployee;
use App\Models\Contract;
use App\Models\Lead;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssignedEmployeeService
{
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
                AssignedEmployee::create([
                    'lead_id' => $lead,
                    'employee_id' => $request['employee_id'],
                    'created_by' => Auth::user()->employee->id,
                ]);

                $lead = Lead::find($lead);
                $lead->update([
                    'is_booker_assigned' => true,
                    'updated_by' => Auth::user()->employee->id
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage()];
        }
        DB::commit();

        return ['result' => 'success', 'message' => 'Successfuly assigned!'];
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
                }
            }
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage()];
        }
        DB::commit();

        return ['result' => 'success', 'message' => 'Successfully updated!'];
    }

    /**
     * delete assigned employee service
     *
     * @param AssignedEmployee $assignedEmployee
     * @return boolean
     */
    public function deleteAssignedEmployee(AssignedEmployee $assignedEmployee): bool
    {
        try {

            // update lead
            $lead = Lead::find($assignedEmployee->lead_id);
            $lead->update([
                'is_booker_assigned' => false,
                'remarks' => null,
                'updated_by' => Auth::user()->employee->id
            ]);

            // delete record here
            $assignedEmployee->delete();
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * removed assigned employee in a lead
     *
     * @param array $request
     * @return boolean
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

                $lead->assignedEmployee->delete();
            }
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e];
        }

        DB::commit();
        return ['result' => 'success', 'message' => 'Successfully removed assignment'];
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

        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage()];
        }
        DB::commit();

        return ['result' => 'success', 'message' => 'Successfully saved!'];
    }
}
