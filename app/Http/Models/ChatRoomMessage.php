<?php

namespace Models;

use \Illuminate\Database\Eloquent\Model;

/**
 * Description of ImageModel
 *
 * @author Heba
 */
class ChatRoomMessage extends Model {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['chat_room_id','sender_user_id','chat_status_id','message_text','message_date',
                    'attachment_id','message_type_id'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at'];
    protected $table = 'chat_messages';

    public static function rules($action, $id = null) {
        switch ($action) {
            case 'save':
                return array(
                    'chat_room_id' => 'required',
                    'sender_user_id' => 'required',
                    'chat_status_id' => 'sometimes',
                    'message_text' => 'required',
                    'message_date' => 'required'
                    
                );
            case 'update':
                return array(
                    'chat_room_id' => 'required',
                    'sender_user_id' => 'required',
                    'chat_status_id' => 'sometimes',
                    'message_text' => 'required',
                    'message_date' => 'required'
                );
            case 'save-attachment': 
                return array(
                    'chat_room_id' => 'required',
                    'sender_user_id' => 'required',
                    'chat_status_id' => 'sometimes',
                    'attachment' => 'required',
                    'message_date' => 'required'  
                );
        }
    }

    public function attachment(){
        return $this->belongsTo('Models\Attachment');
    }

}
