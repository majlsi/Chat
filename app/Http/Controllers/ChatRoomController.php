<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Services\ChatRoomService;
use Services\AttachmentService;
use Services\ChatRoomUserService;
use Validator;
use Models\ChatRoom;
use Helpers\SecurityHelper;
use Lang;

class ChatRoomController extends Controller {

    private $chatRoomService, $securityHelper, $attachmentService, $chatRoomUserService;

    public function __construct(ChatRoomService $chatRoomService, SecurityHelper $securityHelper,
        AttachmentService $attachmentService, ChatRoomUserService $chatRoomUserService) {
        $this->chatRoomService = $chatRoomService;
        $this->securityHelper = $securityHelper;
        $this->attachmentService = $attachmentService;
        $this->chatRoomUserService = $chatRoomUserService;
    }


    public function show($id) {
        $chatRoom = $this->chatRoomService->getById($id)->load('users');
        return response()->json($chatRoom ,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $user = $this->securityHelper->getCurrentUser();
        $data['creator_user_id'] = $user->id;
        $creatorUserName = $data['creator_user_name'];
        
        $orderId = null;
        $isFireEvent = true;
        if(isset($data['order_id'])){
            $orderId = $data['order_id'];
        }
        $validator = Validator::make($data, ChatRoom::rules('save'));
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }
        /** joinning users into created chat room*/
        if(isset($data['users_ids'])){
            /** check if users ids already exist or not */
            $usersExist = $this->chatRoomService->checkUsers($data['users_ids']);
            if($usersExist){
                   /** create chat room */
                $chatRoom = $this->chatRoomService->createRoom($data);
                if(count($chatRoom->users->toArray()) > 1) {
                    $isFireEvent = false;
                }
            }else{
                return response()->json(['error' => Lang::get('validation.custom.users-ids.not-exist',[],'en')], 404);
            }
           
        }else{
             /** create chat room */
             $chatRoom = $this->chatRoomService->getChatRoomByAppId($data['chat_room_name'],$data['app_id']);
             if(!$chatRoom){
                $chatRoom = $this->chatRoomService->create($data);
                if(count($chatRoom->users->toArray()) > 1) {
                    $isFireEvent = false;
                }
             }
             
        }
        if($chatRoom && $isFireEvent){
            $this->chatRoomService->fireJoinChatRoomEvent($chatRoom,$creatorUserName,$orderId);
        }
        if($chatRoom){
            $redisConfig = config('redis');
          
            return response()->json(compact('chatRoom','redisConfig'), 200);
        }else{
            return response()->json(['error' => Lang::get('validation.custom.chat-room.create-error',[],'en')], 400);
        }
    }

    public function update(Request $request, $id) {
        $data = $request->all();
        $validator = Validator::make($data, ChatRoom::rules('update', $id));
        if ($validator->fails()) {
            return response()->json([ "error" => $validator->errors()->all()], 400);
        }
        /** joinning users into update chat room*/
        if(isset($data['users_ids'])){
            /** check if users ids already exist or not */
            $usersExist = $this->chatRoomService->checkUsers($data['users_ids']);
            if(!$usersExist){
                return response()->json(['error' => Lang::get('validation.custom.users-ids.not-exist',[],'en')], 404);
            }
        }
        $updated = $this->chatRoomService->update($id, $data);
        if ($updated) {
            return response()->json(["message" => [Lang::get('validation.custom.user.update',[],'en')]], 200);
        }
    }

    public function destroy($id) {
        $deleted=$this->chatRoomService->delete($id);        
        if($deleted !=0){
            return response()->json(['message' => Lang::get('validation.custom.delete.success',[],'en')], 200);
        }
    }

