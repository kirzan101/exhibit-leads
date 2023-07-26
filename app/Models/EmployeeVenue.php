<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeVenue extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'venue_id'
    ];

    /**
     * associate employee_venue to employee
     *
     * @return Employee
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * associate employee_venue to venue
     *
     * @return Venue
     */
    public function venue()
    {
        return $this->belongsTo(Venue::class, 'venue_id');
    }
}
