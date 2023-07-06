<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Member;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use File;
use Illuminate\Support\Facades\Auth;

class MemberService
{
    /**
     * index of member service
     *
     * @return void
     */
    public function indexMember(): Collection
    {
        $member = Member::where('is_assigned', false)->get();

        return $member;
    }

    /**
     * create member service
     *
     * @param Request $request
     * @return void
     */
    public function createMember(array $request): Member
    {
        // convert array to single string
        $owned_gadgets = implode(',', $request['owned_gadgets']);

        $member = Member::create([
            'title' => $request['title'],
            'first_name' => $request['first_name'],
            'middle_name' => $request['middle_name'],
            'last_name' => $request['last_name'],
            'alias' => $request['alias'],
            'suffix' => $request['suffix'],
            'birth_date' => $request['birth_date'],
            'address' => $request['address'],
            'secondary_address' => $request['secondary_address'],
            'nationality' => $request['nationality'],
            'gender' => $request['gender'],
            'civil_status' => $request['civil_status'],
            'company_name' => $request['company_name'],
            'company_number' => $request['company_number'],
            'occupation' => $request['occupation'],
            'email' => $request['email'],
            'mobile_number_one' => $request['mobile_number_one'],
            'mobile_number_two' => $request['mobile_number_two'],
            'telephone' => $request['telephone'],
            'fax' => $request['fax'],
            'combined_monthly_income' => $request['combined_monthly_income'],
            'internet_connection' => $request['internet_connection'],
            'owned_gadgets' => $owned_gadgets,
            'other_gadgets' => $request['other_gadgets'],
            'spouse_occupation' => $request['spouse_occupation'],
            'nature_of_business' => $request['nature_of_business'],
            'property_assigned' => $request['property_assigned'],
            'created_by' => Auth::user()->employee->id
        ]);

        if ($request['contract_file']) {
            $result = Helper::uploadFile($request['contract_file'], $member);

            if (!$result) {
                throw ValidationException::withMessages(['error on file upload']);
            }
        }

        return $member;
    }

    /**
     * update member
     *
     * @param array $request
     * @param Member $member
     * @return Member
     */
    public function updateMember(array $request, Member $member): Member
    {
        $owned_gadgets = implode(',', $request['owned_gadgets']);
        $request['owned_gadgets'] = $owned_gadgets;
        $request['updated_by'] = Auth::user()->employee->id;

        $member = tap($member)->update($request);

        return $member;
    }

    /**
     * delete member service
     *
     * @param Member $member
     * @return boolean
     */
    public function deleteMember(Member $member): bool
    {
        $member = $member->delete();

        return $member;
    }

    public function showMember(Member $model): Member
    {
        $owned_gadgets = $model->owned_gadgets;
        $arrayed_owned_gadgets = explode(',', $owned_gadgets);

        $member = new Member;
        $member = $model;
        $member->owned_gadgets = $arrayed_owned_gadgets;

        if ($model->contract_file) {
            // $member->contract_file = response()->file(public_path($model->contract_file))->getFile();
            $member->contract_file = $member->getUploadedFile();
        }
        // $member->contract_file = File::files(public_path('uploads'))[0];
        // dd(File::files(public_path('uploads'))[0]);
        // dd(response()->file(public_path($model->contract_file))->getFile());

        // dd($member);

        return $member;
    }

    public function modifyRemarks(array $request) : bool
    {
        $member = Member::find($request['member_id']);

        return $member->update([
            'remarks' => $request['remarks'],
            'updated_by' => Auth::user()->employee->id
        ]);
    }
}
