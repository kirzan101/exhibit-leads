<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department',
        'description',
        'property_id',
    ];

    /**
     * Get the list of permissions of user group
     *
     * @return void
     */
    public function user_group_permissions()
    {
        return $this->hasMany(UserGroupPermission::class);
    }

    /**
     * assoiciate employee to property
     *
     * @return void
     */
    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }
}
