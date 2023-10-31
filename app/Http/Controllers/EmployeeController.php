<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\EmployeeFormRequest;
use App\Http\Requests\ProfileFormRequest;
use App\Http\Resources\EmployeeResource;
use App\Http\Resources\UserResource;
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
    public function index(Request $request)
    {
        $this->authorize('read', Employee::class);

        $sort_by = $request->sort_by;
        if ($request->sort_by == 'full_name') {
            $request->merge(['sort_by' => 'employees.last_name']);
        }

        // set default to desc
        if ($request->is_sort_desc == null) {
            $request->merge(['is_sort_desc' => true]);
        }

        $employees = EmployeeResource::collection($this->employeeService->indexEmployeePaginate($request->toArray()));

        return Inertia::render('Employees/IndexPaginateEmployee', [
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'module' => 'employees',
            'items' => $employees,
            'user_groups' => $this->userGroupService->indexUserGroup(),
            'positions' => Helper::positionList(),
            'user_group_id' => $request->user_group_id,
            'position' => $request->position
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
            'properties' => $this->propertyService->indexProperty(),
            'exhibitors' => $this->employeeService->indexTeamLead()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EmployeeFormRequest $request)
    {
        $this->authorize('create', Employee::class);

        $request->validate([
            'password' => 'required|min:2'
        ]);

        ['result' => $result, 'message' => $message] = $this->employeeService->createEmployee($request->toArray());

        return redirect()->route('employees.index')->with($result, $message);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $this->authorize('read', Employee::class);

        return Inertia::render('Employees/ShowEmployee', [
            'employee' => new EmployeeResource($this->employeeService->showEmployee($employee)),
            'user' => new UserResource($employee->user),
            'user_groups' => $this->userGroupService->indexUserGroup(),
            'venues' => $this->venueService->indexVenueService(),
            'properties' => $this->propertyService->indexProperty(),
            'exhibitors' => $this->employeeService->indexTeamLead()
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
            'user' => new UserResource($employee->user),
            'user_groups' => $this->userGroupService->indexUserGroup(),
            'venues' => $this->venueService->indexVenueService(),
            'properties' => $this->propertyService->indexProperty(),
            'exhibitors' => $this->employeeService->indexTeamLead()
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

        $request->merge(['user_id' => $employee->user_id]);

        ['result' => $result, 'message' => $message] = $this->employeeService->updateEmployee($request->toArray(), $employee);

        return redirect()->route('employees.index')->with($result, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $this->authorize('delete', Employee::class);

        ['result' => $result, 'message' => $message] = $this->employeeService->deleteEmployee($employee);

        return redirect()->back()->with($result, $message);
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

        ['result' => $result, 'message' => $message] = $this->employeeService->resetPassword($id);

        return redirect()->back()->with($result, $message);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function profile()
    {
        $employee = Auth::user()->employee;
        return Inertia::render('Profiles/IndexProfile', [
            'employee' => $this->employeeService->showEmployee($employee),
            'user' => new UserResource(Auth::user()),
            'properties' => $this->propertyService->indexProperty()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function profileEdit(ProfileFormRequest $request)
    {
        $employee = Auth::user()->employee;

        $request->merge(['user_id' => $employee->user_id]);
        $request->merge(['user_group_id' => $employee->user_group_id]);

        ['result' => $result, 'message' => $message] = $this->employeeService->updateEmployee($request->toArray(), $employee);

        // if password is filled
        if ($request->password != null) {
            $this->employeeService->updatePassword($request->toArray(), $employee->user_id);
        }

        return redirect()->back()->with($result, 'Successfully updated the profile!');
    }

    /**
     * update user password
     *
     * @param Request $request
     * @param [Integer] $user_id
     * @return void
     */
    public function updatePassword(Request $request, $user_id)
    {
        $request->validate([
            'password' => 'required|min:3'
        ]);

        ['result' => $result, 'message' => $message] = $this->employeeService->updatePassword($request->toArray(), $user_id);

        return redirect()->back()->with($result, $message);
    }
}
