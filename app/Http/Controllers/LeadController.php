<?php

namespace App\Http\Controllers;

use App\Http\Requests\LeadFormRequest;
use App\Http\Resources\LeadResource;
use App\Models\Lead;
use App\Services\EmployeeService;
use App\Services\LeadService;
use App\Services\PropertyService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class LeadController extends Controller
{
    private LeadService $leadService;
    private EmployeeService $employeeService;
    private PropertyService $propertyService;

    public function __construct(LeadService $leadService, EmployeeService $employeeService, PropertyService $propertyService)
    {
        $this->leadService = $leadService;
        $this->employeeService = $employeeService;
        $this->propertyService = $propertyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leads = LeadResource::collection($this->leadService->indexLead());
        return Inertia::render('Leads/IndexLead', [
            'leads' => $leads,
            'employees' => $this->employeeService->indexEmployee(),
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
            'properties' => $this->propertyService->indexProperty()
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
            'properties' => $this->propertyService->indexProperty()
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
            'properties' => $this->propertyService->indexProperty()
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
        ]);

        $result = $this->leadService->modifyRemarks($request);

        if (!$result) {
            return redirect()->route('assigned-employees.index')->with('error', 'Something went wrong on saving!');
        }

        return redirect()->route('assigned-employees.index')->with('success', 'Successfully saved!');
    }

    /**
     * Display a listing of the resource
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
            'employees' => $this->employeeService->indexEmployee(),
            'per_page' => 5
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
            'per_page' => 5
        ]);
    }

    /**
     * Update the specified resource in storage.
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
     * Update the specified resource in storage.
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
}
