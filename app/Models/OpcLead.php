<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpcLead extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'companion_first_name',
        'companion_middle_name',
        'companion_last_name',
        'address',
        'hotel',
        'mobile_number',
        'occupation',
        'age',
        'source',
        'civil_status',
        'is_uploaded',
    ];


    /**
     * Get the full name
     *
     * @return string
     */
    public function getFullName()
    {
        return sprintf('%s, %s', $this->last_name, $this->first_name);
    }
}
