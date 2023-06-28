<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignedEmployee extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'member_id',
        'created_by',
        'updated_by'
    ];
}
