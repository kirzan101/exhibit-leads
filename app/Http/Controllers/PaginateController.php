<?php

namespace App\Http\Controllers;

use App\Http\Resources\LeadResource;
use App\Services\LeadService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaginateController extends Controller
{
    private LeadService $leadService;

    public function __construct(LeadService $leadService)
    {
        $this->leadService = $leadService;
    }

    public function leadPaginateIndex(Request $request)
    {
        //set default value for lead name
        $sort_by = $request->sort_by;
        if($request->sort_by == 'lead_full_name') {
            $request->merge(['sort_by' => 'last_name']);
        }

        $leads = $this->leadService->indexPaginateLead($request->toArray());

        return Inertia::render('Paginates/LeadPaginate', [
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'items' => LeadResource::collection($leads)
        ]);
    }
}
