<?php

namespace App\Services;

use App\Helpers\Helper;
use App\Models\Permission;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PermissionService extends ActivityLogService
{
    public $last_id = null;
    public $module_name = 'permissions';

    /**
     * index of permission service
     *
     * @return void
     */
    public function indexPermission(): Collection
    {
        $properties = Permission::all();

        return $properties;
    }

    /**
     * create permission
     *
     * @param array $request
     * @return array
     */
    public function createPermission(array $request): array
    {
        try {
            DB::beginTransaction();

            $permission = Permission::create($request);

            $this->last_id = $permission->getKey();

            $return_values = ['result' => 'success', 'message' => 'Successfully saved!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values =  ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        //activity log
        $this->createActivityLog([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'create',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => json_encode($request),
            'causer_id' => Auth::user()->id,
            'subject_id' => $this->last_id
        ]);

        return $return_values;
    }

    /**
     * updated permision
     *
     * @param array $request
     * @param Permission $permission
     * @return array
     */
    public function updatePermission(array $request, Permission $permission): array
    {
        try {
            DB::beginTransaction();

            $permission = tap($permission)->update($request);

            $this->last_id = $permission->getKey();

            $return_values = ['result' => 'success', 'message' => 'Successfully updated!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values =  ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }
        DB::commit();

        //activity log
        $this->createActivityLog([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'update',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => json_encode($request),
            'causer_id' => Auth::user()->id,
            'subject_id' => $this->last_id
        ]);

        return $return_values;
    }

    /**
     * delete permission
     *
     * @param Permission $permission
     * @return array
     */
    public function deletePermission(Permission $permission): array
    {
        try {
            DB::beginTransaction();

            $this->last_id = $permission->getKey();

            $permission->delete();

            $return_values = ['result' => 'success', 'message' => 'Successfully deleted!', 'subject' => $this->last_id];
        } catch (Exception $e) {
            DB::rollBack();

            $return_values =  ['result' => 'error', 'message' => $e->getMessage(), 'subject' => $this->last_id];
        }

        DB::commit();

        //activity log
        $this->createActivityLog([
            'name' => $this->module_name,
            'description' => $return_values['message'],
            'event' => 'delete',
            'status' => $return_values['result'],
            'browser' => json_encode(Helper::deviceInfo()),
            'properties' => '{"permission_id":' . $this->last_id . '}',
            'causer_id' => Auth::user()->id,
            'subject_id' => $this->last_id
        ]);

        return $return_values;
    }

    /**
     * Get the list of modules
     *
     * @return array
     */
    public function modulePermissions(): array
    {
        $modules = Permission::select('module')
            ->distinct('module')
            ->get()
            ->toArray();

        return $modules;
    }
}
