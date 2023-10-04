<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\AssignedConfirmerFormRequest;
use App\Http\Requests\LeadFormRequest;
use App\Http\Resources\LeadResource;
use App\Models\AssignedConfirmer;
use App\Models\Employee;
use App\Models\Lead;
use App\Services\AssignedConfirmerService;
use App\Services\EmployeeService;
use App\Services\LeadService;
use App\Services\PropertyService;
use App\Services\SourceService;
use App\Services\VenueService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AssignedConfirmerController extends Controller
{
    private AssignedConfirmerService $assignedConfirmerService;
    private EmployeeService $employeeService;
    private LeadService $leadService;
    private PropertyService $propertyService;
    private VenueService $venueService;
    private SourceService $sourceService;

    public function __construct(
        AssignedConfirmerService $assignedConfirmerService,
        EmployeeService $employeeService,
        LeadService $leadService,
        PropertyService $propertyService,
        VenueService $venueService,
        SourceService $sourceService
    ) {
        $this->assignedConfirmerService = $assignedConfirmerService;
        $this->employeeService = $employeeService;
        $this->leadService = $leadService;
        $this->propertyService = $propertyService;
        $this->venueService = $venueService;
        $this->sourceService = $sourceService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('read', AssignedConfirmer::class);

        $isConfirmer = (Auth::user()->employee->userGroup->name == 'confirmers') ?? false;

        $leads = LeadResource::collection($this->assignedConfirmerService->indexLeadsOfAssignedConfirmer());

        if ($isConfirmer) {
            $leads = LeadResource::collection($this->assignedConfirmerService->indexLeadsOfCurrentAssignedConfirmer());
        }

        return Inertia::render('AssignedConfirmers/IndexAssignedConfirmer', [
            'leads' => $leads,
            'employees' => $this->employeeService->indexConfirmer(),
            'properties' => $this->propertyService->indexProperty(),
            'venue_list' => $this->venueService->indexVenueService(),
            'status_list' => Helper::leadStatus(),
            'confirmer_status_list' => Helper::leadConfirmerStatus(),
            'per_page' => 5
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssignedConfirmerFormRequest $request)
    {
        $this->authorize('create', AssignedConfirmer::class);

        try {
            DB::beginTransaction();

            $this->assignedConfirmerService->createAssignedConfirmer($request->toArray());
        } catch (Exception $ex) {

            DB::rollBack();
            return redirect()->route('done')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('done')->with('success', 'Successfully assigned!');
    }

    /**
     * Display the specified resource.
     */
    public function showLead(Lead $lead)
    {
        $this->authorize('read', AssignedConfirmer::class);

        if ($lead) {
            $employee = Employee::find($lead->employee_id);
            return Inertia::render('AssignedConfirmers/LeadFormOfAssignedConfirmer', [
                'is_disabled' => true,
                'form_type' => 'assigned-confirmers',
                'lead' => $this->leadService->showLead($lead),
                'properties' => $this->propertyService->indexProperty(),
                'venues' => $this->venueService->indexVenueService(),
                'sources' => $this->sourceService->indexSource(),
            ]);
        }

        return redirect()->route('assigned-confirmers.index')->with('error', 'Lead not found.');
    }

    /**
     * Edit specific lead of assigned confirmers
     *
     * @param Lead $lead
     * @return void
     */
    public function editLead(Lead $lead)
    {
        $this->authorize('update', AssignedConfirmer::class);

        return Inertia::render('AssignedConfirmers/LeadFormOfAssignedConfirmer', [
            'is_disabled' => false,
            'form_type' => 'assigned-confirmers',
            'lead' => $this->leadService->showLead($lead),
            'properties' => $this->propertyService->indexProperty(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => $this->sourceService->indexSource(),
        ]);
    }

    /**
     * Update specific lead of assigned confirmers.
     */
    public function updateLead(LeadFormRequest $request, Lead $lead)
    {
        $this->authorize('update', AssignedConfirmer::class);

        ['result' => $result, 'message' => $message] = $this->leadService->updateLead($request->toArray(), $lead);

        return redirect()->route('assigned-confirmers-show', $lead)->with($result, $message);
    }

    /**
     * reassign leads to another confirmer
     *
     * @param AssignedConfirmerFormRequest $request
     * @return void
     */
    public function reassignConfirmer(AssignedConfirmerFormRequest $request)
    {
        $this->authorize('create', AssignedConfirmer::class);

        ['result' => $result, 'message' => $message] = $this->assignedConfirmerService->updateAssignedConfirmer($request->toArray());

        return redirect()->route('assigned-confirmers.index')->with($result, $message);
    }

    /**
     * remove assignement of leads in confirmer
     *
     * @param Request $request
     * @return void
     */
    public function removeAssignment(Request $request)
    {
        $this->authorize('create', AssignedEmployee::class);

        $request = $request->validate([
            'lead_ids' => 'required|array'
        ]);

        ['result' => $result, 'message' => $message] = $this->assignedConfirmerService->removedAssigned($request);

        return redirect()->route('assigned-confirmers.index')->with($result, $message);
    }

    /**
     * confirm lead
     *
     * @param Request $request
     * @return void
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'lead_status' => 'required',
            'remarks' => 'required'
        ]);

        ['result' => $result, 'message' => $message] = $this->assignedConfirmerService->confirmLead($request->toArray());

        return redirect()->route('confirms')->with($result, $message);
    }
}
