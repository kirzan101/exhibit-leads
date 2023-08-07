<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\AssignedExhibitor;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssignedExhibitorPolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function read(User $user): bool
    {
        return Helper::checkAccess('assign-exhibitors', 'read');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Helper::checkAccess('assign-exhibitors', 'create');
    }

    /**
     * Determine whether the user can edit the model.
     */
    public function update(User $user): bool
    {
        return Helper::checkAccess('assign-exhibitors', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return Helper::checkAccess('assign-exhibitors', 'delete');
    }
}
