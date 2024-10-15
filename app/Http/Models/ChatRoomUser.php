<?php

namespace Models;

use \Illuminate\Database\Eloquent\Model;

/**
 * Description of ImageModel
 *
 * @author Heba
 */
class ChatRoomUser extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['chat_room_id','user_id'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $table = 'chat_room_user';

    public static function rules($action, $id = null) {
        switch ($action) {
            case 'save':
                return array(
                    'chat_room_id' => 'required',
                    'user_id' => 'required'
                    
                );
            case 'update':
                return array(
                    'chat_room_id' => 'required',
                    'user_id' => 'required'
                );
        }
    }


}
