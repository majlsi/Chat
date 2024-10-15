<?php

namespace Models;

use \Illuminate\Database\Eloquent\Model;

/**
 * Description of ImageModel
 *
 * @author Heba
 */
class ChatRoom extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['app_id','chat_room_name','is_closed','creator_user_id'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $table = 'chat_rooms';

    public static function rules($action, $id = null) {
        switch ($action) {
            case 'save':
                return array(
                    'app_id' => 'required',
                    'chat_room_name' => 'required',
                    'creator_user_id' => 'required'
                );
            case 'update':
                return array(
                    'app_id' => 'required',
                    'chat_room_name' => 'required',
                );
        }
    }

    public function users()
    {
        return $this->belongsToMany('Models\User', 'chat_room_user', 'chat_room_id', 'user_id');
    }


}
