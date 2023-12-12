<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\LeadFormRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use App\Services\EmployeeService;
use App\Services\LeadService;
use App\Services\PropertyService;
use App\Services\SourceService;
use App\Services\VenueService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ExhibitController extends Controller
{
    private LeadService $leadService;
    private EmployeeService $employeeService;
    private VenueService $venueService;
    private PropertyService $propertyService;
    private SourceService $sourceService;

    public function __construct(
        LeadService $leadService,
        EmployeeService $employeeService,
        VenueService $venueService,
        PropertyService $propertyService,
        SourceService $sourceService
    ) {
        $this->leadService = $leadService;
        $this->employeeService = $employeeService;
        $this->venueService = $venueService;
        $this->propertyService = $propertyService;
        $this->sourceService = $sourceService;
    }

    /**
     * Get the paginated Exhibit leads
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $this->authorize('read', Exhibit::class);

        //set default value for lead name
        $sort_by = $request->sort_by;
        if ($request->sort_by == 'lead_full_name') {
            $request->merge(['sort_by' => 'last_name']);
        }

        // set default to desc
        if ($request->is_sort_desc == null) {
            $request->merge(['is_sort_desc' => true]);
        }

        $leads = $this->leadService->indexPaginateExhibitLead($request->toArray());

        $leads = LeadResource::collection($leads);

        // set the default exhibitor.
        // replace value on live
        $exhibitor = $this->employeeService->indexExhibitor()->sortByDesc('id')->first();

        return Inertia::render('Exhibits/IndexPaginateExhibit', [
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'occupation' => $request->occupation,
            'venue_id' => $request->venue_id,
            'source_name' => $request->source_name,
            'module' => 'exhibits',
            'items' => $leads,
            // 'employees' => $this->employeeService->indexTeamLeadEmployees(Auth::user()->employee->id),
            'employees' => $this->employeeService->indexTeamLeadEmployeesByCode('EXHIBIT'),
            'occupation_list' => Helper::occupationList(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => Helper::leadSource('EXHIBIT'),
            'exhibitors' => $this->employeeService->indexExhibitor(),
            'exhibitor' => $exhibitor->id // add value if only one exhibitor must be assign
        ]);
    }

    /**
     * show Exhibit Lead details
     *
     * @param Lead $lead
     * @return void
     */
    public function showLead(Lead $lead)
    {
        $this->authorize('read', Exhibit::class);

        return Inertia::render('Exhibits/LeadFormOfExhibit', [
            'is_disabled' => true,
            'form_type' => 'exhibits',
            'lead' => new LeadResource($this->leadService->showLead($lead)),
            'properties' => $this->propertyService->indexProperty(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => $this->sourceService->indexSource(),
        ]);
    }

    /**
     * Edit Exhibit Lead details
     *
     * @param Lead $lead
     * @return void
     */
    public function editLead(Lead $lead)
    {
        $this->authorize('update', Exhibit::class);

        return Inertia::render('Exhibits/LeadFormOfExhibit', [
            'is_disabled' => false,
            'form_type' => 'exhibits',
            'lead' => new LeadResource($this->leadService->showLead($lead)),
            'properties' => $this->propertyService->indexProperty(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => $this->sourceService->indexSource(),
        ]);
    }

    /**
     * Update Exhibit Lead details
     *
     * @param LeadFormRequest $request
     * @param Lead $lead
     * @return void
     */
    public function updateLead(LeadFormRequest $request, Lead $lead)
    {
        $this->authorize('update', Exhibit::class);
        
        ['result' => $result, 'message' => $message] = $this->leadService->updateLead($request->toArray(), $lead);

        return redirect()->route('exhibit-lead-show', $lead)->with($result, $message);
    }
}
