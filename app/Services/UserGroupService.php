<?php

namespace App\Services;

use App\Models\UserGroup;
use Exception;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserGroupService
{
    private UserGroupPermissionService $userGroupPermissionService;
    private ActivityLogService $activityLogService;
    public function __construct(
        UserGroupPermissionService $userGroupPermissionService,
        ActivityLogService $activityLogService
    ) {
        $this->userGroupPermissionService = $userGroupPermissionService;
        $this->activityLogService = $activityLogService;
    }

    public $last_id = null;
    public $module_name = 'user_groups';

    /**
     * index of user group service
     *
     * @return Collection
     */
    public function indexUserGroup(): Collection
    {
        $user_groups = UserGroup::all();

        $isAdmin = (Auth::user()->employee->userGroup->name != 'admin') ?? false;

        if ($isAdmin) {
            $user_groups = UserGroup::where('name', '!=', 'admin')->get();
        }

        return $user_groups;
    }

    /**
     * create user group service
     *
     * @param array $request
     * @return array
     */
    public function createUserGroup(array $request): array
    {
        try {
            DB::beginTransaction();

            $userGroup = UserGroup::create($request);

            $this->last_id = $userGroup->getKey();

            // create user group permissions
            $this->userGroupPermissionService->userGroupPermissionCreateMultiple($request, $userGroup->getKey());

            $return_values = ['result' => 'success', 'message' => 'Successfully saved!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values =  ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        //activity log
        $this->activityLogService->createActivityLog([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'create',
            'status' => $return_values['result'],
            'properties' => json_encode($request),
            'subject_id' => $this->last_id
        ]);

        return $return_values;
    }

    /**
     * update user group service
     *
     * @param array $request
     * @param UserGroup $userGroup
     * @return array
     */
    public function updateUserGroup(array $request, UserGroup $userGroup): array
    {
        try {
            DB::beginTransaction();

            $userGroup = tap($userGroup)->update($request);

            $this->last_id = $userGroup->getKey();

            $this->userGroupPermissionService->userGroupPermissionUpdateByUserGroup($request, $userGroup);

            $return_values = ['result' => 'success', 'message' => 'Successfully updated!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values =  ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        //activity log
        $this->activityLogService->createActivityLog([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'update',
            'status' => $return_values['result'],
            'properties' => json_encode($request),
            'subject_id' => $this->last_id
        ]);

        return $return_values;
    }

    /**
     * delete user group service
     *
     * @param UserGroup $userGroup
     * @return array
     */
    public function deleteUserGroup(UserGroup $userGroup): array
    {
        try {
            DB::beginTransaction();

            $this->last_id = $userGroup->getKey();

            // delete user group permissions
            $this->userGroupPermissionService->deleteUserGroupPermissionByUserGroupId($userGroup->id);

            // delete user group
            $userGroup->delete();


            $return_values = ['result' => 'success', 'message' => 'Successfully deleted!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values =  ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        //activity log
        $this->activityLogService->createActivityLog([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'update',
            'status' => $return_values['result'],
            'properties' => '{"user_group_id":' . $this->last_id . '}',
            'subject_id' => $this->last_id
        ]);

        return $return_values;
    }

    /**
     * show user group service
     *
     * @param UserGroup $userGroup
     * @return UserGroup
     */
    public function showUserGroup(UserGroup $userGroup): UserGroup
    {
        return $userGroup;
    }

    /**
     * index of paginate user groups service
     *
     * @return Paginator
     */
    public function indexUserGroupPaginate(array $request): Paginator
    {
        $user_groups = UserGroup::query();

        //set default values
        $per_page = (array_key_exists('per_page', $request) && $request['per_page'] != null) ? (int)$request['per_page'] : 5;
        $sort_by = (array_key_exists('sort_by', $request) && $request['sort_by'] != null) ? $request['sort_by'] : 'id';
        $sort = 'desc';
        if (array_key_exists('is_sort_desc', $request) && $request['is_sort_desc'] != null) {
            $sort = ($request['is_sort_desc'] == 'true') ? 'desc' : 'asc';
        }

        // search filter
        if (array_key_exists('search', $request) && !empty($request['search'])) {
            $user_groups->where(function ($query) use ($request) {
                $query->where('name', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('department', 'LIKE', '%' . $request['search'] . '%')
                    ->orWhere('description', 'LIKE', '%' . $request['search'] . '%');
            });
        }

        // property filter
        if (array_key_exists('property_id', $request) && !empty($request['property_id'])) {
            $user_groups->where(function ($query) use ($request) {
                $query->where('property_id', $request['property_id']);
            });
        }

        return $user_groups->orderBy($sort_by, $sort)->paginate($per_page);
    }
}
