<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\LeadFormRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use App\Services\EmployeeService;
use App\Services\LeadService;
use App\Services\PropertyService;
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

    public function __construct(LeadService $leadService, EmployeeService $employeeService, PropertyService $propertyService, VenueService $venueService)
    {
        $this->leadService = $leadService;
        $this->employeeService = $employeeService;
        $this->propertyService = $propertyService;
        $this->venueService = $venueService;
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
            'per_page' => 5
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
            'lead_sources' => Helper::leadSource(),
            'venues' => $this->venueService->indexVenueService()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LeadFormRequest $request)
    {
        $this->authorize('create', Lead::class);

        try {
            DB::beginTransaction();

            // add lead
            $this->leadService->createLead($request->validated());
        } catch (Exception $ex) {

            DB::rollBack();

            return redirect()->route('leads.index')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('leads.index')->with('success', 'Successfully saved!');
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
            'lead_sources' => Helper::leadSource(),
            'venues' => $this->venueService->indexVenueService()
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
            'lead_sources' => Helper::leadSource(),
            'venues' => $this->venueService->indexVenueService()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LeadFormRequest $request, Lead $lead)
    {
        $this->authorize('update', Lead::class);

        try {
            DB::beginTransaction();

            $this->leadService->updateLead($request->validated(), $lead);
        } catch (Exception $ex) {

            DB::rollBack();

            return redirect()->route('leads.index')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('leads.index')->with('success', 'Successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lead $lead)
    {
        //
    }

    public function remarks(Request $request)
    {
        $this->authorize('update', Lead::class);

        $request = $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'remarks' => 'required|min:2',
            'lead_status' => 'required|min:1',
        ]);

        $result = $this->leadService->modifyRemarks($request);

        if (!$result) {
            return redirect()->route('assigned-employees.index')->with('error', 'Something went wrong on saving!');
        }

        return redirect()->route('assigned-employees.index')->with('success', 'Successfully saved!');
    }

    /**
     * Display a listing of the invite leads
     *
     * @param Request $request
     * @return void
     */
    public function indexInvite(Request $request)
    {
        $invited = true;

        $leads = LeadResource::collection($this->leadService->indexInvitedLead($invited));

        return Inertia::render('Invites/IndexInvite', [
            'leads' => $leads,
            'employees' => $this->employeeService->indexConfirmer(),
            'occupation_list' => Helper::occupationList(),
            'per_page' => 5,
            'is_confirmer' => (Auth::user()->employee->userGroup->name == 'confirmers') ? true : false,
            'status_list' => Helper::leadStatus(),
        ]);
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

    /**
     * Add invite status to a leads
     */
    public function invite(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'status' => 'required|boolean'
        ]);

        try {
            $lead = Lead::find($request->lead_id);

            $lead = $this->leadService->inviteLead($lead, $request->status);
        } catch (Exception $e) {
            return redirect()->route('assigned-employees.index')->with('error', $e->getMessage());
        }

        return redirect()->route('assigned-employees.index')->with('success', 'Successfully invited!');
    }

    /**
     * remove invite status to a lead
     */
    public function inviteCancel(Request $request)
    {
        $request->validate([
            'lead_ids' => 'required|array'
        ]);

        try {
            foreach ($request->lead_ids as $lead_id) {
                $lead = Lead::find($lead_id);

                $lead = $this->leadService->inviteLead($lead, false);
            }
        } catch (Exception $e) {
            return redirect()->route('invites')->with('error', $e->getMessage());
        }

        return redirect()->route('invites')->with('success', 'Successfully removed from invitees!');
    }

    /**
     * Confirm lead
     */
    public function confirm(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'lead_status_confirmer' => 'required',
            'confirmer_remarks' => 'required'
        ]);

        try {
            
            $lead = Lead::find($request->lead_id);

            $lead = $this->leadService->confirmLead($lead, $request->toArray());
        } catch (Exception $e) {
            return redirect()->route('assigned-confirmers.index')->with('error', $e->getMessage());
        }

        return redirect()->route('assigned-confirmers.index')->with('success', 'Successfully confirmed!');
    }

    /**
     * remove confirm status in leads
     *
     * @param Request $request
     * @return void
     */
    public function removeConfirmed(Request $request)
    {
        $request->validate([
            'lead_ids' => 'required|array'
        ]);

        try {
            foreach ($request->lead_ids as $lead_id) {
                $lead = Lead::find($lead_id);

                $lead = $this->leadService->removeConfirmLead($lead, $request->toArray());
            }
        } catch (Exception $e) {
            return redirect()->route('assigned-confirmers.index')->with('error', $e->getMessage());
        }

        return redirect()->route('assigned-confirmers.index')->with('success', 'Successfully confirmed!');
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
            'per_page' => 5
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
}
