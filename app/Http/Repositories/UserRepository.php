<?php

namespace Repositories;


class UserRepository extends BaseRepository
{

    /**
     * Determine the model of the repository
     *
     */
    public function model()
    {
        return 'Models\User';
    }

    public function login($email,$password)
    {
        $user = $this->model->selectRaw("*")
            ->where('email', $email)
            ->where('password',$password)
            ->first();

        return $user;
    }

    public function getUserByEmail($email)
    {
        $user = $this->model->selectRaw("*")
                ->where('email', $email)->first();
        return $user;
    }


    public function getUserByUsername($username)
    {
        $user = $this->model->selectRaw("*")
                ->where('username', $username)->first();
        return $user;
    }

    public function getUserByUsernameAndAppId($username,$appId){
        $user = $this->model->selectRaw("*")
        ->where('username', $username)
        ->where('app_id', $appId)
        ->first();
        return $user;
    }

    public function getUsertByProviderAndUid($provider, $id)
    {
        return $this->model
            ->where('oauth_provider', $provider)
            ->where('oauth_uid', $id)->first();
       
    }
}