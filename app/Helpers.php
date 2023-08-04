<?php

namespace App\Helpers;

use App\Models\Lead;
use App\Models\User;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Helper
{

    /**
     * Upload lead contract image
     *
     * @param [type] $file
     * @param Lead $lead
     * @return boolean
     */
    public static function uploadFile($file, Lead $lead): bool
    {
        $file_name = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $file_name);
        $file_path = 'uploads/' . $file_name;

        // $file_name = time().'_'.$file->getClientOriginalName();
        // if($lead->contract_file) {
        //     if(Storage::disk('public')->exists($lead->contract_file)) {
        //         Storage::disk('public')->delete($lead->contract_file);
        //     }
        // }

        // $file_path = $file->store('uploads', 'public');

        try {
            $result = $lead->update([
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
    public static function checkAccess($module, $type): bool
    {
        $permission = Auth::user()->employee->userGroup->user_group_permissions->map(function ($item, $key) {
            return $item->permission;
        })->where('module', $module);

        return $permission->contains('type', $type);
    }

    /**
     * clear notifications in sesssion
     *
     * @return string
     */
    public static function clearNotifications(): string
    {
        session()->forget('success');
        session()->forget('error');
        session()->forget('message');

        return "successfully cleared";
    }

    /**
     * Array of lead statuses
     *
     * @return array
     */
    public static function leadStatus(): array
    {
        $statuses = [
            [
                'name' => 'Booked',
                'code' => 'B'
            ],
            [
                'name' => 'Qualified',
                'code' => 'Q'
            ],
            [
                'name' => 'Not Qualified',
                'code' => 'NQ'
            ],
            [
                'name' => 'Not Interested',
                'code' => 'NI'
            ],
            [
                'name' => 'Not Answering',
                'code' => 'NA'
            ],
            [
                'name' => 'Already Attended',
                'code' => 'AA'
            ],
            [
                'name' => 'Not yet in Service',
                'code' => 'NIS'
            ],
            [
                'name' => 'On the Spot',
                'code' => 'OTS'
            ],
            [
                'name' => 'No such person',
                'code' => 'NSP'
            ],
            [
                'name' => 'Wrong Number',
                'code' => 'WN'
            ],
            [
                'name' => 'Cancelled',
                'code' => 'C'
            ],
        ];

        return $statuses;
    }

    public static function leadConfirmerStatus(): array
    {
        $statuses = [
            [
                'name' => 'Confirm',
                'code' => 'C'
            ],
            [
                'name' => 'Not confirm',
                'code' => 'NC'
            ],
            [
                'name' => 'Can\'t come or cancelled',
                'code' => 'CC'
            ],
            [
                'name' => 'Not interested',
                'code' => 'NI'
            ],
            [
                'name' => 'No Show',
                'code' => 'NS'
            ],
            [
                'name' => 'Cannot be reached',
                'code' => 'CBR'
            ],
            [
                'name' => 'Reschedule',
                'code' => 'R'
            ],
        ];

        return $statuses;
    }

    /**
     * list the lead sources
     *
     * @return void
     */
    public static function leadSource()
    {
        $lead_sources = DB::table('leads')
            ->select(DB::raw("CONCAT('source_prefix','source') AS source"))
            ->groupBy('source_prefix', 'source')
            ->get()
            ->toArray();

        return $lead_sources;
    }

    /**
     * list all the distinct occupations
     *
     * @return void
     */
    public static function occupationList()
    {
        $occupations = DB::table('leads')
            ->select('occupation')
            ->groupBy('occupation')
            ->get()
            ->toArray();

        return $occupations;
    }

    public static function sourcePrefix() : array
    {
        $prefix = [
            [
                'name' => 'LSR',
            ],
            [
                'name' => 'ALM',
            ],
            [
                'name' => 'PRJ',
            ],
            [
                'name' => 'ROI',
            ],
            [
                'name' => 'NMB',
            ],
            [
                'name' => 'BROI',
            ],
            [
                'name' => 'BNMB',
            ]
        ];

        return $prefix;
    }
}
