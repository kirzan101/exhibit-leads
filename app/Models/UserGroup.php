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
}
