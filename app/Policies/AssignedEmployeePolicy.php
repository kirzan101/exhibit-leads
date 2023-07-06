<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\AssignedEmployee;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AssignedEmployeePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function read(User $user): bool
    {
        return Helper::checkAccess('assigns', 'read');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Helper::checkAccess('assigns', 'create');
    }

    /**
     * Determine whether the user can edit the model.
     */
    public function update(User $user): bool
    {
        return Helper::checkAccess('assigns', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return Helper::checkAccess('assigns', 'delete');
    }
}
