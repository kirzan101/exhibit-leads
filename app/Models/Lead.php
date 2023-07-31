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
        'is_showed',
        'is_confirm_assigned',
        'remarks',
        'confirmer_remarks',
        'lead_status',
        'lead_status_confirmer',
        'exhibit_code',
        'source',
        'presentation_date',
        'refer_by',
        'holiday_consultant',
        'membership_type',
        'is_confidential',
        'employee_id',
        'venue_id',
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

    /**
     * get the mobile numbers
     *
     * @return void
     */
    public function getMobileNumber()
    {
        if($this->mobile_number_two == null) {
            return $this->mobile_number_one;
        }

        return sprintf('%s/%s', $this->mobile_number_one, $this->mobile_number_two);
    }

    /**
     * associate lead to employee
     *
     * @return void
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * associate lead to a property
     *
     * @return void
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    /**
     * associate lead to a venue
     *
     * @return void
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
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

    public function assignedConfirmer()
    {
        return $this->hasOne(AssignedConfirmer::class);
    }

    public function assignedEmployee()
    {
        return $this->hasOne(AssignedEmployee::class);
    }

    public function getAssignedEmployee()
    {
        $confirmer = Employee::select('employees.*')
            ->join('assigned_employees', 'assigned_employees.employee_id', '=', 'employees.id')
            ->where('assigned_employees.lead_id', $this->id)
            ->first();

        if($confirmer) {
            return $confirmer->getFullName();
        }

        return '-';
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