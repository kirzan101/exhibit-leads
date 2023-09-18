<?php

namespace App\Exports;

use App\Models\Lead;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ConfirmedsExport implements FromArray, WithHeadings
{
    use Exportable;

    protected $confirmeds;

    public function __construct(array $confirmeds)
    {
        $this->confirmeds = $confirmeds;
    }

    /**
     *
     * @return array
     */
    public function array(): array
    {
        return $this->confirmeds;
    }

    public function headings(): array
    {
        return ["Lead Name", "Occupation", "Venue", "Source", "Booker", "Exhibitor", "Confirmer", "Done At"];
    }
}
