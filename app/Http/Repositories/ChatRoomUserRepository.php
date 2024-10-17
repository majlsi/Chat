<?php

namespace Repositories;

/**
 * Description of ChatRoomRepository
 *
 * @author Heba
 */
class ChatRoomUserRepository extends BaseRepository {

    /**
     * Determine the model of the repository
     *
     */
    public function model() {
        return 'Models\ChatRoomUser';
    }

    public function deleteChatRoomUsers($chatRoomId) {
        $this->model->where('chat_room_id',$chatRoomId)->delete();
    }

    public function getChatRoomUsersNumber($chatRoomId){
        return $this->model->where('chat_room_id',$chatRoomId)->count();
    }

    public function getChatRoomUserByUserIdAndChatRoomId($chatRoomId,$userId){
        return $this->model->where('chat_room_id',$chatRoomId)->where('user_id',$userId)->first();
    }
}
