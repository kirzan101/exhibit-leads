<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Lead;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $leads = Lead::where('is_booker_assigned', false)
            ->where('is_exhibitor_assigned', false)
            ->orderBy('id', 'desc')
            ->get();

        if (Auth::user()->employee->userGroup->name == 'exhibit-admin') {
            // get all the unassigned leads
            $leads = Lead::where('is_booker_assigned', false)
                ->where('is_exhibitor_assigned', false)
                ->orderBy('id', 'desc')
                ->get();
        } else if (Auth::user()->employee->userGroup->name == 'exhibit') {
            // get the list of leads that assigned to the exhibitor
            $leads = Lead::select('leads.*')
                ->join('assigned_exhibitors', 'assigned_exhibitors.lead_id', '=', 'leads.id')
                ->where('leads.is_booker_assigned', false)
                ->where('leads.is_exhibitor_assigned', true)
                ->where('assigned_exhibitors.employee_id', '=', Auth::user()->employee->id)
                ->orderBy('leads.id', 'desc')
                ->get();
        } else if (Auth::user()->employee->userGroup->name == 'employees' || Auth::user()->employee->userGroup->name == 'confirmers') {
            // get the list of leads that was created by the employee
            $leads = Lead::where('is_booker_assigned', false)
                ->where('is_exhibitor_assigned', false)
                ->where('created_by', Auth::user()->employee->id)
                ->get();
        }

        return $leads;
    }

    /**
     * create lead service
     *
     * @param Request $request
     * @return array
     */
    public function createLead(array $request): array
    {
        if ($request['owned_gadgets']) {
            // convert array to single string
            $owned_gadgets = implode(',', $request['owned_gadgets']);
            $request['owned_gadgets'] = $owned_gadgets;
        }

        $request['created_by'] = Auth::user()->employee->id;

        try {
            $lead = Lead::create($request);

            // save file
            if ($request['contract_file']) {
                $result = Helper::uploadFile($request['contract_file'], $lead);

                if (!$result) {
                    throw ValidationException::withMessages(['error on file upload']);
                }
            }
        } catch (Exception $e) {
            return ['result' => 'error', 'message' => $e->getMessage()];
        }

        return ['result' => 'success', 'message' => 'Successfully saved!'];
    }

    /**
     * update lead
     *
     * @param array $request
     * @param Lead $lead
     * @return array
     */
    public function updateLead(array $request, Lead $lead): array
    {
        if ($request['owned_gadgets']) {
            $owned_gadgets = implode(',', $request['owned_gadgets']);
            $request['owned_gadgets'] = $owned_gadgets;
        }

        $request['updated_by'] = Auth::user()->employee->id;

        try {
            DB::beginTransaction();

            $lead = tap($lead)->update($request);
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage()];
        }
        DB::commit();

        return ['result' => 'success', 'message' => 'Succefully updated!'];
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
     * index of done lead service
     *
     * @return void
     */
    public function indexDoneLead(): Collection
    {
        $leads = Lead::where('is_done', true)
            ->where('is_confirm_assigned', false)
            ->where('is_done_confirmed', false)
            ->get();

        // if current user is confirmer, get the same venue of leads
        if (Auth::user()->employee->userGroup->name == 'confirmers') {

            // get the assigned venue of employee
            $venue_ids = Auth::user()->employee->employeeVenue->map(function (object $venue) {
                return $venue->venue_id;
            });

            $leads = Lead::where('is_done', true)
                ->where('is_done_confirmed', false)
                ->whereIn('venue_id', $venue_ids->toArray())
                ->get();
        }

        return $leads;
    }

    /**
     * Add done status on lead service
     *
     * @param Lead $lead
     * @param bool $status
     * @return array
     */
    public function done(Lead $lead, bool $status, string $employee_type): array
    {
        try {
            DB::beginTransaction();

            if ($employee_type === 'employee') {
                $lead = tap($lead)->update([
                    'is_done' => $status,
                    'updated_by' => Auth::user()->id
                ]);
            } else if ($employee_type === 'confirmer') {
                $lead = tap($lead)->update([
                    'is_done_confirmed' => $status,
                    'updated_by' => Auth::user()->id
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage()];
        }
        DB::commit();

        return ['result' => 'success', 'message' => 'Successfully Done!'];
    }

    /**
     * index of confirmed lead service
     *
     * @return void
     */
    public function indexConfirmedLead(): Collection
    {
        $leads = Lead::where('is_confirm_assigned', true)
            ->where('is_done_confirmed', true)
            ->get();

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
    public function indexPaginateLead(array $request): Paginator
    {
        $leads = Lead::where('is_booker_assigned', false)
            ->where('is_exhibitor_assigned', false);

        if (Auth::user()->employee->userGroup->name == 'exhibit-admin') {
            // get all the unassigned leads
            $leads = Lead::where('is_booker_assigned', false)
                ->where('is_exhibitor_assigned', false);
        } else if (Auth::user()->employee->userGroup->name == 'exhibit') {
            // get the list of leads that assigned to the exhibitor
            $leads = Lead::select('leads.*')
                ->join('assigned_exhibitors', 'assigned_exhibitors.lead_id', '=', 'leads.id')
                ->where('leads.is_booker_assigned', false)
                ->where('leads.is_exhibitor_assigned', true)
                ->where('assigned_exhibitors.employee_id', '=', Auth::user()->employee->id);
        } else if (Auth::user()->employee->userGroup->name == 'employees' || Auth::user()->employee->userGroup->name == 'confirmers') {
            // get the list of leads that was created by the employee
            $leads = Lead::where('is_booker_assigned', false)
                ->where('is_exhibitor_assigned', false)
                ->where('created_by', Auth::user()->employee->id);
        }

        //set default values
        $per_page = (array_key_exists('per_page', $request) && $request['per_page'] != null) ? (int)$request['per_page'] : 5;
        $sort_by = (array_key_exists('sort_by', $request) && $request['sort_by'] != null) ? $request['sort_by'] : 'id';
        $sort = 'desc';
        if (array_key_exists('is_sort_desc', $request) && $request['is_sort_desc'] != null) {
            $sort = ($request['is_sort_desc'] == 'true') ? 'desc' : 'asc';
        }

        // search filter
        if (array_key_exists('search', $request) && !empty($request['search'])) {
            $leads->where('first_name', 'LIKE', '%' . $request['search'] . '%')
                ->orWhere('last_name', 'LIKE', '%' . $request['search'] . '%')
                ->orWhere('occupation', 'LIKE', '%' . $request['search'] . '%')
                ->orWhere('mobile_number_one', 'LIKE', '%' . $request['search'] . '%')
                ->orWhere('mobile_number_two', 'LIKE', '%' . $request['search'] . '%');
        }

        // venue filter
        if (array_key_exists('venue_id', $request) && !empty($request['venue_id'])) {
            $leads->where('venue_id', $request['venue_id']);
        }

        // source filter
        if (array_key_exists('source_name', $request) && !empty($request['source_name'])) {
            [$prefix, $suffix] = explode("-", $request['source_name']);
            $leads->where('source_prefix', $prefix)
                ->where('source', $suffix);
        }

        // occupation
        if (array_key_exists('occupation', $request) && !empty($request['occupation'])) {
            $leads->where('occupation', $request['occupation']);
        }

        return $leads->orderBy($sort_by, $sort)->paginate($per_page);
    }
}
