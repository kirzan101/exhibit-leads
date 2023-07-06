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
        'property',
        'user_id',
        'user_group_id',
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
}
