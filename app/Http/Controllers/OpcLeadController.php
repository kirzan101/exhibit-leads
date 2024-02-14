<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpcLeadResource;
use App\Services\OpcLeadService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OpcLeadController extends Controller
{
    public function __construct(private OpcLeadService $opcLeadService)
    {
        $this->opcLeadService = $opcLeadService;
    }

    /**
     * List of paginated OPC Leads
     *
     * @param Request $request
     * @return void
     */
    public function index(Request $request)
    {
        //set default value for lead name
        $sort_by = $request->sort_by;
        if ($request->sort_by == 'full_name') {
            $request->merge(['sort_by' => 'last_name']);
        }

        // set default to desc
        if ($request->is_sort_desc == null) {
            $request->merge(['is_sort_desc' => true]);
        }

        $leads = OpcLeadResource::collection($this->opcLeadService->indexPaginateOpcLead($request->toArray()));

        return Inertia::render('OpcLeads/IndexOpcLead', [
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'items' => $leads
        ]);
    }
}
