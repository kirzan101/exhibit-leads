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
        if(!$request->per_page) {
            $request->merge([
                'per_page' => 5
            ]);
        }

        if(!$request->sortDesc) {
            $request->merge([
                'sortDesc' => false
            ]);
        }

        $leads = $this->leadService->indexPaginateLead($request->per_page, $request->toArray());

        // dd($request->sortDesc);

        return Inertia::render('Paginates/LeadPaginate', [
            'items' => LeadResource::collection($leads->items()),
            'per_page' => $leads->perPage(),
            'last_page' => $leads->lastPage(),
            'current_page' => $leads->currentPage(),
            'total' => $leads->total(),
        ]);
    }

    // public function leadPaginate(Request $request)
    // {
    //     $leads = $this->leadService->indexPaginateLead($request->per_page);

    //     return response()->json($leads);
    // }
}
