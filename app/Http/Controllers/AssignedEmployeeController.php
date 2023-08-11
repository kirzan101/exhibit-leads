<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\AssignedEmployeeFormRequest;
use App\Http\Requests\LeadFormRequest;
use App\Http\Resources\LeadResource;
use App\Models\AssignedEmployee;
use App\Models\Employee;
use App\Models\Lead;
use App\Services\AssignedEmployeeService;
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

class AssignedEmployeeController extends Controller
{
    private AssignedEmployeeService $assignedEmployeeService;
    private LeadService $leadService;
    private EmployeeService $employeeService;
    private VenueService $venueService;
    private SourceService $sourceService;
    private PropertyService $propertyService;

    public function __construct(
        AssignedEmployeeService $assignedEmployeeService,
        LeadService $leadService,
        EmployeeService $employeeService,
        VenueService $venueService,
        SourceService $sourceService,
        PropertyService $propertyService
    ) {
        $this->assignedEmployeeService = $assignedEmployeeService;
        $this->leadService = $leadService;
        $this->employeeService = $employeeService;
        $this->venueService = $venueService;
        $this->sourceService = $sourceService;
        $this->propertyService = $propertyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('read', AssignedEmployee::class);

        $isEmployee = (Auth::user()->employee->userGroup->name == 'employees') ?? false;

        $leads = LeadResource::collection($this->assignedEmployeeService->indexAssignedEmployee());

        if ($isEmployee) {
            $leads = LeadResource::collection($this->assignedEmployeeService->indexCurrentAssignedEmployee());
        }

        return Inertia::render('AssignedEmployees/IndexAssignedEmployee', [
            'leads' => $leads,
            'employees' => $this->employeeService->indexEmployee(),
            'status_list' => Helper::leadStatus(),
            'occupation_list' => Helper::occupationList(),
            'venue_list' => $this->venueService->indexVenueService(),
            'per_page' => 5
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssignedEmployeeFormRequest $request)
    {
        $this->authorize('create', AssignedEmployee::class);

        ['result' => $result, 'message' => $message] = $this->assignedEmployeeService->createAssignedEmployee($request->toArray());

        return redirect()->route('leads.index')->with($result, $message);
    }

    /**
     * Show specific lead of assigned employee
     */
    public function showLead(Lead $lead)
    {
        $this->authorize('read', AssignedEmployee::class);

        return Inertia::render('AssignedEmployees/LeadFormOfAssignedEmployee', [
            'is_disabled' => true,
            'form_type' => 'assigned-employees',
            'lead' => $this->leadService->showLead($lead),
            'properties' => $this->propertyService->indexProperty(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => $this->sourceService->indexSource(),
        ]);
    }

    /**
     * Edit specigic lead of assigned employee
     *
     * @param Lead $lead
     * @return void
     */
    public function editLead(Lead $lead)
    {
        $this->authorize('update', AssignedEmployee::class);

        return Inertia::render('AssignedEmployees/LeadFormOfAssignedEmployee', [
            'is_disabled' => false,
            'form_type' => 'assigned-employees',
            'lead' => $this->leadService->showLead($lead),
            'properties' => $this->propertyService->indexProperty(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => $this->sourceService->indexSource(),
        ]);
    }
    
    /**
     * Update specific lead of assigned employee.
     */
    public function updateLead(LeadFormRequest $request, Lead $lead)
    {
        $this->authorize('update', AssignedEmployee::class);
        
        ['result' => $result, 'message' => $message] = $this->leadService->updateLead($request->toArray(), $lead);

        return redirect()->route('assigned-employees-show', $lead)->with($result, $message);
    }

    /**
     * reasssign lead to a different employee
     *
     * @param AssignedEmployeeFormRequest $request
     * @return void
     */
    public function reassignEmployee(AssignedEmployeeFormRequest $request)
    {
        $this->authorize('create', AssignedEmployee::class);

        ['result' => $result, 'message' => $message] = $this->assignedEmployeeService->updateAssignedEmployee($request->toArray());

        return redirect()->route('leads.index')->with($result, $message);
    }

    /**
     * removed assigned employee to a leads
     *
     * @param Request $request
     * @return void
     */
    public function removeAssignment(Request $request)
    {
        $this->authorize('create', AssignedEmployee::class);

        $request->validate([
            'lead_ids' => 'required|array'
        ]);

        ['result' => $result, 'message' => $message] = $this->assignedEmployeeService->removedAssigned($request->toArray());

        return redirect()->route('assigned-employees.index')->with($result, $message);
    }

    /**
     * add or update remarks of lead
     *
     * @param Request $request
     * @return void
     */
    public function remarks(Request $request)
    {
        $this->authorize('update', AssignedEmployee::class);

        $request = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'remarks' => 'required|min:2',
            'lead_status' => 'required|min:1',
            'venue_id' => 'required|exists:venues,id'
        ]);

        ['result' => $result, 'message' => $message] = $this->assignedEmployeeService->modifyRemarks($request);

        return redirect()->route('assigned-employees.index')->with($result, $message);
    }
}
