<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeFormRequest;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Services\UserGroupService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    private EmployeeService $employeeService;
    private UserGroupService $userGroupService;

    public function __construct(EmployeeService $employeeService, UserGroupService $userGroupService)
    {
        $this->employeeService = $employeeService;
        $this->userGroupService = $userGroupService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('read', Employee::class);

        return Inertia::render('Employees/IndexEmployee', [
            'employees' => $this->employeeService->indexEmployee(),
            'per_page' => 5,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Employee::class);

        return Inertia::render('Employees/CreateEmployee', [
            'user_groups' => $this->userGroupService->indexUserGroup()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeFormRequest $request)
    {
        $this->authorize('create', Employee::class);

        try {
            $request->validate([
                'password' => 'required|min:2'
            ]);
            
            DB::beginTransaction();

            $this->employeeService->createEmployee($request->toArray());
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
        $this->authorize('read', Employee::class);

        return Inertia::render('Employees/ShowEmployee', [
            'employee' => $this->employeeService->showEmployee($employee),
            'user' => $employee->user,
            'user_groups' => $this->userGroupService->indexUserGroup()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $this->authorize('update', Employee::class);

        return Inertia::render('Employees/EditEmployee', [
            'employee' => $this->employeeService->showEmployee($employee),
            'user' => $employee->user,
            'user_groups' => $this->userGroupService->indexUserGroup()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeFormRequest $request, Employee $employee)
    {
        $this->authorize('update', Employee::class);

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
        $this->authorize('delete', Employee::class);

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
        $this->authorize('update', Employee::class);

        $result = $this->employeeService->resetPassword($id);

        if ($result) {
            return redirect()->route('employees.index')->with('success', 'Successfully reset the password!');
        }
        return redirect()->route('employees.index')->with('error', 'error on password reset');
    }
}
