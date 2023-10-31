<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\ActivityLog;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\EmployeeVenue;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    public $last_id;

    /**
     * index of employees service
     *
     * @return Collection
     */
    public function indexEmployee(): Collection
    {
        // $employee = Employee::all();
        $employee = Employee::where('user_group_id', '!=', '1')->orderBy('id', 'desc')->get(); // remove admin accounts

        return $employee;
    }

    /**
     * create employee service
     *
     * @param array $request
     * @return array
     */
    public function createEmployee(array $request): array
    {
        try {
            DB::beginTransaction();

            // generate username
            $username = Helper::username($request['first_name'], $request['last_name']);

            $user = User::create([
                'username' => $username,
                'email' => $request['email'],
                'password' => bcrypt($request['password']),
                'is_active' => $request['is_active']
            ]);

            $employee = Employee::create([
                'first_name' => $request['first_name'],
                'middle_name' => $request['middle_name'],
                'last_name' => $request['last_name'],
                'property_id' => $request['property_id'],
                'position' => $request['position'],
                'user_group_id' => $request['user_group_id'],
                'user_id' => $user->id,
                'exhibitor_id' => $request['exhibitor_id']
            ]);

            foreach ($request['venue_ids'] as $venue_id) {
                EmployeeVenue::create([
                    'employee_id' => $employee->id,
                    'venue_id' => $venue_id
                ]);
            }

            $return_values = ['result' => 'success', 'message' => 'Successfully created the employee!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();
            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        //log activity
        ActivityLog::create([
            'name' => 'employees',
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
     * update employee service
     *
     * @param array $request
     * @param Employee $employee
     * @return array
     */
    public function updateEmployee(array $request, Employee $employee): array
    {
        try {
            DB::beginTransaction();
            $user = User::find($request['user_id']);
            $user->update([
                'email' => $request['email'],
                'is_active' => $request['is_active']
            ]);

            $employee = tap($employee)->update([
                'first_name' => $request['first_name'],
                'middle_name' => $request['middle_name'],
                'last_name' => $request['last_name'],
                'property_id' => $request['property_id'],
                'position' => $request['position'],
                'user_group_id' => $request['user_group_id'],
                'exhibitor_id' => $request['exhibitor_id']
            ]);

            foreach ($request['venue_ids'] as $venue_id) {
                EmployeeVenue::create([
                    'employee_id' => $employee->id,
                    'venue_id' => $venue_id
                ]);
            }

            $this->last_id = $employee->id;

            $return_values = ['result' => 'success', 'message' => 'Successfully updated the employee!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();
            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        //log activity
        ActivityLog::create([
            'name' => 'employees',
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
     * delete employee service
     *
     * @param Employee $employee
     * @return array
     */
    public function deleteEmployee(Employee $employee): array
    {
        $request_array = [
            'employee_id' => $employee->id,
        ];


        try {
            $this->last_id = $employee->id;

            // delete employee venues
            EmployeeVenue::where('employee_id', $employee->id)->delete();

            // get the user id
            $user_id = $employee->user_id;

            // delete employee record
            $employee->delete();

            // find user
            $user = User::find($user_id);

            // delete user associated to employee
            $user->delete();

            $return_values = ['result' => 'success', 'message' => 'Successfully deleted the employee!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }

        //log activity
        ActivityLog::create([
            'name' => 'users',
            'description' => $return_values['message'],
            'event' => 'delete',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => json_encode($request_array),
            'causer_id' => Auth::user()->id,
            'subject_id' => $return_values['subject']
        ]);

        return $return_values;
    }

    /**
     * show employee service
     *
     * @param Employee $employee
     * @return Employee
     */
    public function showEmployee(Employee $employee): Employee
    {
        // removes admin account
        if ($employee->userGroup->id == 1) {
            return abort(404);
        }

        return $employee;
    }

    /**
     * reset user pasword | Default Password: P@ssw0rd
     *
     * @param integer $id
     * @return array
     */
    public function resetPassword(int $id): array
    {
        try {
            DB::beginTransaction();
            $employee = Employee::find($id);

            $this->last_id = $employee->id;

            $user = User::find($employee->user_id);

            tap($user)->update([
                'password' => bcrypt('P@ssw0rd'),
                'is_password_changed' => false
            ]);

            $return_values = ['result' => 'success', 'message' => 'Successfully reset the password! The new password is:  P@ssw0rd', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();
            $return_values = ['result' => 'error', 'message' =>  $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        $request_array = [
            'password' => 'P@ssw0rd',
            'is_password_changed' => true,
            'user_id' => $user->id
        ];

        //log activity
        ActivityLog::create([
            'name' => 'users',
            'description' => $return_values['message'],
            'event' => 'update',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => json_encode($request_array),
            'causer_id' => Auth::user()->id,
            'subject_id' => $return_values['subject']
        ]);

        return $return_values;
    }

    /**
     * update employee service
     *
     * @param array $request
     * @param Employee $employee
     * @return array
     */
    public function updatePassword(array $request, int $id): array
    {
        $user = User::find($id);

        $this->last_id = $user->id;

        try {
            DB::beginTransaction();
            $user = tap($user)->update([
                'password' => bcrypt($request['password']),
                'is_password_changed' => true
            ]);

            $return_values = ['result' => 'success', 'message' => 'Successfully updated password!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();
            $return_values = ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        $request_array = [
            'password' => bcrypt($request['password']),
            'is_password_changed' => true,
            'user_id' => $id
        ];

        //log activity
        ActivityLog::create([
            'name' => 'users',
            'description' => $return_values['message'],
            'event' => 'update',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => json_encode($request_array),
            'causer_id' => Auth::user()->id,
            'subject_id' => $return_values['subject']
        ]);

        return $return_values;
    }

    /**
     * Index of confirmers
     *
     * @return Collection
     */
    public function indexConfirmer(): Collection
    {
        $confirmers = Employee::select('employees.*')
            ->join('user_groups', 'user_groups.id', '=', 'employees.user_group_id')
            ->where('user_groups.name', 'confirmers')
            ->get();

        return $confirmers;
    }

    /**
     * Index of encoders
     *
     * @return Collection
     */
    public function indexEncoder(): Collection
    {
        $encoders = Employee::select('employees.*')
            ->join('user_groups', 'user_groups.id', '=', 'employees.user_group_id')
            ->where('user_groups.name', 'employees')
            ->get();

        //if loggedin is an exhibit, roi & survey team lead
        // if(Auth::user()->user)

        return $encoders;
    }

    /**
     * Index of Exhibitors
     *
     * @return Collection
     */
    public function indexTeamLead(): Collection
    {
        $exhibitors = Employee::select('employees.*')
            ->join('user_groups', 'user_groups.id', '=', 'employees.user_group_id')
            ->whereIn('user_groups.name', ['exhibit', 'rois', 'surveys'])
            ->get();

        return $exhibitors;
    }

    /**
     * Index of Exhibitors
     *
     * @return Collection
     */
    public function indexExhibitor(): Collection
    {
        $exhibitors = Employee::select('employees.*')
            ->join('user_groups', 'user_groups.id', '=', 'employees.user_group_id')
            ->where('user_groups.name', 'exhibit')
            ->get();

        return $exhibitors;
    }

    /**
     * Get the employee assigned to exhibitor
     *
     * @param integer|null $exhibitor_id
     * @return void
     */
    public function indexTeamLeadEmployees(?int $exhibitor_id)
    {
        $employees = Employee::select('employees.*')
            ->join('user_groups', 'user_groups.id', '=', 'employees.user_group_id')
            ->where('user_groups.name', 'employees');

        if ($exhibitor_id) {
            $isAdmin = (Employee::find($exhibitor_id)->user_group_id == 1) ? true : false;

            if (!$isAdmin) {
                $employees = $employees->where('employees.exhibitor_id', $exhibitor_id);
            }
        }

        return $employees->orderBy('employees.id', 'desc')->get();
    }

    /**
     * index of paginate employees service
     *
     * @return Paginator
     */
    public function indexEmployeePaginate(array $request): Paginator
    {
        $employees = Employee::select('employees.*')
            ->join('users', 'users.id', '=', 'employees.user_id')
            ->where('employees.user_group_id', '!=', '1');

        //set default values
        $per_page = (array_key_exists('per_page', $request) && $request['per_page'] != null) ? (int)$request['per_page'] : 5;
        $sort_by = (array_key_exists('sort_by', $request) && $request['sort_by'] != null) ? $request['sort_by'] : 'id';
        $sort = 'desc';
        if (array_key_exists('is_sort_desc', $request) && $request['is_sort_desc'] != null) {
            $sort = ($request['is_sort_desc'] == 'true') ? 'desc' : 'asc';
        }

        // search filter
        if (array_key_exists('search', $request) && !empty($request['search'])) {
            $employees->where(function ($query) use ($request) {
                $query->where('employees.first_name', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('employees.last_name', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('employees.position', 'LIKE', '%' . $request['search'] . '%');
            });
        }

        // position filter
        if (array_key_exists('position', $request) && !empty($request['position'])) {
            $employees->where(function ($query) use ($request) {
                $query->where('employees.position', 'LIKE', '%' . $request['position'] . '%');
            });
        }

        // user group filter
        if (array_key_exists('user_group_id', $request) && !empty($request['user_group_id'])) {
            $employees->where(function ($query) use ($request) {
                $query->where('employees.user_group_id', 'LIKE', '%' . $request['user_group_id'] . '%');
            });
        }

        return $employees->orderBy($sort_by, $sort)->paginate($per_page);
    }
}
