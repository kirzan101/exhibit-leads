<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'title',
        'alias',
        'suffix',
        'birth_date',
        'age',
        'address',
        'secondary_address',
        'nationality',
        'gender',
        'civil_status',
        'company_name',
        'company_number',
        'occupation',
        'email',
        'mobile_number_one',
        'mobile_number_two',
        'telephone',
        'fax',
        'combined_monthly_income',
        'internet_connection',
        'owned_gadgets',
        'other_gadgets',
        'spouse_first_name',
        'spouse_last_name',
        'spouse_occupation',
        'nature_of_business',
        'property_id',
        'contract_file',
        'is_booker_assigned',
        'is_done',
        'is_done_confirmed',
        'is_showed',
        'is_confirm_assigned',
        'is_exhibitor_assigned',
        'remarks',
        'confirmer_remarks',
        'lead_status',
        'lead_status_confirmer',
        'exhibit_code',
        'source_prefix',
        'source',
        'presentation_date',
        'presentation_time',
        'refer_by',
        'holiday_consultant',
        'membership_type',
        'is_confidential',
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
     * get concat source
     *
     * @return void
     */
    public function getSource()
    {
        return sprintf('%s-%s', $this->source_prefix, $this->source);
    }

    /**
     * get full name of spouse
     *
     * @return void
     */
    public function getSpouseFullName()
    {
        return sprintf('%s, %s', $this->spouse_last_name, $this->spouse_first_name);
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

    /**
     * associate assigned confirmer to lead
     *
     * @return void
     */
    public function assignedConfirmer()
    {
        return $this->hasOne(AssignedConfirmer::class);
    }

    /**
     * associate assigned employee to lead
     *
     * @return void
     */
    public function assignedEmployee()
    {
        return $this->hasOne(AssignedEmployee::class);
    }

    /**
     * associate assigned exhibitor to lead
     *
     * @return void
     */
    public function assignedExhibitor()
    {
        return $this->hasOne(AssignedExhibitor::class);
    }

    /**
     * associate lead to source
     *
     * @return void
     */
    public function source()
    {
        return $this->belongsTo(Source::class, 'source_id');
    }

    /**
     * ge the assigned employee full name
     *
     * @return void
     */
    public function getAssignedEmployee()
    {
        $employee = Employee::select('employees.*')
            ->join('assigned_employees', 'assigned_employees.employee_id', '=', 'employees.id')
            ->where('assigned_employees.lead_id', $this->id)
            ->first();

        if($employee) {
            return $employee->getFullName();
        }

        return '-';
    }

    /**
     * get the assigned confirmer full name
     *
     * @return void
     */
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
     * get the assigned exhibitor full name
     *
     * @return void
     */
    public function getAssignedExhibitor()
    {
        $exhibitor = Employee::select('employees.*')
            ->join('assigned_exhibitors', 'assigned_exhibitors.employee_id', '=', 'employees.id')
            ->where('assigned_exhibitors.lead_id', $this->id)
            ->first();

        if($exhibitor) {
            return $exhibitor->getFullName();
        }

        return '-';
    }

    /**
     * get the presentation date & time
     *
     * @return void
     */
    public function getPresentationDateTime() {
        $time = Carbon::parse($this->presentation_time)->format('g:i A');
        $date = Carbon::parse($this->presentation_date)->format('Y-m-d');

        return sprintf('%s %s', $date, $time);
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
