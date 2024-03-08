<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OpcLeadsExport implements FromArray, WithHeadings
{
    use Exportable;

    protected $opcLeads;

    public function __construct(array $opcLeads)
    {
        $this->opcLeads = $opcLeads;
    }

    /**
     *
     * @return array
     */
    public function array(): array
    {
        return $this->opcLeads;
    }

    public function headings(): array
    {
        return [
            "Name",
            "Companion Name",
            "Mobile Number",
            "Occupation",
            "Age",
            "Address",
            "Hotel",
            "Source",
            "Civil Status",
            "Date Filled",
            "Date Uploaded"
        ];
    }
}
