<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\EmployeeVenue;
use App\Models\User;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeService
{
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
     * @return Employee
     */
    public function createEmployee(array $request): Employee
    {
        // dd($request['password']);
        // generate username
        $username = Helper::username($request['first_name'], $request['last_name']);

        $user = User::create([
            'username' => $username,
            'email' => $request['email'],
            'password' => bcrypt($request['password'])
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

        return $employee;
    }

    /**
     * update employee service
     *
     * @param array $request
     * @param Employee $employee
     * @return Employee
     */
    public function updateEmployee(array $request, Employee $employee): Employee
    {
        $user = User::find($request['user_id']);
        $user->update([
            'email' => $request['email'],
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

        return $employee;
    }

    /**
     * delete employee service
     *
     * @param Employee $employee
     * @return boolean
     */
    public function deleteEmployee(Employee $employee): bool
    {
        // delete employee venues
        EmployeeVenue::where('employee_id', $employee->id)->delete();

        // get the user id
        $user_id = $employee->user_id;

        // delete employee record
        $result = $employee->delete();

        // find user
        $user = User::find($user_id);

        // delete user associated to employee
        $user->delete();

        return $result;
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
     * @return boolean
     */
    public function resetPassword(int $id): bool
    {
        $employee = Employee::find($id);

        $user = User::find($employee->user_id);

        $result = $user->update([
            'password' => bcrypt('P@ssw0rd')
        ]);

        return $result;
    }

    /**
     * update employee service
     *
     * @param array $request
     * @param Employee $employee
     * @return Employee
     */
    public function updatePassword(array $request, int $id): bool
    {
        $user = User::find($id);

        $result = $user->update([
            'password' => bcrypt($request['password']),
        ]);

        return $result;
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

        return $employees->orderBy($sort_by, $sort)->paginate($per_page);
    }
}
