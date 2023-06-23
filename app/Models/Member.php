<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name', //x
        'last_name',
        'title',
        'alias', //x
        'suffix', //x
        'birth_date',
        'address',
        'secondary_address', //x
        'nationality',
        'gender',
        'civil_status',
        'company_name', //x
        'company_number', //x
        'occupation',
        'email',
        'mobile_number_one',
        'mobile_number_two', //x
        'telephone', //x
        'fax', //x
        'combined_monthly_income',
        'internet_connection',
        'owned_gadgets',
        'other_gadgets', //x
        'spouse_occupation',
        'nature_of_business',
    ];

    /**
     * Get the full name
     *
     * @return string
     */
    public function getFullName() {
        return sprintf('%s %s, %s %s', $this->title, $this->last_name, $this->first_name, $this->middle_name );
    }
}
