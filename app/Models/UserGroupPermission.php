<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserGroupPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_group_id',
        'permission_id',
    ];

    /**
     * associate to user group
     *
     * @return void
     */
    public function userGroup()
    {
        return $this->belongsTo(UserGroup::class);
    }

    /**
     * associate to permission
     *
     * @return void
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class);
    }
}
