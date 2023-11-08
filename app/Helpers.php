<?php

namespace App\Helpers;

use App\Models\Lead;
use App\Models\User;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Agent\Agent;

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

        // for testing local
        // $file_path = Storage::disk('uploads')->getConfig()['root'];
        // Storage::disk('uploads')->put($file_name, file_get_contents($file));

        $file_path = Storage::disk('public')->getConfig()['root'];
        Storage::disk('public')->put($file_name, file_get_contents($file));

        try {
            $result = $lead->update([
                'contract_file' => $file_path,
                'file_name' => $file_name
            ]);

            return $result;
        } catch (Exception $ex) {

            return false;
        }

        return false;
    }

    /**
     * Delete uploaded lead contract image
     *
     * @param string $file_name
     * @return boolean
     */
    public static function deleteFile(string $file_name): bool
    {

        try {

            Storage::disk('public')->delete($file_name);
        } catch (Exception $ex) {

            return false;
        }

        return true;
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
                'name' => 'Call Back',
                'code' => 'CB'
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
     * Get the List of Lead Source
     *
     * @param string|null $team 
     * @return void
     */
    public static function leadSource(?string $team)
    {
        $lead_sources = DB::table('leads')
            ->select(DB::raw("CONCAT(source_prefix,'-',source) AS source"))
            ->distinct('source_prefix');

        if ($team) {

            if ($team == 'ROI') {
                $prefix = ['ROI', 'NMB', 'BROI', 'BNMB'];
            } else if ($team == 'SURVEY') {
                $prefix = ['SURVEY'];
            } else if ($team == 'EXHIBIT') {
                $prefix = ['LSR', 'ALM', 'PRJ', 'LS', 'IP'];
            }

            $lead_sources = $lead_sources->whereIn('source_prefix', $prefix);
        }

        return $lead_sources->orderBy('source')->get()->toArray();
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

    public static function sourcePrefix(): array
    {
        $prefix = [
            [
                'name' => 'LSR',
            ],
            [
                'name' => 'ALM',
            ],
            [
                'name' => 'LS',
            ],
            [
                'name' => 'IP',
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
            ],
            [
                'name' => 'SURVEY',
            ]
        ];

        return $prefix;
    }

    /**
     * generate default email
     *
     * @param [type] $first
     * @param [type] $last
     * @return string
     */
    public static function createEmail($first, $last): string
    {
        // replace ñ to n
        $first = str_replace('ñ', 'n', $first);
        $last = str_replace('ñ', 'n', $last);

        // replace Ñ to n
        $first = str_replace('Ñ', 'n', $first);
        $last = str_replace('Ñ', 'n', $last);

        //get the first name
        $first = strtok($first, ' ');

        // concat if last name has space
        $last = str_replace(' ', '', $last);

        $email = sprintf('%s.%s@astoria.com.ph', $first, $last);

        // check if email already exist
        $is_exist = (User::where('email', $email)->count() > 0) ? true : false;

        if ($is_exist) {
            $count = 1;

            // create again if email is existed
            do {
                $is_exist = (User::where('email', $email)->count() > 0) ? true : false;

                $email = sprintf('%s.%s%d@astoria.com.ph', $first, $last, $count);
                $count++;
            } while ($is_exist);
        }

        return strtolower($email);
    }

    /**
     * Get the prefix of exhibit
     *
     * @return array
     */
    public static function exhibitPrefixes(): array
    {
        $array = [
            'LSR',
            'ALM',
            'PRJ',
            'LS',
            'IP'
        ];

        return $array;
    }

    /**
     * Get the prefix of ROI
     *
     * @return array
     */
    public static function roiPrefixes(): array
    {
        $array = [
            'ROI',
            'NMB',
            'BROI',
            'BNMB'
        ];

        return $array;
    }

    /**
     * Get the prefix of Holiday survey
     *
     * @return array
     */
    public static function surveyPrefix(): array
    {
        $array = [
            'SURVEY'
        ];

        return $array;
    }

    /**
     * Get the device information used by the user
     *
     * @return array
     */
    public static function deviceInfo(): array
    {
        $agent = new Agent();

        return [
            'device' => $agent->device(),
            'platform' => $agent->platform(),
            'browser' => $agent->browser()
        ];
    }

    /**
     * list all the distinct position
     *
     * @return array
     */
    public static function positionList(): array
    {
        $positions = DB::table('employees')
            ->select('position')
            ->groupBy('position')
            ->get()
            ->toArray();

        return $positions;
    }

    /**
     * list all the distinct refer_by
     *
     * @param string|null $source
     * @value ROI|SURVEY|EXHIBIT
     * @return array
     */
    public static function referByList(?string $source): array
    {
        $filter_source = [];
        $refer_bys = DB::table('leads')
            ->select('refer_by')
            ->whereNotNull('refer_by')
            ->groupBy('refer_by')
            ->get()
            ->toArray();

        if ($source) {
            switch ($source) {
                case "ROI":
                    $filter_source = self::roiPrefixes();
                    break;
                case "SURVEY":
                    $filter_source = self::surveyPrefix();
                    break;
                case "EXHIBIT":
                    $filter_source = self::exhibitPrefixes();
                    break;
                default:
                    $filter_source = [
                        'LSR',
                        'ALM',
                        'PRJ',
                        'LS',
                        'IP',
                        'ROI',
                        'NMB',
                        'BROI',
                        'BNMB',
                        'SURVEY'
                    ];
            }


            $refer_bys = DB::table('leads')
                ->select('refer_by')
                ->whereNotNull('refer_by')
                ->whereIn('source_prefix', $filter_source)
                ->groupBy('refer_by')
                ->get()
                ->toArray();
        }

        return $refer_bys;
    }
}
