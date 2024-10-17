<?php

namespace Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'username', 'role_id','app_id' ,'oauth_provider', 'oauth_uid', 'is_verified',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function rules($action, $id = null)
    {
        switch ($action) {
            case 'save':
                return array(
                    'app_id' => 'required',
                    'username' => 'required_with:app_id',
                    'role_id' => 'required',
                );
            case 'update':
                return array(
                    'app_id' => 'required',
                    'role_id' => 'required',
                    'username' => 'required_with:app_id|unique_with:users,app_id,NULL,'.$id.',deleted_at,NULL',
                );

        }
    }


    public function getUsernameForPasswordReset()
    {
        return $this->username;
    }


    public function role()
    {
        return $this->belongsTo('Models\Role');
    }

    public function chatRooms()
    {
        return $this->belongsToMany('Models\ChatRoom', 'chat_room_user', 'user_id', 'chat_room_id');
    }

            /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        $customClaims = ['user_id' => $this->id];
        return $customClaims;
    }

}
