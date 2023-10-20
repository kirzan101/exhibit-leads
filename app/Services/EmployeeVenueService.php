<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ActivityLog;
use App\Models\EmployeeVenue;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeVenueService
{
    public $last_id;
    public $module_name = 'employee_venues';

    /**
     * create employee venue record
     *
     * @param array $request
     * @return array
     */
    public function createMultipleEmployeeVenue(array $request): array
    {
        try {
            DB::beginTransaction();

            $error = 0;
            foreach ($request['venue_ids'] as $venue) {
                $employeeVenue = EmployeeVenue::create([
                    'employee_id' => $request['employee_id'],
                    'venue_id' => $venue
                ]);

                if (!$employeeVenue) {
                    $error++;
                }

                $this->last_id = $employeeVenue->id;
            }

            if ($error > 0) {
                DB::rollBack();
                return ['result' => 'error', 'message' => 'Error on adding venues', 'subject' => $this->last_id];
            }
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        return ['result' => 'success', 'message' => 'Successfully created employee venues', 'subject' => $this->last_id];
    }

    /**
     * update employee venue record
     *
     * @param array $request
     * @return array
     */
    public function updateMultipleEmployeeVenue(array $request): array
    {
        //delete previous records
        $employee_venues = EmployeeVenue::where('employee_id', $request['employee_id'])->get();

        if ($employee_venues->count() > 0) {
            foreach ($employee_venues as $employee_venue) {
                $employee_venue->delete();
            }
        }

        // create new records
        $error = 0;
        try {
            DB::beginTransaction();

            $error = 0;
            foreach ($request['venue_ids'] as $venue) {
                $employeeVenue = EmployeeVenue::create([
                    'employee_id' => $request['employee_id'],
                    'venue_id' => $venue
                ]);

                if (!$employeeVenue) {
                    $error++;
                }

                $this->last_id = $employeeVenue->id;
            }

            if ($error > 0) {
                DB::rollBack();
                return ['result' => 'error', 'message' => 'Error on adding venues', 'subject' => $this->last_id];
            }
        } catch (Exception $e) {
            DB::rollBack();

            return ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        $return_values = ['result' => 'success', 'message' => 'Successfully updated employee venues', 'subject' => $this->last_id];

        //log activity
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
     * delete employee venue by employee id
     *
     * @param integer $id
     * @return array
     */
    public function deleteEmployeeVenueByEmployeeId(int $id): array
    {
        $this->last_id = $id;
        try {
            DB::beginTransaction();
            $employee_venues = EmployeeVenue::where('employee_id', $id)->get();

            if ($employee_venues->count() > 0) {
                foreach ($employee_venues as $employee_venue) {
                    $employee_venue->delete();
                }

                $result = ['result' => 'success', 'message' => 'Successfully deleted employee venues', 'subject' => $this->last_id];
            } else {
                $result = ['result' => 'success', 'message' => 'Employee has no venues', 'subject' => $this->last_id];
            }
        } catch (Exception $e) {
            DB::rollBack();
            return ['result' => 'success', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }

        DB::commit();

        return $result;
    }

    /**
     * get the array of venue ids of an employee
     *
     * @param integer $employee_id
     * @return array
     */
    public function employeeVenueIds(int $employee_id): array
    {
        $venue_ids = EmployeeVenue::select('venue_id')->where('employee_id', $employee_id)->get();

        return $venue_ids->toArray();
    }
}
