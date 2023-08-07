<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\AssignedExhibitorFormRequest;
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
use Exception;
use Illuminate\Http\Request;
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
    public function index()
    {
        $this->authorize('read', AssignedExhibitor::class);

        $leads = $this->assignedExhibitorService->indexLeadsAssignedExhibitor();
        $employees = $this->employeeService->indexExhibitor();

        return Inertia::render('AssignedExhibitors/IndexAssignedExhibitor', [
            'leads' => LeadResource::collection($leads),
            'employees' => EmployeeResource::collection($employees),
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
        $this->authorize('create', AssignedExhibitor::class);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssignedExhibitorFormRequest $request)
    {
        $this->authorize('create', AssignedExhibitor::class);

        try {
            DB::beginTransaction();

            $this->assignedExhibitorService->createAssignedExhbitor($request->toArray());
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

        return redirect()->route('assigned-exhibitors.index')->with('error', 'Lead not found.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AssignedExhibitor $assignedExhibitor)
    {
        $this->authorize('update', AssignedExhibitor::class);

        $this->assignedExhibitorService->updateAssignedExhibitor($request->toArray(), $assignedExhibitor);

        return redirect()->route('assigned-employees.index')->with('success', 'Successfully reassigned');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignedExhibitor $assignedExhibitor)
    {
        $this->authorize('delete', AssignedExhibitor::class);
    }

    public function reassignExhibitor(AssignedExhibitorFormRequest $request)
    {
        $this->authorize('create', AssignedExhibitor::class);

        try {
            DB::beginTransaction();

            $this->assignedExhibitorService->createAssignedExhbitor($request->toArray());
        } catch (Exception $ex) {

            DB::rollBack();
            return redirect()->route('leads.index')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('assigned-exhibitors.index')->with('success', 'Successfully reassigned!');
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

        $result = $this->assignedExhibitorService->removedAssignedExhibitor($request);

        if (!$result) {
            return redirect()->route('assigned-employees.index')->with('error', 'Error on removing assignment!');
        }

        return redirect()->route('assigned-employees.index')->with('success', 'Successfully removed assignment!');
    }
}
