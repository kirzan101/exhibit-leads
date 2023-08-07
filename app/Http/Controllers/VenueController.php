<?php

namespace App\Http\Controllers;

use App\Http\Requests\VenueFormRequest;
use App\Http\Resources\VenueResource;
use App\Models\Venue;
use App\Services\VenueService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class VenueController extends Controller
{
    private VenueService $venueService;

    public function __construct(VenueService $venueService)
    {
        $this->venueService = $venueService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('read', Venue::class);

        $venues = $this->venueService->indexVenueService();

        return Inertia::render('Venues/IndexVenue', [
            'venues' => VenueResource::collection($venues),
            'per_page' => 5
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

        try {
            DB::beginTransaction();

            $this->venueService->createVenueService($request->toArray());

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('venues.index')->with('error', $e->getMessage());

        }

        DB::commit();
        return redirect()->route('venues.index')->with('success', 'Successfully created!');
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

        try {
            DB::beginTransaction();

            $this->venueService->updateVenueService($request->toArray(), $venue);

        } catch (Exception $e) {
            DB::rollBack();
            
            return redirect()->route('venues.index')->with('error', $e->getMessage());

        }
        DB::commit();

        return redirect()->route('venues.index')->with('success', 'Successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venue $venue)
    {
        $this->authorize('delete', Venue::class);

        try {
            DB::beginTransaction();

            $this->venueService->deleteVenueService($venue);

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('venues.index')->with('error', $e->getMessage());

        }
        DB::commit();
        
        return redirect()->route('venues.index')->with('success', 'Successfully deleted');
    }
}
