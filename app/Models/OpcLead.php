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
        'source_prefix',
        'civil_status',
        'date_filled',
        'is_uploaded',
        'remarks'
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

    /**
     * Get the companion full name
     *
     * @return string
     */
    public function getCompanionFullName()
    {
        if(!$this->companion_first_name || !$this->companion_last_name) {
            return '';
        }

        return sprintf('%s, %s', $this->companion_last_name, $this->companion_first_name); 
    }

    /**
     * Get the source prefix & source
     *
     * @return void
     */
    public function getSource()
    {
        return sprintf('%s-%s', $this->source_prefix, $this->source);
    }
}
