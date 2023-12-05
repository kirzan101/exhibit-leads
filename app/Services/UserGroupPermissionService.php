<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\UserGroup;
use App\Models\UserGroupPermission;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserGroupPermissionService
{
    public $last_id = null;
    public $module_name = 'user_group_permissions';

    /**
     * index of user group permissions
     *
     * @return Collection
     */
    public function userGroupPermissionIndex(): Collection
    {
        $user_group_permissions = UserGroupPermission::all();

        return $user_group_permissions;
    }

    /**
     * create user group permission service
     *
     * @param array $request
     * @return array
     */
    public function userGroupPermissionCreate(array $request): array
    {
        try {
            DB::beginTransaction();

            $result = UserGroupPermission::create($request);

            $this->last_id = $result->id;

            $return_values =  ['result' => 'success', 'message' => 'Successfully created!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values =  ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        return $return_values;
    }

    /**
     * update user group permission
     *
     * @param array $request
     * @param UserGroupPermission $userGroupPermission
     * @return array
     */
    public function userGroupPermissionUpdate(array $request, UserGroupPermission $userGroupPermission): array
    {
        try {
            DB::beforeExecuting();

            $result = tap($userGroupPermission)->update($request);

            $this->last_id = $result->id;

            $return_values =  ['result' => 'success', 'message' => 'Successfully updated!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values =  ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        return $return_values;
    }

    /**
     * delete user group permision
     *
     * @param UserGroupPermission $userGroupPermission
     * @return array
     */
    public function userGroupPermissionDelete(UserGroupPermission $userGroupPermission): array
    {
        try {
            DB::beginTransaction();

            $this->last_id = $userGroupPermission->id;
            $userGroupPermission->delete();

            $return_values =  ['result' => 'success', 'message' => 'Successfully deleted!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();
            $return_values =  ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        return $return_values;
    }

    /**
     * update user group permission by user group
     *
     * @param array $request
     * @param UserGroup $userGroup
     * @return void
     */
    public function userGroupPermissionUpdateByUserGroup(array $request, UserGroup $userGroup)
    {
        try {
            DB::beginTransaction();

            //removed current permissions of user group
            UserGroupPermission::where('user_group_id', $userGroup->id)->delete();

            // re-create the permission of user group
            foreach ($request['permissions'] as $permission) {
                UserGroupPermission::create([
                    'user_group_id' => $userGroup->getKey(),
                    'permission_id' => $permission
                ]);
            }

            $return_values =  ['result' => 'success', 'message' => 'Successfully updated!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values =  ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        return $return_values;
    }

    /**
     * create multiple user group permisions
     *
     * @param array $request
     * @param integer $userGroupId
     * @return array
     */
    public function userGroupPermissionCreateMultiple(array $request, int $userGroupId): array
    {
        if (count($request['permissions']) == 0) {
            return ['result' => 'error', 'message' => 'Permissions must not empty!', 'subject' => null];
        }

        try {
            DB::beginTransaction();

            foreach ($request['permissions'] as $permission) {
                UserGroupPermission::create([
                    'user_group_id' => $userGroupId,
                    'permission_id' => $permission
                ]);
            }

            $return_values =  ['result' => 'success', 'message' => 'Successfully created!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values =  ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        return $return_values;
    }

    /**
     * delete user group permissions by user group ID
     *
     * @param integer $userGroupId
     * @return array
     */
    public function deleteUserGroupPermissionByUserGroupId(int $userGroupId): array
    {
        try {
            UserGroupPermission::where('user_group_id', $userGroupId)->delete();

            $return_values =  ['result' => 'success', 'message' => 'Successfully created!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            $return_values =  ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }

        return $return_values;
    }
}
