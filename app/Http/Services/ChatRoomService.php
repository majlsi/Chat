<?php

namespace Services;

use Repositories\ChatRoomRepository;
use Repositories\UserRepository;
use Repositories\ChatRoomUserRepository;
use Illuminate\Database\DatabaseManager;
use \Illuminate\Database\Eloquent\Model;
use Helpers\ChatEventHelper;

class ChatRoomService extends BaseService
{
    private $userRepository;
    private $chatRoomUserRepository;
    private $chatEventHelper;

    public function __construct(DatabaseManager $database, ChatRoomRepository $repository,UserRepository $userRepository,
    ChatRoomUserRepository $chatRoomUserRepository,
    ChatEventHelper $chatEventHelper)
    {
        $this->setDatabase($database);
        $this->setRepository($repository);
        $this->userRepository = $userRepository;
        $this->chatRoomUserRepository = $chatRoomUserRepository;
        $this->chatEventHelper =  $chatEventHelper;
    }

    public function prepareCreate(array $data)
    {
        return $this->repository->create($data);
    }

    public function prepareUpdate(Model $model, array $data)
    {
        if (isset($data['users_ids'])) {
            // delete old users
            $chatRoom = $this->repository->find($model->id);
            $this->chatRoomUserRepository->deleteChatRoomUsers($model->id);
            $this->joinUsersToRoom($chatRoom,$data['users_ids']);
            unset($data['users_ids']);
        }
        return $this->repository->update($data, $model->id);
    }

    public function prepareDelete(int $id)
    {
        return $this->repository->delete($id);
    }

    public function findBy($attribute, $value, $columns = array('*'))
    {
        return $this->repository->findByField($attribute, $value, $columns)->first();
    }

    public function checkUsers($usersIds){
        foreach($usersIds as $userId){
           $user = $this->userRepository->find($userId);
           if(!$user){
               return false;
           }
        }
        return true;
    }

    public function createRoom($data){
        $this->openTransaction();
        try{
            $chatRoom = $this->repository->getChatRoomByAppId($data['chat_room_name'],$data['app_id']);
            if(!$chatRoom){
                $chatRoom =  $this->repository->create($data);
            }
            $this->joinUsersToRoom($chatRoom,$data['users_ids']);
        }catch(Exception $e) {
            $this->rollBack();

            throw $e;
        }
        $this->closeTransaction();
        if($chatRoom){
            return $chatRoom;
        }else{
            return false;
        }
    }

    public function checkChatRoomAssignToAdmin($chatRoomId) {
        $chatRoom = $this->repository->find($chatRoomId);
        if($chatRoom){
            $chatRoomUsers = $chatRoom->users->where('role_id',config('roles.admin'));
            if(count($chatRoomUsers)){
                return false;
            }
            return true;
        }
    }

    public function joinUsersToRoom($chatRoom,$usersIds){
        $checkUser = false;
        foreach($usersIds as $userId){
            $chatRoomUser = $this->chatRoomUserRepository->findWhere([
                ['chat_room_id','=',$chatRoom->id],
                ['user_id','=',$userId],

            ]);

            if(count($chatRoomUser) == 0){
                $chatRoom->users()->attach($userId);
                $checkUser = true;
            }
        }
        return $checkUser;
    }

    public function getChatRoomByAppId($chatRoomName,$appId){
        return $this->repository->getChatRoomByAppId($chatRoomName,$appId);
    }

    public function getChatRoomById($chatRoomId,$appId){
        return $this->repository->getChatRoomById($chatRoomId,$appId);
    }

    public function filteredChatRooms($filter)
    {
        $sortFlag = true;
        if (isset($filter->SearchObject)) {
            $params = (object) $filter->SearchObject;
        } else {
            $params = new stdClass();
        }

        if (!property_exists($filter, "SortBy")) {
            $filter->SortBy = "chat_rooms.created_at";
            $sortFlag = false;
        } else if ($filter->SortBy == 'id') {
            $filter->SortBy = "chat_rooms.id";
            $sortFlag = false;
        }
        if (!property_exists($filter, "SortDirection")) {
            $filter->SortDirection = "DESC";
            $sortFlag = false;
        }
        return $this->repository->filterChatRooms($filter->PageNumber, $filter->PageSize, $params, $filter->SortBy, $filter->SortDirection, $sortFlag);

    }

    public function checkUserExistInChatRoom($chatRoom,$userId){
      $chatRoomUsers =  $chatRoom->users->where('id',$userId);
      if(count($chatRoomUsers)){
          return true;
      }
      return false;
    }


    public function fireJoinChatRoomEvent($chatRoom,$creatorUserName,$orderId){
        $eventClassName = 'App\Events\JoinChatRoomEvent';
        $firingData = [
            'message_text' => 'New chat request from '.$creatorUserName,
            'creatorUserName' => $creatorUserName,
            'chatRoom' => $chatRoom,
            'orderId' => $orderId
        ];
        $this->chatEventHelper->fireJoinChatRoomEvent($firingData,$eventClassName);

    }

    public function fireCloseChatRoomEvent($chatRoom){
        $eventClassName = 'App\Events\CloseChatRoomEvent';
        $firingData = [ 
            'chatRoom' => $chatRoom
        ];
        $this->chatEventHelper->fireCloseChatRoomEvent($firingData,$eventClassName);

    }

}