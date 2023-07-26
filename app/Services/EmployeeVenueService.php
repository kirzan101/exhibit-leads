<?php

namespace App\Services;

use App\Models\EmployeeVenue;

class EmployeeVenueService
{
    /**
     * create employee venue record
     *
     * @param array $request
     * @return EmployeeVenue
     */
    public function createMultipleEmployeeVenue(array $request) : bool
    {
        $error = 0;
        foreach($request['venue_ids'] as $venue) {
            $employeeVenue = EmployeeVenue::create([
                'employee_id' => $request['employee_id'],
                'venue_id' => $venue
            ]);

            if(!$employeeVenue) {
                $error++;
            }
        }

        if($error > 0) {
            return false;
        }

        return true;
    }

    /**
     * update employee venue record
     *
     * @param array $request
     * @return EmployeeVenue
     */
    public function updateMultipleEmployeeVenue(array $request) : bool
    {
        //delete previous records
        $employee_venue = EmployeeVenue::where($request['employee_id']);
        $employee_venue->delete();

        // create new records
        $error = 0;
        foreach($request['venue_ids'] as $venue) {
            $employeeVenue = EmployeeVenue::create([
                'employee_id' => $request['employee_id'],
                'venue_id' => $venue
            ]);

            if(!$employeeVenue) {
                $error++;
            }
        }

        if($error > 0) {
            return false;
        }

        return true;
    }
    
    /**
     * delete employee venue by employee id
     *
     * @param integer $id
     * @return boolean
     */
    public function deleteEmployeeVenueByEmployeeId(int $id) : bool
    {
        $employee_venue = EmployeeVenue::where('employee_id', $id)->first();

        $result = $employee_venue->delete();

        return $result;
    }
}