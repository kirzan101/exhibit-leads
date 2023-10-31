<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\AssignedExhibitorFormRequest;
use App\Http\Requests\LeadFormRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\LeadResource;
use App\Models\AssignedExhibitor;
use App\Models\Lead;
use App\Services\AssignedExhibitorService;
use App\Services\EmployeeService;
use App\Services\LeadService;
use App\Services\PropertyService;
use App\Services\SourceService;
use App\Services\VenueService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AssignedExhibitorController extends Controller
{
    private AssignedExhibitorService $assignedExhibitorService;
    private LeadService $leadService;
    private EmployeeService $employeeService;
    private PropertyService $propertyService;
    private VenueService $venueService;
    private SourceService $sourceService;

    public function __construct(
        AssignedExhibitorService $assignedExhibitorService,
        LeadService $leadService,
        EmployeeService $employeeService,
        VenueService $venueService,
        PropertyService $propertyService,
        SourceService $sourceService
    ) {
        $this->assignedExhibitorService = $assignedExhibitorService;
        $this->leadService = $leadService;
        $this->employeeService = $employeeService;
        $this->venueService = $venueService;
        $this->propertyService = $propertyService;
        $this->sourceService = $sourceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('read', AssignedExhibitor::class);

        //set default value for lead name
        $sort_by = $request->sort_by;
        if ($request->sort_by == 'lead_full_name') {
            $request->merge(['sort_by' => 'last_name']);
        }

        // set default to desc
        if ($request->is_sort_desc == null) {
            $request->merge(['is_sort_desc' => true]);
        }

        // set default value for start to
        if (!$request->has('start_to')) {
            $request->merge(['start_to' => Carbon::now()->format('Y-m-d')]);
        }

        // set default value for end to
        if (!$request->has('end_to')) {
            $request->merge(['end_to' => Carbon::now()->format('Y-m-d')]);
        }

        $isEmployee = (Auth::user()->employee->userGroup->name == 'employees') ?? false;

        $leads = LeadResource::collection($this->assignedExhibitorService->indexAssignedExhibitorPaginate($request->toArray()));

        if ($isEmployee) {
            $leads = LeadResource::collection($this->assignedExhibitorService->indexAssignedExhibitorPaginate($request->toArray()));
        }

        return Inertia::render('AssignedExhibitors/IndexPaginateAssignedExhibitor', [
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'occupation' => $request->occupation,
            'venue_id' => $request->venue_id,
            'source_name' => $request->source_name,
            'module' => 'assigned-exhibitors',
            'items' => $leads,
            'employees' => $this->employeeService->indexExhibitor(),
            'occupation_list' => Helper::occupationList(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => Helper::leadSource(null),
            'status_list' => Helper::leadStatus(),
            'start_to' => $request->start_to,
            'end_to' => $request->end_to,
            'lead_status' => $request->lead_status,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssignedExhibitorFormRequest $request)
    {
        $this->authorize('create', AssignedExhibitor::class);

        ['result' => $result, 'message' => $message, 'subject' => $subject] = $this->assignedExhibitorService->createAssignedExhbitor($request->toArray());

        // return redirect()->route('leads.index')->with($result, $message);
        return redirect()->back()->with($result, $message);
    }

    /**
     * Show specific lead of assigned employee
     */
    public function showLead(Lead $lead)
    {
        $this->authorize('read', AssignedExhibitor::class);

        return Inertia::render('AssignedExhibitors/LeadFormOfAssignedExhibitor', [
            'is_disabled' => true,
            'form_type' => 'assigned-exhibitors',
            'lead' => new LeadResource($this->leadService->showLead($lead)),
            'properties' => $this->propertyService->indexProperty(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => $this->sourceService->indexSource(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('read', AssignedExhibitor::class);

        $lead = Lead::where('id', $id)->where('is_exhibitor_assigned', true)->first();

        if ($lead) {
            $exhibitor = $lead->assignedExhibitor->employee;

            return Inertia::render('AssignedExhibitors/ShowAssignedExhibitor', [
                'lead' => new LeadResource($lead),
                'assigned_exhibitor' => ($exhibitor) ? $exhibitor->getFullName() : '-',
                'properties' => $this->propertyService->indexProperty(),
                'venues' => $this->venueService->indexVenueService(),
                'sources' => $this->sourceService->indexSource(),
            ]);
        }

        return redirect('/assigned-exhibitors')->with('error', 'Lead not found.');
    }

    /**
     * Edit specific lead of assigned exhibitor
     *
     * @param Lead $lead
     * @return void
     */
    public function editLead(Lead $lead)
    {
        $this->authorize('update', AssignedExhibitor::class);

        return Inertia::render('AssignedExhibitors/LeadFormOfAssignedExhibitor', [
            'is_disabled' => false,
            'form_type' => 'assigned-exhibitors',
            'lead' => new LeadResource($this->leadService->showLead($lead)),
            'properties' => $this->propertyService->indexProperty(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => $this->sourceService->indexSource(),
        ]);
    }

    /**
     * Update specific lead of assigned exhibitor.
     */
    public function updateLead(LeadFormRequest $request, Lead $lead)
    {
        $this->authorize('update', AssignedExhibitor::class);

        ['result' => $result, 'message' => $message] = $this->leadService->updateLead($request->toArray(), $lead);

        return redirect()->route('assigned-exhibitors-show', $lead)->with($result, $message);
    }

    /**
     * reassigned leads to another exhibitor
     *
     * @param AssignedExhibitorFormRequest $request
     * @return void
     */
    public function reassignExhibitor(AssignedExhibitorFormRequest $request)
    {
        $this->authorize('create', AssignedExhibitor::class);

        ['result' => $result, 'message' => $message, 'subject' => $subject] = $this->assignedExhibitorService->updateAssignedExhibitor($request->toArray());

        // return redirect('/assigned-exhibitors')->with($result, $message);
        return redirect()->back()->with($result, $message);
    }

    /**
     * removed assigned employee to a leads
     *
     * @param Request $request
     * @return void
     */
    public function removeAssignment(Request $request)
    {
        $this->authorize('create', AssignedExhibitor::class);

        $request = $request->validate([
            'lead_ids' => 'required|array'
        ]);

        ['result' => $result, 'message' => $message, 'subject' => $subject] = $this->assignedExhibitorService->removedAssignedExhibitor($request);

        // return redirect('/assigned-exhibitors')->with($result, $message);
        return redirect()->back()->with($result, $message);
    }
}
