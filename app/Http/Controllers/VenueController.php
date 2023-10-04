<?php

namespace App\Http\Controllers;

use App\Http\Requests\VenueFormRequest;
use App\Http\Resources\VenueResource;
use App\Models\Venue;
use App\Services\GenericPaginateService;
use App\Services\VenueService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VenueController extends Controller
{
    private VenueService $venueService;
    private GenericPaginateService $genericPaginateService;
    public $table = 'venues';

    public function __construct(VenueService $venueService, GenericPaginateService $genericPaginateService)
    {
        $this->venueService = $venueService;
        $this->genericPaginateService = $genericPaginateService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('read', Venue::class);
        //set default value for lead name
        $sort_by = $request->sort_by;

        // set default to desc
        if ($request->is_sort_desc == null) {
            $request->merge(['is_sort_desc' => true]);
        }

        $venues = VenueResource::collection($this->genericPaginateService->index($this->table, $request->toArray()));

        return Inertia::render('Venues/IndexPaginateVenue',[
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'module' => 'venues',
            'items' => $venues,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Venue::class);

        return Inertia::render('Venues/CreateVenue');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VenueFormRequest $request)
    {
        $this->authorize('create', Venue::class);

        ['result' => $result, 'message' => $message] = $this->venueService->createVenueService($request->toArray());

        return redirect()->route('venues.index')->with($result, $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Venue $venue)
    {
        $this->authorize('read', Venue::class);

        $venue = $this->venueService->showVenueService($venue);

        return Inertia::render('Venues/ShowVenue', [
            'venue' => new VenueResource($venue)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venue $venue)
    {
        $this->authorize('update', Venue::class);

        return Inertia::render('Venues/EditVenue', [
            'venue' => new VenueResource($venue)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VenueFormRequest $request, Venue $venue)
    {
        $this->authorize('update', Venue::class);

        ['result' => $result, 'message' => $message] = $this->venueService->updateVenueService($request->toArray(), $venue);

        return redirect()->route('venues.index')->with($result, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venue $venue)
    {
        $this->authorize('delete', Venue::class);

        ['result' => $result, 'message' => $message] = $this->venueService->deleteVenueService($venue);

        return redirect()->route('venues.index')->with($result, $message);
    }
}
