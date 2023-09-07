<?php

namespace App\Services;

use App\Models\AssignedConfirmer;
use App\Models\Lead;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

            foreach ($request['lead_ids'] as $lead) {
                $lead = Lead::find($lead);

                $lead = tap($lead)->update([
                    'is_confirm_assigned' => true,
                    'updated_by' => Auth::user()->employee->id
                ]);

                AssignedConfirmer::create([
                    'lead_id' => $lead->getKey(),
                    'employee_id' => $request['employee_id'],
                    'created_by' => Auth::user()->employee->id,
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage()];
        }
        DB::commit();

        return ['result' => 'success', 'message' => 'Successfully saved!'];
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
            foreach ($request['lead_ids'] as $lead) {
                $assignedConfirmer = AssignedConfirmer::where('lead_id', $lead)->first();

                if ($assignedConfirmer) {
                    $assignedConfirmer->update([
                        'employee_id' => $request['employee_id'],
                        'updated_by' => Auth::user()->employee->id
                    ]);
                }
            }
        } catch (Exception $e) {
            return ['result' => 'success', 'message' => $e->getMessage()];
        }

        return ['result' => 'success', 'message' => 'Successfully saved!'];
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

                // update lead information
                $lead->update([
                    'is_confirm_assigned' => false,
                    'updated_by' => Auth::user()->employee->id
                ]);

                // delete assigned confirmer
                $lead->assignedConfirmer->delete();
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
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
            if($assigned_confirmer) {
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
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage()];
        }
        DB::commit();

        return ['result' => 'success', 'message' => 'Successfully confirmed!'];
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
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage()];
        }
        DB::commit();

        return ['result' => 'error', 'message' => 'Successfully saved!'];
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

                $lead->assignedConfirmer->delete();
            }
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage()];
        }
        DB::commit();

        return ['result' => 'success', 'message' => 'Successfully removed!'];
    }
}
