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
            ->where('leads.is_invited', false)
            ->get();

        return $assigned_leads;
    }

    /**
     * create assigned employee service
     *
     * @param array $request
     * @return AssignedEmployee
     */
    public function createAssignedEmployee(array $request): bool
    {
        try {
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
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * update assigned employee service
     *
     * @param array $request
     * @param AssignedEmployee $assignedEmployee
     * @return AssignedEmployee
     */
    public function updateAssignedEmployee(array $request, AssignedEmployee $assignedEmployee): bool
    {
        try {
            foreach ($request['lead_ids'] as $lead) {
                AssignedEmployee::create([
                    'lead_id' => $lead,
                    'employee_id' => $request['employee_id'],
                    'created_by' => Auth::user()->employee->id,
                ]);

                $lead = Lead::find($lead);
                $lead->update([
                    'is_booker_assigned' => true,
                    'remarks' => null,
                    'updated_by' => Auth::user()->employee->id
                ]);
            }
        } catch (Exception $ex) {
            return false;
        }

        // $assigned_employee = tap($assignedEmployee)->update($request);

        return true;
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
            $assignedEmployee->delete;

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
    public function removedAssigned(array $request) : bool
    {
        try {
            foreach ($request['lead_ids'] as $lead) {
                $lead = Lead::find($lead);
                $lead->update([
                    'is_booker_assigned' => false,
                    'remarks' => null,
                    'updated_by' => Auth::user()->employee->id
                ]);

                $assigned_employee = AssignedEmployee::where('lead_id', $lead->id);
                $assigned_employee->delete();
            }
        } catch (Exception $ex) {
            return false;
        }

        return true;
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
            ->where('leads.is_invited', false)
            ->where('assigned_employee.employee_id', Auth::user()->employee->id)
            ->get();

        return $assigned_leads;
    }
}
