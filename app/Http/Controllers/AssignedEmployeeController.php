<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignedEmployeeFormRequest;
use App\Models\AssignedEmployee;
use App\Services\AssignedEmployeeService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AssignedEmployeeController extends Controller
{
    private AssignedEmployeeService $assignedEmployee;

    public function __construct(AssignedEmployeeService $assignedEmployee)
    {
        $this->assignedEmployee = $assignedEmployee;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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

            $this->assignedEmployee->createAssignedEmployee($request->validated());
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
    public function show(AssignedEmployee $assignedEmployee)
    {
        //
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
    public function update(Request $request, AssignedEmployee $assignedEmployee)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AssignedEmployee $assignedEmployee)
    {
        //
    }
}
