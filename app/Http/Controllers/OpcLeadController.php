<?php

namespace App\Http\Controllers;

use App\Exports\OpcLeadsExport;
use App\Helpers\Helper;
use App\Http\Resources\OpcLeadResource;
use App\Models\OpcLead;
use App\Services\OpcLeadService;
use Carbon\Carbon;
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
        $this->authorize('read', OpcLead::class);

        //set default value for lead name
        $sort_by = $request->sort_by;
        if ($request->sort_by == 'full_name') {
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

        $leads = OpcLeadResource::collection($this->opcLeadService->indexPaginateOpcLead($request->toArray()));

        return Inertia::render('OpcLeads/IndexOpcLead', [
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'start_to' => $request->start_to,
            'end_to' => $request->end_to,
            'source_name' => $request->source_name,
            'sources' => Helper::opcLeadSource(),
            'items' => $leads
        ]);
    }

    /**
     * Download opc leads
     *
     * @param Request $request
     * @return void
     */
    public function downloadOpcLeads(Request $request)
    {
        $leads = $this->opcLeadService->opcLeadReportService($request->toArray());
        $leads_resource = OpcLeadResource::collection($leads);

        $file_name = sprintf('%s-%s.xlsx', Carbon::now()->format('Y-m-d-H-i'), 'opc-leads');
        $exported_leads = new OpcLeadsExport($leads_resource->resource->toArray());

        return ($exported_leads)->download($file_name);
    }
}
