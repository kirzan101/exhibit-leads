<?php

namespace App\Http\Controllers;

use App\Helpers\Helper;
use App\Http\Resources\UserGroupResource;
use App\Models\UserGroup;
use App\Services\GenericPaginateService;
use App\Services\PermissionService;
use App\Services\PropertyService;
use App\Services\UserGroupPermissionService;
use App\Services\UserGroupService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserGroupController extends Controller
{
    public $module = 'usergroups';
    private UserGroupService $userGroupService;
    private PropertyService $propertyService;
    private UserGroupPermissionService $userGroupPermissionService;
    private PermissionService $permissionService;

    public function __construct(
        UserGroupService $userGroupService,
        PropertyService $propertyService,
        UserGroupPermissionService $userGroupPermissionService,
        PermissionService $permissionService
    ) {
        $this->userGroupService = $userGroupService;
        $this->propertyService = $propertyService;
        $this->userGroupPermissionService = $userGroupPermissionService;
        $this->permissionService = $permissionService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('read', UserGroup::class);

        // $this->authorize('read', UserGroup::class);
        //set default value for lead name
        $sort_by = $request->sort_by;

        // set default to desc
        if ($request->is_sort_desc == null) {
            $request->merge(['is_sort_desc' => true]);
        }

        $user_groups = UserGroupResource::collection($this->userGroupService->indexUserGroupPaginate($request->toArray()));

        $properties = $this->propertyService->indexProperty();

        return Inertia::render('UserGroups/IndexPaginateUserGroup', [
            'sortBy' => $sort_by,
            'sortDesc' => filter_var($request->is_sort_desc, FILTER_VALIDATE_BOOLEAN),
            'search' => $request->search,
            'module' => $this->module,
            'items' => $user_groups,
            'properties' => $properties,
            'property_id' => $request->property_id
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', UserGroup::class);

        return Inertia::render('UserGroups/CreateUserGroup', [
            'properties' => $this->propertyService->indexProperty(),
            'permissions' => $this->permissionService->indexPermission(),
            'modules' => $this->permissionService->modulePermissions(),
            'codes' => Helper::userGroupCodes()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', UserGroup::class);

        ['result' => $result, 'message' => $message] = $this->userGroupService->createUserGroup($request->toArray());

        // return redirect()->back()->with($result, $message);
        return redirect()->route('usergroups.index')->with($result, $message);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $this->authorize('read', UserGroup::class);

        $userGroup = UserGroup::find($id);
        $user_group = new UserGroupResource($this->userGroupService->showUserGroup($userGroup));
        $permissions = $this->permissionService->indexPermission();
        $modules = $this->permissionService->modulePermissions();
        $codes = Helper::userGroupCodes();

        return Inertia::render('UserGroups/ShowUserGroup', [
            'user_group' => $user_group,
            'properties' => $this->propertyService->indexProperty(),
            'permissions' => $permissions,
            'modules' => $modules,
            'codes' => $codes,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $this->authorize('update', UserGroup::class);

        $userGroup = UserGroup::find($id);
        $user_group = new UserGroupResource($this->userGroupService->showUserGroup($userGroup));
        $permissions = $this->permissionService->indexPermission();
        $modules = $this->permissionService->modulePermissions();
        $codes = Helper::userGroupCodes();

        return Inertia::render('UserGroups/EditUserGroup', [
            'user_group' => $user_group,
            'properties' => $this->propertyService->indexProperty(),
            'permissions' => $permissions,
            'modules' => $modules,
            'codes' => $codes,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $this->authorize('update', UserGroup::class);

        $userGroup = UserGroup::find($id);

        ['result' => $result, 'message' => $message] = $this->userGroupService->updateUserGroup($request->toArray(), $userGroup);

        // return redirect()->back()->with($result, $message);
        return redirect()->route('usergroups.index')->with($result, $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->authorize('delete', UserGroup::class);

        $userGroup = UserGroup::findOrFail($id);

        ['result' => $result, 'message' => $message] = $this->userGroupService->deleteUserGroup($userGroup);

        return redirect()->route('usergroups.index')->with($result, $message);
    }
}
