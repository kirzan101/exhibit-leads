<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\AssignedConfirmerFormRequest;
use App\Http\Resources\LeadResource;
use App\Models\AssignedConfirmer;
use App\Models\Employee;
use App\Models\Lead;
use App\Services\AssignedConfirmerService;
use App\Services\EmployeeService;
use App\Services\LeadService;
use App\Services\PropertyService;
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

    public function __construct(AssignedConfirmerService $assignedConfirmerService, EmployeeService $employeeService, LeadService $leadService, PropertyService $propertyService, VenueService $venueService)
    {
        $this->assignedConfirmerService = $assignedConfirmerService;
        $this->employeeService = $employeeService;
        $this->leadService = $leadService;
        $this->propertyService = $propertyService;
        $this->venueService = $venueService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('read', AssignedConfirmer::class);

        $isConfirmer = (Auth::user()->employee->userGroup->name == 'confirmers') ?? false;
        
        $leads = LeadResource::collection($this->assignedConfirmerService->indexLeadsOfAssignedConfirmer());

        if($isConfirmer) {
            $leads = LeadResource::collection($this->assignedConfirmerService->indexLeadsOfCurrentAssignedConfirmer());
        }

        return Inertia::render('AssignedConfirmers/IndexAssignedConfirmer', [
            'leads' => $leads,
            'employees' => $this->employeeService->indexConfirmer(),
            'properties' => $this->propertyService->indexProperty(),
            'venue_list' => $this->venueService->indexVenueService(),
            'status_list' => Helper::leadConfirmerStatus(),
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
            return redirect()->route('invites')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('invites')->with('success', 'Successfully assigned!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('read', AssignedConfirmer::class);

        $lead = Lead::where('id', $id)->where('is_assigned', true)->first();

        if ($lead) {
            $employee = Employee::find($lead->employee_id);
            return Inertia::render('AssignedConfirmers/ShowAssignedConfirmer', [
                'lead' => $this->leadService->showLead($lead),
                'assigned_confirmer' => ($employee) ? $employee->getFullName() : '-'
            ]);
        }

        return redirect()->route('assigned-confirmers.index')->with('error', 'Lead not found.');
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

        try {
            DB::beginTransaction();

            $this->assignedConfirmerService->updateAssignedConfirmer($request->toArray());
        } catch (Exception $ex) {

            DB::rollBack();
            return redirect()->route('leads.index')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('assigned-confirmers.index')->with('success', 'Successfully reassigned!');
    }

    public function removeAssignment(Request $request)
    {
        $this->authorize('create', AssignedEmployee::class);

        $request = $request->validate([
            'lead_ids' => 'required|array'
        ]);

        $result = $this->assignedConfirmerService->removedAssigned($request);

        if (!$result) {
            return redirect()->route('assigned-confirmers.index')->with('error', 'Error on removing assignment!');
        }

        return redirect()->route('assigned-confirmers.index')->with('success', 'Successfully removed assignment!');
    }
}
