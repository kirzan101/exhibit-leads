<?php

namespace App\Helpers;

use App\Models\Member;
use Exception;

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
}
