<?php

namespace Helpers;

use Carbon\Carbon;
use Services\ChatRoomService;

class ChatRoomMessageHelper
{
    private $chatRoomService;


    public function __construct(ChatRoomService $chatRoomService)
    {
        $this->chatRoomService = $chatRoomService;
    }

    public function prepareChatMessageDataOnCreate($data)
    {
        $chatData=[];
        if(isset($data["sender_user_id"])){
            $chatData["sender_user_id"]= $data["sender_user_id"];
        }
        if(isset($data["message_text"])){
            $chatData["message_text"]= $data["message_text"];
        }
        if(isset($data["chat_room_id"])){
                $chatData["chat_room_id"] = $data['chat_room_id'];
        }

        $chatData['message_type_id'] = config('messageTypes.text');
        $chatData["message_date"] = Carbon::now()->addHours($data['diff_hours']);
        return $chatData;
    }

    public function prepareChatAttachmentDataOnCreate($data)
    {
        $chatData=[];
        if(isset($data["sender_user_id"])){
            $chatData["sender_user_id"]= $data["sender_user_id"];
        }
        if(isset($data["attachment"])){
            $chatData["attachment"]= $data["attachment"];
        }
        if(isset($data["chat_room_id"])){
            $chatData["chat_room_id"] = $data['chat_room_id'];
        }

        $chatData['message_type_id'] = config('messageTypes.attachment');
        $chatData["message_date"] = Carbon::now()->addHours($data['diff_hours']);
        return $chatData;
    }
}