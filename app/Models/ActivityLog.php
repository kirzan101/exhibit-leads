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
        'status',
        'browser',
        'properties',
        'subject_id',
        'causer_id',
    ];

    /**
     * associate user to causer_id
     *
     * @return void
     */
    public function causer() {
        return $this->belongsTo(User::class, 'causer_id');
    }
}
