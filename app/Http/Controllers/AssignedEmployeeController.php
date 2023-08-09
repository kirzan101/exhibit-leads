<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\AssignedEmployeeFormRequest;
use App\Http\Resources\LeadResource;
use App\Models\AssignedEmployee;
use App\Models\Employee;
use App\Models\Lead;
use App\Services\AssignedEmployeeService;
use App\Services\EmployeeService;
use App\Services\LeadService;
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

    public function __construct(AssignedEmployeeService $assignedEmployeeService, LeadService $leadService, EmployeeService $employeeService, VenueService $venueService)
    {
        $this->assignedEmployeeService = $assignedEmployeeService;
        $this->leadService = $leadService;
        $this->employeeService = $employeeService;
        $this->venueService = $venueService;
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
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('read', AssignedEmployee::class);

        $lead = Lead::where('id', $id)->where('is_assigned', true)->first();

        if ($lead) {
            $employee = $lead->assignedEmployee->employee;
            return Inertia::render('AssignedEmployees/ShowAssignedEmployee', [
                'lead' => $this->leadService->showLead($lead),
                'assigned_employee' => ($employee) ? $employee->getFullName() : '-'
            ]);
        }

        return redirect()->route('assigned-employees.index')->with('error', 'Lead not found.');
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
