<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\Member;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class MemberPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function read(User $user): bool
    {
        // $permission = $user->employee->userGroup->user_group_permissions->map(function ($item, $key) {
        //     return $item->permission;
        // })->where('module', 'members');

        // return $permission->contains('type', 'read');

        return Helper::checkAccess('members', 'read');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // $permission = $user->employee->userGroup->user_group_permissions->map(function ($item, $key) {
        //     return $item->permission;
        // })->where('module', 'members');

        // return $permission->contains('type', 'create');

        return Helper::checkAccess('members', 'create');
    }

    /**
     * Determine whether the user can edit the model.
     */
    public function update(User $user): bool
    {
        // $permission = $user->employee->userGroup->user_group_permissions->map(function ($item, $key) {
        //     return $item->permission;
        // })->where('module', 'members');

        // return $permission->contains('type', 'update');

        return Helper::checkAccess('members', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        // $permission = $user->employee->userGroup->user_group_permissions->map(function ($item, $key) {
        //     return $item->permission;
        // })->where('module', 'members');


        // return $permission->contains('type', 'delete');

        return Helper::checkAccess('members', 'delete');
    }
}
