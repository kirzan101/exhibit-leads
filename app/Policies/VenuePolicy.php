<?php

namespace App\Policies;

use App\Helpers\Helper;
use App\Models\User;
use App\Models\Venue;
use Illuminate\Auth\Access\Response;

class VenuePolicy
{
    /**
     * Determine whether the user can create models.
     */
    public function read(User $user): bool
    {
        return Helper::checkAccess('venues', 'read');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return Helper::checkAccess('venues', 'create');
    }

    /**
     * Determine whether the user can edit the model.
     */
    public function update(User $user): bool
    {
        return Helper::checkAccess('venues', 'update');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return Helper::checkAccess('venues', 'delete');
    }
}
