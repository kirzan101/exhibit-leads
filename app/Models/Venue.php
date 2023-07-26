<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'created_by',
        'updated_by',
    ];

    /**
     * get the created by details
     *
     * @return Employee
     */
    public function createdBy()
    {
        $this->belongsTo(Employee::class, 'created_by');
    }

    /**
     * get the updated by details
     *
     * @return User
     */
    public function updatedBy()
    {
        $this->belongsTo(Employee::class, 'updated_by');
    }
}
