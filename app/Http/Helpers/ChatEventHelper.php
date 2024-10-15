<?php

namespace Helpers;

use App\Events\ChatEvent;
use App\Events\JoinChatRoomEvent;
use App\Events\CloseChatRoomEvent;

class ChatEventHelper
{
    public function __construct()
    {
        
    }
    public static function fireEvent($channelName,$firingData,$eventClassName)
    {
        event(new $eventClassName($channelName,$firingData));
    }

    public static function fireJoinChatRoomEvent($firingData,$eventClassName)
    {
        event(new $eventClassName($firingData));
    }

    public static function fireCloseChatRoomEvent($firingData,$eventClassName)
    {
        event(new $eventClassName($firingData));
    }

 
}