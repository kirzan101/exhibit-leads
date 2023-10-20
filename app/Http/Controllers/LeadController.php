<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\LeadFormRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\LeadResource;
use App\Models\AssignedConfirmer;
use App\Models\AssignedEmployee;
use App\Models\Lead;
use App\Services\ActivityLogService;
use App\Services\AssignedEmployeeService;
use App\Services\EmployeeService;
use App\Services\EmployeeVenueService;
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

class LeadController extends Controller
{
    private LeadService $leadService;
    private EmployeeService $employeeService;
    private PropertyService $propertyService;
    private VenueService $venueService;
    private SourceService $sourceService;
    private EmployeeVenueService $employeeVenueService;
    public $module_name = 'leads';

    public function __construct(
        LeadService $leadService,
        EmployeeService $employeeService,
        PropertyService $propertyService,
        VenueService $venueService,
        SourceService $sourceService,
        EmployeeVenueService $employeeVenueService,
    ) {
        $this->leadService = $leadService;
        $this->employeeService = $employeeService;
        $this->propertyService = $propertyService;
        $this->venueService = $venueService;
        $this->sourceService = $sourceService;
        $this->employeeVenueService = $employeeVenueService;
    }

    /**
     * paginate index of Leads
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        $this->authorize('read', Lead::class);

        //set default value for lead name
        $sort_by = $request->sort_by;
        if ($request->sort_by == 'lead_full_name') {
            $request->merge(['sort_by' => 'last_name']);
        }

        // set default to desc
        if ($request->is_sort_desc == null) {
            $request->merge(['is_sort_desc' => true]);
        }

        $leads = LeadResource::collection($this->leadService->indexPaginateLead($request->toArray()));

        // set the default exhibitor.
        // replace value on live
        $exhibitor = $this->employeeService->indexExhibitor()->first();

        $sources = Helper::leadSource(NULL);
        if (Auth::user()->employee->usergroup->name == 'exhibit-admin') {
            $sources = Helper::leadSource('EXHIBIT');
        }

        return Inertia::render('Leads/IndexPaginateLead', [
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
            'sources' => $sources,
            'exhibitors' => $this->employeeService->indexExhibitor(),
            'exhibitor' => $exhibitor->id // add value if only one exhibitor must be assign
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Lead::class);

        return Inertia::render('Leads/CreateLead', [
            'properties' => $this->propertyService->indexProperty(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => $this->sourceService->indexSource(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LeadFormRequest $request)
    {
        $this->authorize('create', Lead::class);

        if (!$request->has('owned_gadgets')) {
            $request->merge(['owned_gadgets' => null]);
        }

        // add lead
        ['result' => $result, 'message' => $message, 'subject' => $subject] = $this->leadService->createLead($request->toArray());

        return redirect()->route('leads.index')->with($result, $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Lead $lead)
    {
        $this->authorize('read', Lead::class);

        $lead = $this->leadService->showLead($lead);

        return Inertia::render('Leads/ShowLead', [
            'lead' => new LeadResource($lead),
            'properties' => $this->propertyService->indexProperty(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => $this->sourceService->indexSource(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Lead $lead)
    {
        $this->authorize('update', Lead::class);
        $lead = $this->leadService->showLead($lead);

        return Inertia::render('Leads/EditLead', [
            'lead' => new LeadResource($lead),
            'properties' => $this->propertyService->indexProperty(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => $this->sourceService->indexSource(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LeadFormRequest $request, Lead $lead)
    {
        $this->authorize('update', Lead::class);

        if (!$request->has('owned_gadgets')) {
            $request->merge(['owned_gadgets' => null]);
        }

        ['result' => $result, 'message' => $message, 'subject' => $subject] = $this->leadService->updateLead($request->toArray(), $lead);

        return redirect()->route('leads.index')->with($result, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        //
    }

    /**
     * Display a listing of the done leads
     *
     * @return void
     */
    public function indexDoneLead(Request $request)
    {
        $this->authorize('read', AssignedConfirmer::class);

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

        $leads = LeadResource::collection($this->leadService->indexPaginateDoneLead($request->toArray()));

        return Inertia::render('Confirms/IndexPaginateConfirm', [
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'occupation' => $request->occupation,
            'venue_id' => $request->venue_id,
            'source_name' => $request->source_name,
            'module' => 'confirms',
            'items' => $leads,
            'employees' => $this->employeeService->indexEncoder(),
            'occupation_list' => Helper::occupationList(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => Helper::leadSource(null),
            'status_list' => Helper::leadStatus(),
            'confirmer_status_list' => Helper::leadConfirmerStatus(),
            'start_to' => $request->start_to,
            'end_to' => $request->end_to,
            'start_time_to' => $request->start_time_to,
            'end_time_to' => $request->end_time_to,
            'lead_status' => $request->lead_status,
            'employee_id' => $request->employee_id,
            'is_confirmer' => (Auth::user()->employee->userGroup->name == 'confirmers') ? true : false,
            'employee_venues' => $this->employeeVenueService->employeeVenueIds(Auth::user()->employee->id),
        ]);
    }

    /**
     * Add done status to a leads
     */
    public function done(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'status' => 'required|boolean',
            'employee_type' => 'required'
        ]);

