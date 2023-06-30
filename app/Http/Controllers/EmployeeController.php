<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeFormRequest;
use App\Models\Employee;
use App\Services\EmployeeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    private EmployeeService $employeeService;

    public function __construct(EmployeeService $employeeService)
    {
        $this->employeeService = $employeeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('Employees/IndexEmployee', [
            'employees' => $this->employeeService->indexEmployee(),
            'per_page' => 5
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Employees/CreateEmployee', []);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeFormRequest $request)
    {
        try {
            $request->validate([
                'password' => 'required|min:2'
            ]);

            DB::beginTransaction();

            $this->employeeService->createEmployee($request->validated());
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->route('employees.index')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('employees.index')->with('success', 'Successfully saved!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return Inertia::render('Employees/ShowEmployee', [
            'employee' => $this->employeeService->showEmployee($employee),
            'user' => $employee->user
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return Inertia::render('Employees/EditEmployee', [
            'employee' => $this->employeeService->showEmployee($employee),
            'user' => $employee->user
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeFormRequest $request, Employee $employee)
    {
        try {
            DB::beginTransaction();
            $this->employeeService->updateEmployee($request->validated(), $employee);
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->route('employees.index')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('employees.index')->with('success', 'Successfully saved!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $result = $this->employeeService->deleteEmployee($employee);

        if ($result) {
            return redirect()->route('employees.index')->with('success', 'Successfully deleted!');
        }

        return redirect()->route('employees.index')->with('error', 'error on deletion');
    }

    /**
     * Reset user password | Default Password: P@ssw0rd
     *
     * @param [type] $id
     * @return void
     */
    public function resetPassword($id)
    {
        $result = $this->employeeService->resetPassword($id);

        if ($result) {
            return redirect()->route('employees.index')->with('success', 'Successfully reset the password!');
        }
        return redirect()->route('employees.index')->with('error', 'error on password reset');
    }
}
