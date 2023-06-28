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
        'property'
    ];

    /**
     * Get the full name
     *
     * @return string
     */
    public function getFullName() {
        return sprintf('%s, %s', $this->last_name, $this->first_name);
    }
}
