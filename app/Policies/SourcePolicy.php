<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\Source;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class SourcePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function read(User $user): bool
    {
        return Helper::checkAccess('sources', 'read');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Helper::checkAccess('sources', 'create');
    }

    /**
     * Determine whether the user can edit the model.
     */
    public function update(User $user): bool
    {
        return Helper::checkAccess('sources', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return Helper::checkAccess('sources', 'delete');
    }
}
