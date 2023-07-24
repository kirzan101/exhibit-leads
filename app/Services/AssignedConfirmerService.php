<?php

namespace App\Services;

use App\Models\AssignedConfirmer;
use App\Models\Lead;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class AssignedConfirmerService
{
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
            ->where('leads.is_invited', true)
            ->get();

        return $assigned_leads;
    }

    /**
     * create assigned employee service
     *
     * @param array $request
     * @return AssignedConfirmer
     */
    public function createAssignedConfirmer(array $request): bool
    {
        try {
            foreach ($request['lead_ids'] as $lead) {
                AssignedConfirmer::create([
                    'lead_id' => $lead,
                    'employee_id' => $request['employee_id'],
                    'created_by' => Auth::user()->employee->id,
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
     * @param AssignedConfirmer $assignedConfirmer
     * @return AssignedConfirmer
     */
    public function updateAssignedConfirmer(array $request): bool
    {
        try {
            foreach ($request['lead_ids'] as $lead) {
                $assignedConfirmer = AssignedConfirmer::where('lead_id', $lead)->first();

                if($assignedConfirmer) {
                    $assignedConfirmer->update([
                        'employee_id' => $request['employee_id'],
                        'updated_by' => Auth::user()->employee->id
                    ]);
                }
            }
        } catch (Exception $ex) {
            return false;
        }

        // $assigned_employee = tap($assignedConfirmer)->update($request);

        return true;
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
     * @return boolean
     */
    public function removedAssigned(array $request): bool
    {
        try {
            foreach ($request['lead_ids'] as $lead) {
                $lead = Lead::find($lead);

                $assignedConfirmer = AssignedConfirmer::where('lead_id', $lead->id)->first();

                // delete assigned confirmer
                $assignedConfirmer->delete();

                // update lead information
                $lead->update([
                    'is_confirm_assigned' => false,
                    'confirmer_remarks' => null,
                    'updated_by' => Auth::user()->employee->id
                ]);
            }
        } catch (Exception $ex) {
            return false;
        }

        return true;
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
            ->where('leads.is_invited', true)
            ->where('assigned_confirmers.employee_id', Auth::user()->employee->id)
            ->get();


        return $assigned_leads;
    }
}
