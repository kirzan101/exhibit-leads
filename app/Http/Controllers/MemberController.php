<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Requests\MemberFormRequest;
use App\Http\Resources\MemberResource;
use App\Models\Member;
use App\Services\EmployeeService;
use App\Services\MemberService;
use App\Services\PropertyService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MemberController extends Controller
{
    private MemberService $memberService;
    private EmployeeService $employeeService;
    private PropertyService $propertyService;

    public function __construct(MemberService $memberService, EmployeeService $employeeService, PropertyService $propertyService)
    {
        $this->memberService = $memberService;
        $this->employeeService = $employeeService;
        $this->propertyService = $propertyService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $members = MemberResource::collection($this->memberService->indexMember());
        return Inertia::render('Members/IndexMember', [
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
        $this->authorize('create', Member::class);

        return Inertia::render('Members/CreateMember', [
            'properties' => $this->propertyService->indexProperty()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(MemberFormRequest $request)
    {
        $this->authorize('create', Member::class);

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
        $this->authorize('read', Member::class);

        $member = $this->memberService->showMember($member);

        return Inertia::render('Members/ShowMember', [
            'member' => new MemberResource($member),
            'properties' => $this->propertyService->indexProperty()
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        $this->authorize('update', Member::class);
        // dd($member->getUploadedFile(), Storage::disk('public'));
        $member = $this->memberService->showMember($member);

        return Inertia::render('Members/EditMember', [
            'member' => new MemberResource($member),
            'properties' => $this->propertyService->indexProperty()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(MemberFormRequest $request, Member $member)
    {
        $this->authorize('update', Member::class);

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

    public function remarks(Request $request)
    {
        $this->authorize('update', Member::class);

        $request = $request->validate([
            'member_id' => 'required|exists:members,id',
            'remarks' => 'required|min:2',
        ]);

        $result = $this->memberService->modifyRemarks($request);

        if (!$result) {
            return redirect()->route('assigned-employees.index')->with('error', 'Something went wrong on saving!');
        }

        return redirect()->route('assigned-employees.index')->with('success', 'Successfully saved!');
    }

    /**
     * Display a listing of the resource
     *
     * @param Request $request
     * @return void
     */
    public function indexInvite(Request $request)
    {
        $invited = true;

        $members = MemberResource::collection($this->memberService->indexInvitedMember($invited));

        return Inertia::render('Invites/IndexInvite', [
            'members' => $members,
            'employees' => $this->employeeService->indexEmployee(),
            'per_page' => 5
        ]);
    }

    /**
     * Display a paginate listing of the resource.
     */
    public function indexPaginate(Request $request)
    {
        $per_page = $request->per_page;
        $page = $request->page;

        if (!$per_page) {
            $per_page = 5;
        }

        // if(!$page) {
        //     $page = 1;
        // }

        $members = MemberResource::collection($this->memberService->indexPaginateMember($per_page));

        // dd($members);
        return Inertia::render('Members/PaginateMember', [
            'members' => $members,
            'employees' => $this->employeeService->indexEmployee(),
            'per_page' => 5
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function invite(Request $request)
    {
        $request->validate([
            'member_id' => 'required|exists:members,id',
            'status' => 'required|boolean'
        ]);

        try {
            $member = Member::find($request->member_id);

            $member = $this->memberService->inviteMember($member, $request->status);

        } catch (Exception $e) {
            return redirect()->route('assigned-employees.index')->with('error', $e->getMessage());
        }

        return redirect()->route('assigned-employees.index')->with('success', 'Successfully invited!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function inviteCancel(Request $request)
    {
        $request->validate([
            'member_ids' => 'required|array'
        ]);

        try {
            foreach ($request->member_ids as $member_id) {
                $member = Member::find($member_id);

                $member = $this->memberService->inviteMember($member, false);
            }
        } catch (Exception $e) {
            return redirect()->route('invites')->with('error', $e->getMessage());
        }

        return redirect()->route('invites')->with('success', 'Successfully removed from invitees!');
    }
}
