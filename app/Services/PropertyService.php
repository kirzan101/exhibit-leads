<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ActivityLog;
use App\Models\Property;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PropertyService
{
    public $last_id = null;
    public $module_name = 'properties';

    /**
     * index of property service
     *
     * @return void
     */
    public function indexProperty()
    {
        $properties = Property::all();

        return $properties;
    }

    /**
     * create property service
     *
     * @param array $request
     * @return array
     */
    public function createProperty(array $request): array
    {
        try {
            DB::beginTransaction();

            $property = Property::create($request);
            $this->last_id = $property->id;

            $return_values = ['result' => 'success', 'message' => 'Successfully created!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

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
     * update property service
     *
     * @param array $request
     * @param Property $property
     * @return array
     */
    public function updateProperty(array $request, Property $property): array
    {
        try {
            DB::beginTransaction();

            $property = tap($property)->update($request);
            $this->last_id = $property->id;

            $return_values = ['result' => 'success', 'message' => 'Successfully updated!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
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
     * delete property service
     *
     * @param Property $property
     * @return array
     */
    public function deleteProperty(Property $property): array
    {
        $this->last_id = $property->id;

        try {
            DB::beginTransaction();

            $property->delete();

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
            'properties' => '{"property_id":' . $this->last_id . '}',
            'causer_id' => Auth::user()->id,
            'subject_id' => $return_values['subject']
        ]);

        return $return_values;
    }
}
