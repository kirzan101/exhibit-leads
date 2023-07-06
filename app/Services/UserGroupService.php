<?php

namespace App\Services;

use App\Models\UserGroup;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class UserGroupService
{
    /**
     * index of user group service
     *
     * @return Collection
     */
    public function indexUserGroup() : Collection
    {
        $user_groups = UserGroup::all();

        $isAdmin = (Auth::user()->employee->userGroup->name != 'admin') ?? false;

        if($isAdmin) {
            $user_groups = UserGroup::where('name', '!=', 'admin')->get();
        }

        return $user_groups;
    }

    /**
     * create user group service
     *
     * @param array $request
     * @return UserGroup
     */
    public function createUserGroup(array $request) : UserGroup
    {
        $user_group = UserGroup::create($request);

        return $user_group;
    }

    /**
     * update user group service
     *
     * @param array $request
     * @param UserGroup $userGroup
     * @return UserGroup
     */
    public function updateUserGroup(array $request, UserGroup $userGroup) : UserGroup
    {
        $user_group = tap($userGroup)->update($request);

        return $user_group;
    }

    /**
     * delete user group service
     *
     * @param UserGroup $userGroup
     * @return boolean
     */
    public function deleteUserGroup(UserGroup $userGroup) : bool
    {
        return $userGroup->delete();
    }

    /**
     * show user group service
     *
     * @param UserGroup $userGroup
     * @return UserGroup
     */
    public function showUserGroup(UserGroup $userGroup) : UserGroup
    {
        return $userGroup;
    }
}