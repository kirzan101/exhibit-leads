<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'event',
        'browser',
        'properties',
        'subject_id',
        'causer_id',
    ];
}
