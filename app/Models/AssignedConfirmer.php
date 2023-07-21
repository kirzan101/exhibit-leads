<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedConfirmer extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'lead_id',
        'created_by',
        'updated_by'
    ];

    /**
     * associate assigned confirmer to an employee
     *
     * @return Employee
     */
    public function employee()
    {
        $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * associate assigned confirmer to a lead
     *
     * @return Lead
     */
    public function lead()
    {
        $this->belongsTo(Lead::class, 'lead_id');
    }

    /**
     * associate assigned confirmer to an employee
     *
     * @return Employee
     */
    public function createdBy()
    {
        $this->belongsTo(Employee::class, 'created_by');
    }

    /**
     * associate assigned confirmer to an employee
     *
     * @return Employee
     */
    public function updatedBy()
    {
        $this->belongsTo(Employee::class, 'updated_by');
    }
}
