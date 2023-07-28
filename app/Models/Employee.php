<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'position',
        'property_id',
        'user_id',
        'user_group_id',
        'venue_id'
    ];

    /**
     * Get the full name
     *
     * @return string
     */
    public function getFullName() {
        return sprintf('%s, %s', $this->last_name, $this->first_name);
    }

    /**
     * associate user to employee
     *
     * @return void
     */
    public function user() {
        return $this->belongsTo(User::class);
    }

    /**
     * associate user group to employee
     *
     * @return void
     */
    public function userGroup() {
        return $this->belongsTo(UserGroup::class);
    }

    /**
     * associate property to employee
     *
     * @return void
     */
    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    /**
     * get the associated list of employee venue
     *
     * @return void
     */
    public function employeeVenue()
    {
        return $this->hasMany(EmployeeVenue::class);
    }
}
