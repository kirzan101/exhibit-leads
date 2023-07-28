<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\EmployeeFormRequest;
use App\Http\Requests\ProfileFormRequest;
use App\Http\Resources\EmployeeResource;
use App\Models\Employee;
use App\Services\EmployeeService;
use App\Services\PropertyService;
use App\Services\UserGroupService;
use App\Services\VenueService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    private EmployeeService $employeeService;
    private UserGroupService $userGroupService;
    private VenueService $venueService;
    private PropertyService $propertyService;

    public function __construct(EmployeeService $employeeService, UserGroupService $userGroupService, VenueService $venueService, PropertyService $propertyService)
    {
        $this->employeeService = $employeeService;
        $this->userGroupService = $userGroupService;
        $this->venueService = $venueService;
        $this->propertyService = $propertyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('read', Employee::class);

        $employees = EmployeeResource::collection($this->employeeService->indexEmployee());

        return Inertia::render('Employees/IndexEmployee', [
            'employees' => $employees,
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
            'user_groups' => $this->userGroupService->indexUserGroup(),
            'venues' => $this->venueService->indexVenueService(),
            'properties' =>$this->propertyService->indexProperty()
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
            'employee' => new EmployeeResource($this->employeeService->showEmployee($employee)),
            'user' => $employee->user,
            'user_groups' => $this->userGroupService->indexUserGroup(),
            'venues' => $this->venueService->indexVenueService(),
            'properties' =>$this->propertyService->indexProperty()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $this->authorize('update', Employee::class);

        return Inertia::render('Employees/EditEmployee', [
            'employee' => new EmployeeResource($this->employeeService->showEmployee($employee)),
            'user' => $employee->user,
            'user_groups' => $this->userGroupService->indexUserGroup(),
            'venues' => $this->venueService->indexVenueService(),
            'properties' => $this->propertyService->indexProperty(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EmployeeFormRequest $request, Employee $employee)
    {
        $this->authorize('update', Employee::class);

        //clear the notification message session
        Helper::clearNotifications();

        try {
            DB::beginTransaction();

            $request->merge(['user_id' => $employee->user_id]);

            $this->employeeService->updateEmployee($request->toArray(), $employee);
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
            return redirect()->route('employees.index')->with('success', 'Successfully reset the password! The new password is:  P@ssw0rd');
        }
        return redirect()->route('employees.index')->with('error', 'error on password reset');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function profile()
    {
        $employee = Auth::user()->employee;
        return Inertia::render('Profiles/IndexProfile', [
            'employee' => $this->employeeService->showEmployee($employee),
            'user' => Auth::user()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function profileEdit(ProfileFormRequest $request)
    {
        $employee = Auth::user()->employee;

        //clear the notification message session
        // Helper::clearNotifications();

        try {
            DB::beginTransaction();

            // add additional fields for ids in request
            $request->merge(['user_id' => $employee->user_id]);
            $request->merge(['user_group_id' => $employee->user_group_id]);

            $employee = $this->employeeService->updateEmployee($request->toArray(), $employee);

            // if password is filled
            if($request->password != null) {
                $this->employeeService->updatePassword($request->toArray(), $employee->user_id);
            }

        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->route('profile')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('profile')->with('success', 'Successfully updated!');
    }
}
