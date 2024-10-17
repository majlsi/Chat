<?php

namespace Services;

use Repositories\ChatRoomUserRepository;
use Illuminate\Database\DatabaseManager;
use \Illuminate\Database\Eloquent\Model;


class ChatRoomUserService extends BaseService
{

    public function __construct(DatabaseManager $database, ChatRoomUserRepository $repository)
    {
        $this->setDatabase($database);
        $this->setRepository($repository);
    }

    public function prepareCreate(array $data)
    {
       return $this->repository->create($data);
    }

    public function prepareUpdate(Model $model, array $data)
    {
       return $this->repository->update($data, $model->id);
    }

    public function prepareDelete(int $id)
    {
        return $this->repository->delete($id);
    }

    public function getChatRoomUsersNumber($chatRoomId){
        return $this->repository->getChatRoomUsersNumber($chatRoomId);
    }

    public function getChatRoomUserByUserIdAndChatRoomId($chatRoomId,$userId){
        return $this->repository->getChatRoomUserByUserIdAndChatRoomId($chatRoomId,$userId);
    }
}