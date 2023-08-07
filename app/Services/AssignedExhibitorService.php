<?php

namespace App\Services;

use App\Models\AssignedExhibitor;
use App\Models\Lead;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class AssignedExhibitorService
{
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
            ->get();

        return $leads;
    }

    /**
     * create assigned exhibitor service
     *
     * @param array $request
     * @return boolean
     */
    public function createAssignedExhbitor(array $request): bool
    {
        try {
            foreach ($request['lead_ids'] as $lead) {
                AssignedExhibitor::create([
                    'lead_id' => $lead,
                    'employee_id' => $request['employee_id'],
                    'created_by' => Auth::user()->employee->id,
                ]);

                $lead = Lead::find($lead);
                $lead->update([
                    'is_exhibitor_assigned' => true,
                    'updated_by' => Auth::user()->employee->id
                ]);
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * update assigned exhibitor service
     *
     * @param array $request
     * @return boolean
     */
    public function updateAssignedExhibitor(array $request): bool
    {
        try {
            foreach ($request['lead_ids'] as $lead) {
                AssignedExhibitor::create([
                    'lead_id' => $lead,
                    'employee_id' => $request['employee_id'],
                    'created_by' => Auth::user()->employee->id,
                ]);

                $lead = Lead::find($lead);
                $lead->update([
                    'is_exhibitor_assigned' => true,
                    'updated_by' => Auth::user()->employee->id
                ]);
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * delete assigned exhibitor service
     *
     * @param AssignedExhibitor $assignedExhibitor
     * @return boolean
     */
    public function deleteAssignedExhibitor(AssignedExhibitor $assignedExhibitor): bool
    {
        try {
            $lead = Lead::find($assignedExhibitor->lead_id);
            $lead->update([
                'is_exhibitor_assigned' => false,
                'updated_by' => Auth::user()->employee->id
            ]);

            $assignedExhibitor->delete();
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
    public function removedAssignedExhibitor(array $request) : bool
    {
        try {
            foreach ($request['lead_ids'] as $lead) {
                $lead = Lead::find($lead);
                $lead->update([
                    'is_exhibitor_assigned' => false,
                    'updated_by' => Auth::user()->employee->id
                ]);

                $assigned_exhibitor = AssignedExhibitor::where('lead_id', $lead->id);
                $assigned_exhibitor->delete();
            }
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }
}
