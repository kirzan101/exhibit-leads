<?php

namespace App\Http\Controllers\API;

use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Resources\LeadResource;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeadController extends Controller
{
    private LeadService $leadService;

    public function __construct(
        LeadService $leadService,
    )
    {
        $this->leadService = $leadService;
    }

    /**
     * Paginated data of leads
     *
     * @param Request $request
     * $request->sort_by
     * $request->per_page
     * $request->is_sort_desc
     * $request->search
     * $request->venue_id
     * $request->source_name
     * $request->occupation
     * @return void
     */
    public function index(Request $request)
    {
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

        return $leads;
    }
}
