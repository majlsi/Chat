<?php

namespace Helpers;


use Services\UserService;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class SecurityHelper
{

    private $userService;


    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public static function getHashedPassword($password)
    {
        $hashedPassword= hash('sha256', $password, FALSE);
        return $hashedPassword;
    }

    public static function check($value, $hashedValue)
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        return (hash('sha256', $value) === $hashedValue);
    }


    public function getCurrentUser()
    {
        try {
            $payload = JWTAuth::parseToken()->getPayload();
            if (!$payload->get('user_id')) {
                return null;
            }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $ex) {
            return null;
        }
        $user = $this->userService->getById($payload->get('user_id'));

        if ($user->role_id == config('roles.admin')) {
            return $this->userService->getById($payload->get('user_id'));        
        } elseif ($user->role_id == config('roles.client')) {
            $account=$this->userService->getById($payload->get('user_id'));
            $account->role_id=$user->role_id;
            return $account;
        }

    }
}