<?php

namespace App\Services;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Collection;

class ReportService
{
    /**
     * Get Conirmed report
     *
     * @param array $request
     * @return Collection
     */
    public function confirmedReport(array $request) : Collection
    {
        $leads = Lead::select('leads.*')
            ->join('assigned_employees', 'assigned_employees.lead_id', 'leads.id')
            ->join('assigned_confirmers', 'assigned_confirmers.lead_id', 'leads.id')
            ->join('assigned_exhibitors', 'assigned_exhibitors.lead_id', 'leads.id')
            ->where('leads.is_confirm_assigned', true)
            ->where('leads.is_done_confirmed', true);

        //set default values
        $sort_by = (array_key_exists('sort_by', $request) && $request['sort_by'] != null) ? $request['sort_by'] : 'id';
        $sort = 'desc';
        if (array_key_exists('is_sort_desc', $request) && $request['is_sort_desc'] != null) {
            $sort = ($request['is_sort_desc'] == 'true') ? 'desc' : 'asc';
        }

        // search filter
        if (array_key_exists('search', $request) && !empty($request['search'])) {
            $leads->where('leads.first_name', 'LIKE', '%' . $request['search'] . '%')
                ->orWhere('leads.last_name', 'LIKE', '%' . $request['search'] . '%')
                ->orWhere('leads.occupation', 'LIKE', '%' . $request['search'] . '%')
                ->orWhere('leads.mobile_number_one', 'LIKE', '%' . $request['search'] . '%')
                ->orWhere('leads.mobile_number_two', 'LIKE', '%' . $request['search'] . '%');
        }

        // venue filter
        if (array_key_exists('venue_id', $request) && !empty($request['venue_id'])) {
            $leads->where('leads.venue_id', $request['venue_id']);
        }

        // source filter
        if (array_key_exists('source_name', $request) && !empty($request['source_name'])) {
            [$prefix, $suffix] = explode("-", $request['source_name']);
            $leads->where('leads.source_prefix', $prefix)
                ->where('leads.source', $suffix);
        }

        // occupation
        if (array_key_exists('occupation', $request) && !empty($request['occupation'])) {
            $leads->where('leads.occupation', $request['occupation']);
        }

        // booker filter
        if (array_key_exists('employee_id', $request) && !empty($request['employee_id'])) {
            $leads->where('assigned_employees.employee_id', $request['employee_id']);
        }

        // confirmer filter
        if (array_key_exists('confirmer_id', $request) && !empty($request['confirmer_id'])) {
            $leads->where('assigned_confirmers.employee_id', $request['confirmer_id']);
        }

        // exhibitor filter
        if (array_key_exists('exhibitor_id', $request) && !empty($request['exhibitor_id'])) {
            $leads->where('assigned_confirmers.employee_id', $request['exhibitor_id']);
        }

        return $leads->orderBy($sort_by, $sort)->get();
    }
}