        $lead = Lead::find($request->lead_id);

        ['result' => $result, 'message' => $message, 'subject' => $subject] = $this->leadService->done($lead, $request->status, $request->employee_type);

        return redirect()->route('assigned-employees')->with($result, $message);
    }

    /**
     * remove done status to a lead
     */
    public function cancelDone(Request $request)
    {
        $request->validate([
            'lead_ids' => 'required|array',
            'employee_type' => 'required'
        ]);

        try {
            foreach ($request->lead_ids as $lead_id) {
                $lead = Lead::find($lead_id);

                ['subject' => $subject] = $this->leadService->done($lead, false, $request->employee_type);
            }
        } catch (Exception $e) {
            return redirect()->route('done')->with('error', $e->getMessage());
        }

        return redirect()->route('done')->with('success', 'Successfully removed from done!');
    }

    /**
     * Add done status to a leads
     */
    public function doneConfirmer(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'status' => 'required|boolean',
            'employee_type' => 'required'
        ]);

        $lead = Lead::find($request->lead_id);

        ['result' => $result, 'message' => $message, 'subject' => $subject] = $this->leadService->done($lead, $request->status, $request->employee_type);

        return redirect()->route('confirms')->with($result, $message);
    }

    /**
     * remove done status to a lead
     */
    public function cancelDoneConfirmer(Request $request)
    {
        $request->validate([
            'lead_ids' => 'required|array',
            'employee_type' => 'required'
        ]);

        try {
            foreach ($request->lead_ids as $lead_id) {
                $lead = Lead::find($lead_id);

                ['subject' => $subject] = $this->leadService->done($lead, false, $request->employee_type);
            }
        } catch (Exception $e) {

            return redirect()->route('done')->with('error', $e->getMessage());
        }

        return redirect()->route('confirms')->with('success', 'Successfully removed from confirms!');
    }

    /**
     * Display the list of leads that has a confirmed status
     *
     * @param Request $request
     * @return void
     */
    public function indexConfirmed(Request $request)
    {
        $leads = LeadResource::collection($this->leadService->indexConfirmedLead());

        return Inertia::render('Confirmeds/IndexConfirmed', [
            'leads' => $leads,
            'employees' => $this->employeeService->indexConfirmer(),
            'occupation_list' => Helper::occupationList(),
            'per_page' => 5,
            'is_confirmer' => (Auth::user()->employee->userGroup->name == 'confirmers') ? true : false,
            'status_list' => Helper::leadStatus(),
            'confirmer_status_list' => Helper::leadConfirmerStatus(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => Helper::leadSource(null)
        ]);
    }

    public function indexPaginateConfirmed(Request $request)
    {
        // $this->authorize('read', AssignedConfirmer::class);

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

        // if sort by date
        if ($request->sort_by == 'assigned_confirmer.updated_at') {
            $request->merge(['sort_by' => 'assigned_confirmers.updated_at']);
        }

        $leads = LeadResource::collection($this->leadService->indexPaginateConfirmedLead($request->toArray()));

        return Inertia::render('Confirmeds/IndexPaginateConfirmed', [
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'occupation' => $request->occupation,
            'venue_id' => $request->venue_id,
            'source_name' => $request->source_name,
            'start_to' => $request->start_to,
            'end_to' => $request->end_to,
            'employee_id' => $request->employee_id,
            'confirmer_id' => $request->confirmer_id,
            'exhibitor_id' => $request->exhibitor_id,
            'items' => $leads,
            'occupation_list' => Helper::occupationList(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => Helper::leadSource(null),
            'status_list' => Helper::leadStatus(),
            'confirmer_status_list' => Helper::leadConfirmerStatus(),
            'employees' => EmployeeResource::collection($this->employeeService->indexEncoder()),
            'confirmers' => EmployeeResource::collection($this->employeeService->indexConfirmer()),
            'exhibitors' => EmployeeResource::collection($this->employeeService->indexExhibitor()),
            'module' => 'confirmeds',
        ]);
    }
}
