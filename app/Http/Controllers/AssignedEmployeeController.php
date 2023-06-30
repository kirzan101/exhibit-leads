<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignedEmployeeFormRequest;
use App\Http\Resources\MemberResource;
use App\Models\AssignedEmployee;
use App\Models\Employee;
use App\Models\Member;
use App\Services\AssignedEmployeeService;
use App\Services\EmployeeService;
use App\Services\MemberService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AssignedEmployeeController extends Controller
{
    private AssignedEmployeeService $assignedEmployeeService;
    private MemberService $memberService;
    private EmployeeService $employeeService;

    public function __construct(AssignedEmployeeService $assignedEmployeeService, MemberService $memberService, EmployeeService $employeeService)
    {
        $this->assignedEmployeeService = $assignedEmployeeService;
        $this->memberService = $memberService;
        $this->employeeService = $employeeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = MemberResource::collection($this->assignedEmployeeService->indexAssignedEmployee());

        return Inertia::render('AssignedEmployees/IndexAssignedEmployee', [
            'members' => $members,
            'employees' => $this->employeeService->indexEmployee(),
            'per_page' => 5
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AssignedEmployeeFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $this->assignedEmployeeService->createAssignedEmployee($request->validated());
        } catch (Exception $ex) {

            DB::rollBack();
            return redirect()->route('members.index')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('members.index')->with('success', 'Successfully assigned!');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $member = Member::find($id);
        $employee = Employee::find($member->employee_id);
        return Inertia::render('AssignedEmployees/ShowAssignedEmployee', [
            'member' => $this->memberService->showMember($member),
            'assigned_employee' => ($employee) ? $employee->getFullName() : '-'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AssignedEmployee $assignedEmployee)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AssignedEmployeeFormRequest $request, AssignedEmployee $assignedEmployee)
    {
        try {
            DB::beginTransaction();

            $this->assignedEmployeeService->updateAssignedEmployee($request->validated(), $assignedEmployee);

        } catch (\Throwable $th) {
            //throw $th;
        }

        return redirect()->route('assigned-employees.index')->with('success', 'Successfully reassigned');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignedEmployee $assignedEmployee)
    {
        //
    }

    public function reassignEmployee(AssignedEmployeeFormRequest $request)
    {
        try {
            DB::beginTransaction();

            $this->assignedEmployeeService->createAssignedEmployee($request->validated());
        } catch (Exception $ex) {

            DB::rollBack();
            return redirect()->route('members.index')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('assigned-employees.index')->with('success', 'Successfully reassigned!');
    }

    public function removeAssignment(AssignedEmployeeFormRequest $request)
    {
        $result = $this->assignedEmployeeService->removedAssgined($request->validated());

        if(!$result) {
            return redirect()->route('assigned-employees.index')->with('error', 'Error on removing assignment!');
        }

        return redirect()->route('assigned-employees.index')->with('success', 'Successfully removed assignment!');
    }
}
