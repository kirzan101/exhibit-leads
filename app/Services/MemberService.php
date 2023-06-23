<?php

namespace App\Services;

use App\Models\Member;
use Illuminate\Http\Request;

class MemberService
{
    public function indexMember()
    {
        return Member::all();
    }

    /**
     * create member
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
        ]);

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
        $member = tap($member)->update($request);

        return $member;
    }
}