<?php

namespace App\Services;

use App\Models\Contract;
use App\Models\Employee;
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
        $employee = Employee::all();

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
        $employee = Employee::create($request);

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
        $employee = tap($employee)->update($request);

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
        $result = $employee->delete();

        return $result;
    }

    public function showEmployee(Employee $employee) : Employee
    {
        return $employee;
    }
}