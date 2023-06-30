<?php

namespace App\Services;

use App\Models\AssignedEmployee;
use App\Models\Contract;
use App\Models\Member;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignedEmployeeService
{
    /**
     * index of assigned employee service
     *
     * @return Collection
     */
    public function indexAssignedEmployee(): Collection
    {
        $assigned_employees = Member::where('is_assigned', true)->get();

        return $assigned_employees;
    }

    /**
     * create assigned employee service
     *
     * @param array $request
     * @return AssignedEmployee
     */
    public function createAssignedEmployee(array $request): bool
    {
        try {
            foreach ($request['member_ids'] as $member) {
                AssignedEmployee::create([
                    'member_id' => $member,
                    'employee_id' => $request['employee_id'],
                    'created_by' => 1,
                ]);

                $member = Member::find($member);
                $member->update([
                    'is_assigned' => true,
                    'employee_id' => $request['employee_id']
                ]);
            }
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }

    /**
     * update assigned employee service
     *
     * @param array $request
     * @param AssignedEmployee $assignedEmployee
     * @return AssignedEmployee
     */
    public function updateAssignedEmployee(array $request, AssignedEmployee $assignedEmployee): bool
    {
        try {
            foreach ($request['member_ids'] as $member) {
                AssignedEmployee::create([
                    'member_id' => $member,
                    'employee_id' => $request['employee_id'],
                    'created_by' => 1,
                ]);

                $member = Member::find($member);
                $member->update([
                    'is_assigned' => true,
                    'employee_id' => $request['employee_id']
                ]);
            }
        } catch (Exception $ex) {
            return false;
        }

        // $assigned_employee = tap($assignedEmployee)->update($request);

        return true;
    }

    /**
     * delete assigned employee service
     *
     * @param AssignedEmployee $assignedEmployee
     * @return boolean
     */
    public function deleteAssignedEmployee(AssignedEmployee $assignedEmployee): bool
    {
        return $assignedEmployee->delete;
    }

    public function removedAssgined(array $request) : bool
    {
        try {
            foreach ($request['member_ids'] as $member) {
                $member = Member::find($member);
                $member->update([
                    'is_assigned' => false,
                    'employee_id' => null,
                    'remarks' => null
                ]);
            }
        } catch (Exception $ex) {
            return false;
        }

        return true;
    }
}
