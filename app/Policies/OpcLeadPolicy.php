<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\User;

class OpcLeadPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function read(User $user): bool
    {
        return Helper::checkAccess('opc-leads', 'read');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // $permission = $user->employee->userGroup->user_group_permissions->map(function ($item, $key) {
        //     return $item->permission;
        // })->where('module', 'leads');

        // return $permission->contains('type', 'create');

        return Helper::checkAccess('opc-leads', 'create');
    }

    /**
     * Determine whether the user can edit the model.
     */
    public function update(User $user): bool
    {
        // $permission = $user->employee->userGroup->user_group_permissions->map(function ($item, $key) {
        //     return $item->permission;
        // })->where('module', 'leads');

        // return $permission->contains('type', 'update');

        return Helper::checkAccess('opc-leads', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        // $permission = $user->employee->userGroup->user_group_permissions->map(function ($item, $key) {
        //     return $item->permission;
        // })->where('module', 'leads');


        // return $permission->contains('type', 'delete');

        return Helper::checkAccess('opc-leads', 'delete');
    }
}
