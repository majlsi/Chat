<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Helpers\SecurityHelper;
use Illuminate\Http\Request;
use Models\Role;
use Services\ModuleService;
use Services\RoleRightService;
use Services\RoleService;
use Services\UserService;
use Validator;
use Lang;

class RoleController extends Controller
{

    private $roleService;
    private $userService;
    private $roleRightService;
    private $securityHelper;
    private $moduleService;

    public function __construct(RoleService $roleService, UserService $userService, RoleRightService $roleRightService, SecurityHelper $securityHelper, ModuleService $moduleService)
    {
        $this->roleService = $roleService;
        $this->userService = $userService;
        $this->roleRightService = $roleRightService;
        $this->securityHelper = $securityHelper;
        $this->moduleService = $moduleService;
    }

    public function index()
    {
        return response()->json($this->roleService->getAll(), 200);
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        return response()->json($this->roleService->getById($id)->load('rights'), 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, Role::rules('save'));
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }
        $role = $this->roleService->create($data);
        return response()->json($role, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $data = $request->all();
        $validator = Validator::make($data, Role::rules('update', $id));
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }
        $role = $this->roleService->update($id, $data);
        return response()->json(['message' => Lang::get('validation.custom.update.success',[],'en')], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        $role = $this->roleService->getById($id);
        $role->rights()->delete();
        $this->roleService->delete($id);
    }

    public function getPagedList(Request $request)
    {
        $data = (object)$request->all();
        return response()->json($this->roleService->filterRoles($data), 200);
    }

    public function getRoleRights()
    {
        $user = $this->securityHelper->getCurrentUser();
        $roleId = $user->role_id;
        $roleRights = $this->moduleService->getRoleRights($roleId);

        return response()->json($roleRights, 200);
    }

    public function CanAccess($rightId)
    {
        $user = $this->securityHelper->getCurrentUser();
        $roleId = $user->role_id;
        $canAccess = $this->roleRightService->canAccess($roleId, $rightId);
        if (count($canAccess) > 0) {
            return response()->json(['canAccess' => 1], 200);
        } else {
            return response()->json(['canAccess' => 0], 200);
        }
    }

    public function getModulesRights()
    {
        return response()->json($this->moduleService->getAll()->load('rights'), 200);
    }

}
