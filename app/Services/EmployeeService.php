<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Contract;
use App\Models\Employee;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class EmployeeService
{
    /**
     * index of employees service
     *
     * @return Collection
     */
    public function indexEmployee() : Collection
    {
        // $employee = Employee::all();
        $employee = Employee::where('user_group_id', '!=', '1')->get(); // remove admin accounts

        return $employee;
    }

    /**
     * create employee service
     *
     * @param array $request
     * @return Employee
     */
    public function createEmployee(array $request) : Employee
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
            'property' => $request['property'],
            'position' => $request['position'],
            'user_group_id' => $request['user_group_id'],
            'user_id' => $user->id
        ]);

        return $employee;
    }

    /**
     * update employee service
     *
     * @param array $request
     * @param Employee $employee
     * @return Employee
     */
    public function updateEmployee(array $request, Employee $employee) : Employee
    {
        $user = User::find($request['user_id']);
        $user->update([
            'email' => $request['email'],
        ]);

        $employee = tap($employee)->update([
            'first_name' => $request['first_name'],
            'middle_name' => $request['middle_name'],
            'last_name' => $request['last_name'],
            'property' => $request['property'],
            'position' => $request['position'],
            'user_group_id' => $request['user_group_id'],
        ]);

        return $employee;
    }

    /**
     * delete employee service
     *
     * @param Employee $employee
     * @return boolean
     */
    public function deleteEmployee(Employee $employee) : bool
    {
        $user_id = $employee->user_id;
        $result = $employee->delete();
        
        $user = User::find($user_id);
        $user->delete();

        return $result;
    }

    /**
     * show employee service
     *
     * @param Employee $employee
     * @return Employee
     */
    public function showEmployee(Employee $employee) : Employee
    {
        return $employee;
    }

    /**
     * reset user pasword | Default Password: P@ssw0rd
     *
     * @param integer $id
     * @return boolean
     */
    public function resetPassword(int $id) : bool
    {
        $employee = Employee::find($id);

        $user = User::find($employee->user_id);

        $result = $user->update([
            'password' => bcrypt('P@ssw0rd')
        ]);

        return $result;
    }
}