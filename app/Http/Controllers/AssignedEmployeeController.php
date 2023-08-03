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
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssignedEmployeeFormRequest $request)
    {
        $this->authorize('create', AssignedEmployee::class);

        try {
            DB::beginTransaction();

            $this->assignedEmployeeService->createAssignedEmployee($request->validated());
        } catch (Exception $ex) {

            DB::rollBack();
            return redirect()->route('leads.index')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('leads.index')->with('success', 'Successfully assigned!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('read', AssignedEmployee::class);

        $lead = Lead::where('id', $id)->where('is_assigned', true)->first();

        if ($lead) {
            $employee = Employee::find($lead->employee_id);
            return Inertia::render('AssignedEmployees/ShowAssignedEmployee', [
                'lead' => $this->leadService->showLead($lead),
                'assigned_employee' => ($employee) ? $employee->getFullName() : '-'
            ]);
        }

        return redirect()->route('assigned-employees.index')->with('error', 'Lead not found.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssignedEmployee $assignedEmployee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssignedEmployeeFormRequest $request, AssignedEmployee $assignedEmployee)
    {
        $this->authorize('update', AssignedEmployee::class);

        try {
            DB::beginTransaction();

            $this->assignedEmployeeService->updateAssignedEmployee($request->validated(), $assignedEmployee);
        } catch (\Throwable $th) {
            //throw $th;
        }

        return redirect()->route('assigned-employees.index')->with('success', 'Successfully reassigned');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignedEmployee $assignedEmployee)
    {
        //
    }

    public function reassignEmployee(AssignedEmployeeFormRequest $request)
    {
        $this->authorize('create', AssignedEmployee::class);

        try {
            DB::beginTransaction();

            $this->assignedEmployeeService->createAssignedEmployee($request->validated());
        } catch (Exception $ex) {

            DB::rollBack();
            return redirect()->route('leads.index')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('assigned-employees.index')->with('success', 'Successfully reassigned!');
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

        $request = $request->validate([
            'lead_ids' => 'required|array'
        ]);

        $result = $this->assignedEmployeeService->removedAssigned($request);

        if (!$result) {
            return redirect()->route('assigned-employees.index')->with('error', 'Error on removing assignment!');
        }

        return redirect()->route('assigned-employees.index')->with('success', 'Successfully removed assignment!');
    }
}
