<?php

namespace App\Services;

use Alimranahmed\LaraOCR\Facades\OCR;

class OcrService
{

    /**
     * scan uploaded image
     *
     * @param array $request
     * @return string
     */
    public function scan(array $request): string
    {
        $result = OCR::scan($request['uploaded_image']);

        return $result;
    }

    /**
     * format scanned images
     *
     * @param String $request
     * @return array
     */
    public function formatScanned(String $request): array
    {
        //separate lines by "\n"
        $request_result = explode("\n", trim($request));

        $keys = array();
        $values = array();
        foreach ($request_result as $value) {

            // filter only fields with colon
            if (str_contains($value, ':')) {

                //separate field from input
                $get_named_value = explode(":", trim($value));

                $key = str_replace(" ", '_', strtolower($get_named_value[0]));
                // $value = trim(strtolower($get_named_value[1]));
                $value = trim($get_named_value[1]);

                array_push($keys, $key);
                array_push($values, $value);
            }
        }

        $result = array_combine($keys, $values);

        return $result;
    }
}
