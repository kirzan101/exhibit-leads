<?php

namespace App\Http\Controllers;

use App\Http\Requests\MemberFormRequest;
use App\Models\Member;
use App\Services\EmployeeService;
use App\Services\MemberService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MemberController extends Controller
{
    private MemberService $memberService;
    private EmployeeService $employeeService;

    public function __construct(MemberService $memberService, EmployeeService $employeeService)
    {
        $this->memberService = $memberService;
        $this->employeeService = $employeeService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // return $this->memberService->indexMember();
        return Inertia::render('Members/IndexMember', [
            'members' => $this->memberService->indexMember(),
            'employees' => $this->employeeService->indexEmployee(),
            'per_page' => 5
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Members/CreateMember', []);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MemberFormRequest $request)
    {
        // dd($request->file('contract_file'));
        try {
            DB::beginTransaction();

            // add member
            $this->memberService->createMember($request->validated());

        } catch (Exception $ex) {

            DB::rollBack();

            return redirect()->route('members.index')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('members.index')->with('success', 'Successfully saved!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Member $member)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        // dd($this->memberService->showMember($member)->owned_gadgets);
        return Inertia::render('Members/EditMember', [
            'member' => $this->memberService->showMember($member)
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MemberFormRequest $request, Member $member)
    {
        try {
            DB::beginTransaction();

            $this->memberService->updateMember($request->validated(), $member);

        } catch (Exception $ex) {

            DB::rollBack();

            return redirect()->route('members.index')->with('error', $ex->getMessage());
        }

        DB::commit();
        return redirect()->route('members.index')->with('success', 'Successfully updated!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Member $member)
    {
        //
    }
}
