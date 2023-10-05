<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\User;

class ActivityLogPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function read(User $user): bool
    {
        return Helper::checkAccess('confirms', 'read');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Helper::checkAccess('confirms', 'create');
    }

    /**
     * Determine whether the user can edit the model.
     */
    public function update(User $user): bool
    {
        return Helper::checkAccess('confirms', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return Helper::checkAccess('confirms', 'delete');
    }
}
