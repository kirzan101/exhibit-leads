<?php

namespace App\Helpers;

use App\Models\Member;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Helper
{

    /**
     * Upload member contract image
     *
     * @param [type] $file
     * @param Member $member
     * @return boolean
     */
    public static function uploadFile($file, Member $member): bool
    {
        $file_name = $file->getClientOriginalName();
        $file->move(public_path('uploads'), $file_name);
        $file_path = 'uploads/' . $file_name;

        // $file_name = time().'_'.$file->getClientOriginalName();
        // if($member->contract_file) {
        //     if(Storage::disk('public')->exists($member->contract_file)) {
        //         Storage::disk('public')->delete($member->contract_file);
        //     }
        // }

        // $file_path = $file->store('uploads', 'public');

        try {
            $result = $member->update([
                'contract_file' => $file_path
            ]);

            return $result;
        } catch (Exception $ex) {

            return false;
        }

        return false;
    }

    /**
     * Generate unique username
     * Username format: {first name initial}{last name}
     * Example result: Juan Dela Cruz => jdelacruz
     *
     * @param [string] $first_name
     * @param [string] $last_name
     * @return string
     */
    public static function username($first_name, $last_name)
    {
        $result = null;
        $suffix = 0;

        try {

            // if first_name/last_name is empty, throw null
            if (trim($first_name) == null || trim($first_name) == null)
                return $result;

            do {
                //set the first name to small letters
                $first_name = strtolower(trim($first_name));

                //get the first character of the first name
                $first_character = substr($first_name, 0, 1);

                //set the last name to small letters
                $last_name = strtolower(trim($last_name));

                // remove spaces between word
                $lastname = str_replace(' ', '', $last_name);

                //concat first name first character and the last name
                $result = sprintf('%s%s', $first_character, $lastname);

                // check if username is unique
                $user_count = User::where('username', $result)->count();

                // if username is existing add suffix
                if ($user_count > 0) {
                    $suffix++;
                    $result = sprintf('%s%u', $result, $suffix);

                    // recheck if username is existing
                    $user_count = User::where('username', $result)->count();
                }
            } while ($user_count > 0);
        } catch (\Throwable $th) {
            return $result;
        }

        return $result;
    }

    /**
     * check user access type to a module
     *
     * @param [type] $module
     * @param [type] $type
     * @return boolean
     */
    public static function checkAccess($module, $type) : bool
    {
        $permission = Auth::user()->employee->userGroup->user_group_permissions->map(function ($item, $key) {
            return $item->permission;
        })->where('module', $module);

        return $permission->contains('type', $type);
    }
}