    public function addUsersToChatRoom(Request $request,int $roomId){
        $data = $request->all();
        $chatRoom=$this->chatRoomService->getChatRoomById($roomId,$data['app_id']);
        if(!$chatRoom){
            return response()->json([ "error" => Lang::get('validation.custom.chat-room.not-found',[],'en')], 404);
        }  
        $chatRoomUsers = $this->chatRoomService->checkChatRoomAssignToAdmin($roomId);
        if(!$chatRoomUsers){
            return response()->json([ "error" => Lang::get('validation.custom.chat-room.assign-error',[],'en')], 400);
        }
        /** check if users ids already exist or not */
        $usersExist = $this->chatRoomService->checkUsers($data['users_ids']);
        if($usersExist){
               /** join users to chat room */
            $usersJoin = $this->chatRoomService->joinUsersToRoom($chatRoom,$data['users_ids']);
            if(!$usersJoin){
                return response()->json(["error" => Lang::get('validation.custom.chat-room.add-users-error',[],'en')], 400);
            }
            return response()->json(["message" => Lang::get('validation.custom.chat-room.add-users-success',[],'en')], 200);
        }else{
            return response()->json(['error' => Lang::get('validation.custom.users-ids.not-exist',[],'en')], 404);
        }  
    }


    public function closeChatRoom(Request $request,int $roomId){
        $chatRoom=$this->chatRoomService->getById($roomId);
        if(!$chatRoom){
            return response()->json([ "error" => Lang::get('validation.custom.chat-room.not-found',[],'en')], 404);
        }  
        $user = $this->securityHelper->getCurrentUser();
        if($user->role_id == config('roles.client')){
            return response()->json(["error" => Lang::get('validation.custom.chat-room.no-access',[],'en')], 400);
        }
        $chatRoomUpdated = $this->chatRoomService->update($roomId, ['is_closed' => !$chatRoom->is_closed]);
        if ($chatRoomUpdated) {
            $this->chatRoomService->fireCloseChatRoomEvent($chatRoom);
            if(!$chatRoom->is_closed){
                return response()->json(["message" => Lang::get('validation.custom.chat-room.close-success',[],'en')], 200);
            }else{
                return response()->json(["message" => Lang::get('validation.custom.chat-room.reopen-success',[],'en')], 200);
            }
        }
        


    }

    public function getPagedList(Request $request) {
        $filter = (object) ($request->all());
        return response()->json($this->chatRoomService->filteredChatRooms($filter),200);
    }

    public function checkUserExistInChatRoom(Request $request,int $roomId){
        $data = $request->all();
        $userId = $data['chat_user_id'];
        $chatRoom = $this->chatRoomService->getById($roomId);
        if(!$chatRoom){
            return response()->json([ "error" => Lang::get('validation.custom.chat-room.not-found',[],'en')], 404);
        }
        $isUserExist = $this->chatRoomService->checkUserExistInChatRoom($chatRoom,$userId);
        if($isUserExist){
            return response()->json([ "message" => Lang::get('validation.custom.user.exist',[],'en')], 200);

        }
        return response()->json([ "error" => Lang::get('validation.custom.user.not-exist',[],'en')], 400);
    }

    public function getChatRoomAttachments(Request $request,int $chatRoomId){
        $filter = (object) ($request->all());
        $chatRoomAttachments = $this->attachmentService->filteredChatRoomAttachments($filter,$chatRoomId);
        $chatRoomAttachments->file_base_url = config('attachment.image_base_url');
        return response()->json($chatRoomAttachments, 200);
    }

    public function deleteUserFromChatRoom($chatRoomId,$userId){
        $chatRoomUser = $this->chatRoomUserService->getChatRoomUserByUserIdAndChatRoomId($chatRoomId,$userId);
        if($chatRoomUser) {
            $this->chatRoomUserService->delete($chatRoomUser->id);
            return response()->json([ "message" => Lang::get('translation.chat-room-users.delete.success',[],'en')], 200);
        }
        return response()->json([ "error" => Lang::get('translation.chat-room-users.delete.error',[],'en')], 400);
    }
}
