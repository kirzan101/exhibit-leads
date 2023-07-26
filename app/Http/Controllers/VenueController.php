<?php

namespace App\Http\Controllers;

use App\Http\Requests\VenueFormRequest;
use App\Http\Resources\VenueResource;
use App\Models\Venue;
use App\Services\VenueService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        $venues = $this->venueService->indexVenueService();

        return VenueResource::collection($venues);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(VenueFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $this->venueService->createVenueService($request->toArray());

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('venues')->with('error', $e->getMessage());

        }

        DB::commit();
        return redirect()->route('venues')->with('success', 'Successfully created!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Venue $venue)
    {
        $venue = $this->venueService->showVenueService($venue);

        return new VenueResource($venue);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Venue $venue)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(VenueFormRequest $request, Venue $venue)
    {
        try {
            DB::beginTransaction();

            $this->venueService->updateVenueService($request->toArray(), $venue);

        } catch (Exception $e) {
            DB::rollBack();
            
            return redirect()->route('venues')->with('error', $e->getMessage());

        }
        DB::commit();

        return redirect()->route('venues')->with('success', 'Successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venue $venue)
    {
        try {
            DB::beginTransaction();

            $this->venueService->deleteVenueService($venue);

        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('venues')->with('error', $e->getMessage());

        }
        DB::commit();
        
        return redirect()->route('venues')->with('success', 'Successfully deleted');
    }
}
