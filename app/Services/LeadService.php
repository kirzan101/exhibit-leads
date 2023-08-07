<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Lead;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LeadService
{
    /**
     * index of lead service
     * returned leads depends on the user group of the current logged in employee
     *
     * @return void
     */
    public function indexLead(): Collection
    {
        $lead = Lead::where('is_booker_assigned', false)->orderBy('id', 'desc')->get();

        if (Auth::user()->employee->userGroup->name == 'exhibit-admin') {
            // get all the unassigned leads
            $lead = Lead::where('is_booker_assigned', false)
                ->where('is_exhibitor_assigned', false)
                ->orderBy('id', 'desc')
                ->get();
        } else if(Auth::user()->employee->userGroup->name == 'exhibit') {
            // get the list of leads that assigned to the exhibitor
            $lead = Lead::select('leads.*')
                ->join('assigned_exhibitors', 'assigned_exhibitors.lead_id', '=', 'leads.id')
                ->where('leads.is_booker_assigned', false)
                ->where('leads.is_exhibitor_assigned', true)
                ->where('assigned_exhibitors.employee_id', '=', Auth::user()->employee->id)
                ->orderBy('leads.id', 'desc')
                ->get();
        } else if(Auth::user()->employee->userGroup->name == 'employees' || Auth::user()->employee->userGroup->name == 'confirmers') {
            // get the list of leads that assigned to the exhibitor of the employee
            $lead = Lead::select('leads.*')
                ->join('assigned_exhibitors', 'assigned_exhibitors.lead_id', '=', 'leads.id')
                ->where('leads.is_booker_assigned', false)
                ->where('leads.is_exhibitor_assigned', true)
                ->where('assigned_exhibitors.employee_id', '=', Auth::user()->employee->exhibitor_id)
                ->orderBy('leads.id', 'desc')
                ->get();
        }

        return $lead;
    }

    /**
     * create lead service
     *
     * @param Request $request
     * @return void
     */
    public function createLead(array $request): Lead
    {
        if ($request['owned_gadgets']) {
            // convert array to single string
            $owned_gadgets = implode(',', $request['owned_gadgets']);
            $request['owned_gadgets'] = $owned_gadgets;
        }

        $request['created_by'] = Auth::user()->employee->id;

        $lead = Lead::create($request);

        if ($request['contract_file']) {
            $result = Helper::uploadFile($request['contract_file'], $lead);

            if (!$result) {
                throw ValidationException::withMessages(['error on file upload']);
            }
        }

        return $lead;
    }

    /**
     * update lead
     *
     * @param array $request
     * @param Lead $lead
     * @return Lead
     */
    public function updateLead(array $request, Lead $lead): Lead
    {
        if ($request['owned_gadgets']) {
            $owned_gadgets = implode(',', $request['owned_gadgets']);
            $request['owned_gadgets'] = $owned_gadgets;
        }

        $request['updated_by'] = Auth::user()->employee->id;

        $lead = tap($lead)->update($request);

        return $lead;
    }

    /**
     * delete lead service
     *
     * @param Lead $lead
     * @return boolean
     */
    public function deleteLead(Lead $lead): bool
    {
        $lead = $lead->delete();

        return $lead;
    }

    /**
     * show lead service
     *
     * @param Lead $model
     * @return Lead
     */
    public function showLead(Lead $model): Lead
    {
        if ($model->owned_gadgets) {
            $owned_gadgets = $model->owned_gadgets;
            $arrayed_owned_gadgets = explode(',', $owned_gadgets);

            $lead = new Lead;
            $lead = $model;
            $lead->owned_gadgets = $arrayed_owned_gadgets;
        }

        if ($model->contract_file) {
            $lead->contract_file = response()->file(public_path($model->contract_file))->getFile(); //$lead->getUploadedFile();
        }

        return $lead;
    }

    /**
     * modify remarks of lead service
     *
     * @param array $request
     * @return boolean
     */
    public function modifyRemarks(array $request): bool
    {
        $lead = Lead::find($request['lead_id']);

        return $lead->update([
            'remarks' => $request['remarks'],
            'lead_status' => $request['lead_status'],
            'venue_id' => $request['venue_id'],
            'updated_by' => Auth::user()->employee->id
        ]);
    }

    /**
     * index of invited lead service
     *
     * @return void
     */
    public function indexInvitedLead(bool $invited): Collection
    {
        $leads = Lead::where('is_invited', $invited)
            ->where('is_confirm_assigned', false)
            ->get();

        // if current user is confirmer, get the same venue of leads
        if (Auth::user()->employee->userGroup->name == 'confirmers') {

            // get the assigned venue of employee
            $venue_ids = Auth::user()->employee->employeeVenue->map(function (object $venue) {
                return $venue->venue_id;
            });

            $leads = Lead::where('is_invited', $invited)
                ->where('is_confirm_assigned', false)
                ->whereIn('venue_id', $venue_ids)
                ->get();
        }

        return $leads;
    }

    /**
     * Add invite status on lead service
     *
     * @param Lead $lead
     * @param bool $status
     * @return Lead
     */
    public function inviteLead(Lead $lead, bool $status): Lead
    {
        $lead = tap($lead)->update([
            'is_invited' => $status,
            'updated_by' => Auth::user()->id
        ]);

        return $lead;
    }

    /**
     * Confirm the lead
     *
     * @param Lead $lead
     * @param array $request
     * @return Lead
     */
    public function confirmLead(Lead $lead, array $request): Lead
    {
        $lead = tap($lead)->update([
            'confirmer_remarks' => $request['confirmer_remarks'],
            'lead_status_confirmer' => $request['lead_status_confirmer'],
            'is_confirm_assigned' => true,
            'updated_by' => Auth::user()->id
        ]);

        return $lead;
    }

    /**
     * remove confirm status
     *
     * @param Lead $lead
     * @return Lead
     */
    public function removeConfirmLead(Lead $lead): Lead
    {
        $lead = tap($lead)->update([
            'confirmer_remarks' => null,
            'lead_status_confirmer' => null,
            'is_confirm_assigned' => false,
            'updated_by' => Auth::user()->id
        ]);

        return $lead;
    }

    /**
     * index of confirmed lead service
     *
     * @return void
     */
    public function indexConfirmedLead(): Collection
    {
        $leads = Lead::where('is_invited', true)
            ->where('is_confirm_assigned', true)
            ->whereNotNull('confirmer_remarks')
            ->get();

        if (Auth::user()->employee->userGroup->name == 'confirmers') {
            Lead::select('leads.*')
                ->where('leads.is_confirm_assigned', true)
                ->where('leads.is_invited', true)
                ->where('leads.venue_id', '=', Auth::user()->employee->venue_id)
                ->whereNotNull('confirmer_remarks')
                ->get();
        }

        return $leads;
    }

    /**
     * Mark lead as showed
     *
     * @param array $request
     * @return void
     */
    public function showed(array $request)
    {
        $lead = Lead::find($request['lead_id']);

        $lead = tap($lead)->update([
            'is_showed' => $request['status'],
            'updated_by' => Auth::user()->employee->id
        ]);

        return $lead;
    }

    /**
     * index page with paginate
     *
     * @return Paginator
     */
    public function indexPaginateLead(int $perPage): Paginator
    {
        $lead = Lead::where('is_booker_assigned', false)->paginate($perPage);

        return $lead;
    }
}
