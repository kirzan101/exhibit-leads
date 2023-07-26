<?php

namespace App\Services;

use App\Models\Venue;
use Illuminate\Database\Eloquent\Collection;

class VenueService
{
    /**
     * index of venue service
     *
     * @return Collection
     */
    public function indexVenueService() : Collection
    {
        $venues = Venue::all();

        return $venues;
    }

    /**
     * create venue service
     *
     * @param array $request
     * @return Venue
     */
    public function createVenueService(array $request) : Venue
    {
        $venue = Venue::create($request);

        return $venue;
    }

    /**
     * update venue service
     *
     * @param array $request
     * @param Venue $venue
     * @return Venue
     */
    public function updateVenueService(array $request, Venue $venue) : Venue
    {
        $venue = tap($venue)->update($request);

        return $venue;
    }

    /**
     * delete venue service
     *
     * @param Venue $venue
     * @return boolean
     */
    public function deleteVenueService(Venue $venue) : bool
    {
        $result = $venue->delete();

        return $result;
    }

    public function showVenueService(Venue $venue) : Venue
    {
        return $venue;
    }
}