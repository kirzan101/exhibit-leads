<?php 

namespace App\Services;

use App\Models\Property;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class PropertyService
{
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
     * @return Property
     */
    public function createProperty(array $request) : Property
    {
        $property = Property::create($request);

        return $property;
    }

    /**
     * update property service
     *
     * @param array $request
     * @param Property $property
     * @return Property
     */
    public function updateProperty(array $request, Property $property) : Property
    {
        $property = tap($property)->update($request);

        return $property;
    }

    /**
     * delete property service
     *
     * @param Property $property
     * @return boolean
     */
    public function deleteProperty(Property $property) : bool
    {
        $result = $property->delete();

        return $result;
    }
}