<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Resources\LeadResource;
use App\Services\EmployeeService;
use App\Services\LeadService;
use App\Services\VenueService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LeadStatusController extends Controller
{
    private LeadService $leadService;
    private EmployeeService $employeeService;
    private VenueService $venueService;

    public function __construct(
        LeadService $leadService,
        EmployeeService $employeeService,
        VenueService $venueService
    ) {
        $this->leadService = $leadService;
        $this->employeeService = $employeeService;
        $this->venueService = $venueService;
    }

    public function index(Request $request)
    {
        $this->authorize('read', Status::class);

        //set default value for lead name
        $sort_by = $request->sort_by;
        if ($request->sort_by == 'lead_full_name') {
            $request->merge(['sort_by' => 'last_name']);
        }

        // set default to desc
        if ($request->is_sort_desc == null) {
            $request->merge(['is_sort_desc' => true]);
        }

        $leads = LeadResource::collection($this->leadService->indexPaginateLeadStatus($request->toArray()));

        // set the default exhibitor.
        // replace value on live
        $exhibitor = $this->employeeService->indexExhibitor()->first();

        return Inertia::render('LeadStatuses/IndexPaginateLeadStatus', [
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'occupation' => $request->occupation,
            'venue_id' => $request->venue_id,
            'source_name' => $request->source_name,
            'module' => 'leads',
            'items' => $leads,
            'employees' => $this->employeeService->indexEncoder(),
            'occupation_list' => Helper::occupationList(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => Helper::leadSource(null),
            'exhibitors' => $this->employeeService->indexExhibitor(),
            'exhibitor' => $exhibitor->id // add value if only one exhibitor must be assign
        ]);
    }
}
