<?php

namespace Helpers;

use Tymon\JWTAuth\Facades\JWTAuth;
use Services\UserService;
use Illuminate\Support\Str;

class UserHelper
{

    private $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public static function prepareUserDataOnCreate($data,$provider)
    {
        $userData=[];
        if (isset($data['password'])) {
            $userData["password"]= $data["password"];
        }

        if(isset($data["oauth_uid"])){
            $userData["oauth_uid"]= $data["oauth_uid"];
        }

        if(isset($data["email"])){
            $userData["email"]= $data["email"];
            $userData["username"]= $data["email"];
        }

        if(isset($data["username"])){
            $userData["username"]= $data["username"];
        }

        if(isset($data["mobile"])){
            $userData["username"]= $data["mobile"];
        }
        if(isset($data["role_id"])){
            $userData["role_id"]= $data["role_id"];
        }

        if(isset($data["app_id"])){
            $userData["app_id"]= $data["app_id"];
        }

       
        $userData["is_verified"]= 0;
        $userData['oauth_provider'] =$provider;

        return $userData;
    }

    public static function prepareUserDataOnUpdate($data,$userRole)
    {
        $userData=[]; 
       
        $userData["role_id"]= $userRole;

        if (isset($data['email'])) {
            $userData["email"]= $data["email"];
        }

        if (isset($data['name'])) {
            $userData["name"]= $data["name"];
        }

        return $userData;
    }
    
}
