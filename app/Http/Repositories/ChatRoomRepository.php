<?php

namespace Repositories;

/**
 * Description of ChatRoomRepository
 *
 * @author Heba
 */
class ChatRoomRepository extends BaseRepository {

    /**
     * Determine the model of the repository
     *
     */
    public function model() {
        return 'Models\ChatRoom';
    }

    public function getChatRoomByAppId($chatRoomName,$appId){
        return $this->model
        ->where('chat_room_name', '=', $chatRoomName)
        ->where('app_id', '=', $appId)->first();
    }
    public function getChatRoomById($chatRoomId,$appId){
        return $this->model
        ->where('id', '=', $chatRoomId)
        ->where('app_id', '=', $appId)->first();
    }
    public function filterChatRooms($pageNumber, $pageSize, $searchObj, $sortBy, $sortDirection, $sortFlag){
        $query = $this->getAllChatRoomsQuery($searchObj, $sortFlag);
        return $this->getPagedQueryResults($pageNumber,  $pageSize,  $query, $sortBy, $sortDirection);
    }

    public function getAllChatRoomsQuery($searchObj, $sortFlag){
        $this->model = $this->model->
        with(['users' => function ($query) {
            
            return $query->leftJoin('chat_rooms','chat_rooms.id','chat_room_user.chat_room_id')
            ->selectRaw("users.id as user_assigned_id")->whereRaw('chat_room_user.user_id != chat_rooms.creator_user_id');
            

        }]);
        if (isset($searchObj->creator_user_id)) {
            $this->model = $this->model->where('creator_user_id', '=', $searchObj->creator_user_id );
        }

        if (isset($searchObj->chat_room_id)) {
            $this->model = $this->model->where('chat_rooms.id', '=', $searchObj->chat_room_id );
        }
        if (isset($searchObj->app_id)) {
            $this->model = $this->model->where('app_id', '=', $searchObj->app_id );
        }
        if (isset($searchObj->chat_room_name)) {
            $this->model = $this->model->whereRaw("(chat_room_name like ?  )",array('%' . $searchObj->chat_room_name . '%'));
        }
        if (isset($searchObj->user_id)) {
            $this->model = $this->model
                            ->whereRaw('(chat_room_user.user_id = ? OR chat_rooms.creator_user_id = ? )',array($searchObj->user_id,$searchObj->user_id));
        }
        if (isset($searchObj->is_active)) {
            if($searchObj->is_active){
                $this->model = $this->model->where('is_closed', '=', 0 );
            }else{
                $this->model = $this->model->where('is_closed', '=', 1 );
            }
        }

        if (isset($searchObj->from_date)) {
            $this->model = $this->model->whereRaw("date(chat_rooms.created_at) >='" . $searchObj->from_date . "'");
        }
        if (isset($searchObj->to_date)) {
            $this->model = $this->model->whereRaw("date(chat_rooms.created_at) <='" . $searchObj->to_date . "'");
        }
        if($sortFlag == false){
            $this->model = $this->model->orderBy('chat_rooms.created_at','desc');
        }
       
        return $this->model->selectRaw('distinct chat_rooms.*,
        (SELECT message_text FROM chat_messages LEFT JOIN chat_rooms as chatRooms ON chatRooms.id = chat_messages.chat_room_id WHERE chat_messages.chat_room_id = chat_rooms.id ORDER BY chat_messages.created_at DESC LIMIT 1) as last_message_text,
        (SELECT DATE_FORMAT(message_date,"%H:%i %p") FROM chat_messages LEFT JOIN chat_rooms as chatRooms ON chatRooms.id = chat_messages.chat_room_id WHERE chat_messages.chat_room_id = chat_rooms.id ORDER BY chat_messages.created_at DESC LIMIT 1) as last_message_time,
        (SELECT message_date FROM chat_messages LEFT JOIN chat_rooms as chatRooms ON chatRooms.id = chat_messages.chat_room_id WHERE chat_messages.chat_room_id = chat_rooms.id ORDER BY chat_messages.created_at DESC LIMIT 1) as last_message_date')
        ->leftJoin('chat_room_user','chat_room_user.chat_room_id','chat_rooms.id')
        ->leftJoin('chat_messages','chat_messages.chat_room_id','chat_rooms.id');
    }
}
