<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ActivityLog;
use App\Models\Venue;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VenueService
{
    public $last_id = null;
    public $module_name = 'venues';

    /**
     * index of venue service
     *
     * @return Collection
     */
    public function indexVenueService(): Collection
    {
        $venues = Venue::all();

        return $venues;
    }

    /**
     * create venue service
     *
     * @param array $request
     * @return array
     */
    public function createVenueService(array $request): array
    {
        try {
            DB::beginTransaction();

            $venue = Venue::create($request);
            $this->last_id = $venue->id;

            $return_values = ['result' => 'success', 'message' => 'Successfully saved!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }

        DB::commit();
        //log activity
        ActivityLog::create([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'create',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => json_encode($request),
            'causer_id' => Auth::user()->id,
            'subject_id' => $return_values['subject']
        ]);

        return $return_values;
    }

    /**
     * update venue service
     *
     * @param array $request
     * @param Venue $venue
     * @return array
     */
    public function updateVenueService(array $request, Venue $venue): array
    {
        try {
            DB::beginTransaction();

            $venue = tap($venue)->update($request);

            $this->last_id = $venue->id;

            $return_values = ['result' => 'success', 'message' => 'Successfully updated!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];

            return $return_values;
        }
        DB::commit();

        ActivityLog::create([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'update',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => json_encode($request),
            'causer_id' => Auth::user()->id,
            'subject_id' => $return_values['subject']
        ]);

        return $return_values;
    }

    /**
     * delete venue service
     *
     * @param Venue $venue
     * @return array
     */
    public function deleteVenueService(Venue $venue): array
    {
        try {
            DB::beginTransaction();
            $this->last_id = $venue->id;

            $venue->delete();

            $return_values = ['result' => 'success', 'message' => 'Successfully deleted!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();
            
            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }

        DB::commit();
        
        ActivityLog::create([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'delete',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => '{"venue_id":' . $this->last_id  . '}',
            'causer_id' => Auth::user()->id,
            'subject_id' => $return_values['subject']
        ]);

        return $return_values;
    }

    public function showVenueService(Venue $venue): Venue
    {
        return $venue;
    }
}
