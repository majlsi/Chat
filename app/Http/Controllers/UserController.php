<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Services\UserService;
use Validator;
use Models\User;
use Helpers\SecurityHelper;
use Lang;

class UserController extends Controller {

    private $userService, $securityHelper;

    public function __construct(UserService $userService, SecurityHelper $securityHelper) {
        $this->userService = $userService;
        $this->securityHelper = $securityHelper;
    }

    public function getPagedList(Request $request) {
        $filter = (object) ($request->all());
        return response()->json($this->userService->filteredUsers($filter),200);
    }


    public function show($id) {
        $user = $this->userService->getById($id);
        return response()->json($user ,200);
    }

        /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $validator = Validator::make($data, User::rules('save'));
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }
        $user = $this->userService->create($data);
        return response()->json($user, 200);
    }

    public function update(Request $request, $id) {
        $data = $request->all();
        $validator = Validator::make($data, User::rules('update', $id));
        if ($validator->fails()) {
            return response()->json([ "error" => $validator->errors()->all()], 400);
        }
        $updated = $this->userService->update($id, $data);
        if ($updated) {
            return response()->json(["message" => Lang::get('validation.custom.user.update',[],'en')], 200);
        }
    }

    public function destroy($id) {
        $deleted=$this->userService->delete($id);        
        if($deleted !=0){
            return response()->json(['message' => Lang::get('validation.custom.delete.success',[],'en')], 200);
        }
    }

}
