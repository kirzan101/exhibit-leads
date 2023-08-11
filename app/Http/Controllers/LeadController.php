<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\LeadFormRequest;
use App\Http\Resources\LeadResource;
use App\Models\AssignedEmployee;
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

class LeadController extends Controller
{
    private LeadService $leadService;
    private EmployeeService $employeeService;
    private PropertyService $propertyService;
    private VenueService $venueService;
    private SourceService $sourceService;
    private AssignedEmployeeService $assignedEmployeeService;

    public function __construct(
        LeadService $leadService,
        EmployeeService $employeeService,
        PropertyService $propertyService,
        VenueService $venueService,
        SourceService $sourceService,
        AssignedEmployeeService $assignedEmployeeService
    ) {
        $this->leadService = $leadService;
        $this->employeeService = $employeeService;
        $this->propertyService = $propertyService;
        $this->venueService = $venueService;
        $this->sourceService = $sourceService;
        $this->assignedEmployeeService = $assignedEmployeeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('read', Lead::class);

        $leads = LeadResource::collection($this->leadService->indexLead());
        return Inertia::render('Leads/IndexLead', [
            'leads' => $leads,
            'employees' => $this->employeeService->indexEncoder(),
            'occupation_list' => Helper::occupationList(),
            'per_page' => 5,
            'venues' => $this->venueService->indexVenueService(),
            'sources' => $this->sourceService->indexSource(),
            'exhibitors' => $this->employeeService->indexExhibitor()
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
        ['result' => $result, 'message' => $message] = $this->leadService->createLead($request->toArray());

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
        // dd($lead->getUploadedFile(), Storage::disk('public'));
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

        ['result' => $result, 'message' => $message] = $this->leadService->updateLead($request->toArray(), $lead);

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
    public function indexDoneLead()
    {
        $leads = LeadResource::collection($this->leadService->indexDoneLead());

        return Inertia::render('Confirms/IndexConfirm', [
            'leads' => $leads,
            'employees' => $this->employeeService->indexConfirmer(),
            'occupation_list' => Helper::occupationList(),
            'per_page' => 5,
            'is_confirmer' => (Auth::user()->employee->userGroup->name == 'confirmers') ? true : false,
            'status_list' => Helper::leadStatus(),
            'confirmer_status_list' => Helper::leadConfirmerStatus(),
            'venues' => $this->venueService->indexVenueService(),
            'sources' => Helper::leadSource()
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

        ['result' => $result, 'message' => $message] = $this->leadService->done($lead, $request->status, $request->employee_type);

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

                $lead = $this->leadService->done($lead, false, $request->employee_type);
            }
        } catch (Exception $e) {
            return redirect()->route('done')->with('error', $e->getMessage());
        }

        return redirect()->route('done')->with('success', 'Successfully removed from done!');
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
            'sources' => Helper::leadSource()
        ]);
    }

    /**
     * set the lead as showed
     *
     * @param Request $request
     * @return void
     */
    public function showedLead(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'status' => 'required'
        ]);

        try {

            $this->leadService->showed($request->toArray());
        } catch (Exception $e) {
            return redirect()->route('confirmed')->with('error', 'Unsuccessful to mark as showed!');
        }

        return redirect()->route('confirmed')->with('success', 'Successfully mark as showed!');
    }

    /**
     * Display a paginate listing of the resource.
     */
    public function indexPaginate(Request $request)
    {
        $per_page = $request->per_page;
        $page = $request->page;

        if (!$per_page) {
            $per_page = 5;
        }

        // if(!$page) {
        //     $page = 1;
        // }

        $leads = LeadResource::collection($this->leadService->indexPaginateLead($per_page));

        // dd($leads);
        return Inertia::render('Leads/PaginateLead', [
            'leads' => $leads,
            'employees' => $this->employeeService->indexEmployee(),
            'occupation_list' => Helper::occupationList(),
            'per_page' => 5
        ]);
    }
}
