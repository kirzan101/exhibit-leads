<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
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
        'property_id',
        'contract_file',
        'is_assigned',
        'is_invited',
        'is_confirmed',
        'remarks',
        'confirmer_remarks',
        'lead_status',
        'employee_id',
        'created_by',
        'updated_by',
    ];

    /**
     * Get the full name
     *
     * @return string
     */
    public function getFullName()
    {
        return sprintf('%s %s, %s %s', $this->title, $this->last_name, $this->first_name, $this->middle_name);
    }

    public function getMobileNumber()
    {
        if($this->mobile_number_two == null) {
            return $this->mobile_number_one;
        }

        return sprintf('%s/%s', $this->mobile_number_one, $this->mobile_number_two);
    }

    /**
     * associate member to employee
     *
     * @return void
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * associate member to a property
     *
     * @return void
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * Get uploaded file name
     *
     * @return void
     */
    public function getFileName()
    {
        return ltrim(strstr($this->contract_file, "/"), '/');
    }

    public function getAssignedConfirmer()
    {
        $confirmer = Employee::select('employees.*')
            ->join('assigned_confirmers', 'assigned_confirmers.employee_id', '=', 'employees.id')
            ->where('assigned_confirmers.lead_id', $this->id)
            ->first();

        if($confirmer) {
            return $confirmer->getFullName();
        }

        return '-';
    }

    /**
     * associate member created by to employee
     *
     * @return void
     */
    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    /**
     * associate member updated by to employee
     *
     * @return void
     */
    public function updatedBy()
    {
        return $this->belongsTo(Employee::class, 'updated_by')->withDefault();
    }
}
