<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Services\ChatRoomMessageService;
use Services\UserService;
use Validator;
use Models\ChatRoomMessage;
use Helpers\ChatRoomMessageHelper;
use Services\ChatRoomService;
use Services\ChatRoomUserService;
use Lang;

class ChatRoomMessageController extends Controller {

    private $chatRoomMessageService, $chatRoomMessageHelper, $userService , $chatRoomService, $chatRoomUserService;

    public function __construct(ChatRoomMessageService $chatRoomMessageService,
                                ChatRoomMessageHelper $chatRoomMessageHelper,
                                UserService $userService ,
                                ChatRoomService $chatRoomService, ChatRoomUserService $chatRoomUserService) {
        $this->chatRoomMessageService = $chatRoomMessageService;
        $this->chatRoomMessageHelper = $chatRoomMessageHelper;
        $this->userService = $userService;
        $this->chatRoomService = $chatRoomService;
        $this->chatRoomUserService = $chatRoomUserService;
    }


    public function show($id) {
        $chatRoomMessage = $this->chatRoomMessageService->getById($id);
        return response()->json($chatRoomMessage ,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $senderUser = $this->userService->getById($data['sender_user_id']);

        $error = $this->validateChatMessage($data,$senderUser);
        if($error){
            return response()->json($error, 404);
        }
        $messageData = $this->chatRoomMessageHelper->prepareChatMessageDataOnCreate($data);
        $validator = Validator::make($messageData, ChatRoomMessage::rules('save'));
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }
        $chatMessage = $this->chatRoomMessageService->create($messageData);
        if($chatMessage){
            $this->chatRoomMessageService->fireChatEvent($chatMessage,$data,$senderUser->id);
            return response()->json(['message'=>  Lang::get('translation.chat_room_message.send',[],'en')], 200);
        }
  
        
    }

    public function update(Request $request, $id) {
        $data = $request->all();
        $validator = Validator::make($data, chatRoomMessage::rules('update', $id));
        if ($validator->fails()) {
            return response()->json([ "error" => $validator->errors()->all()], 400);
        }
        $updated = $this->chatRoomMessageService->update($id, $data);
        if ($updated) {
            return response()->json(["message" => [Lang::get('translation.chat_room_message.update',[],'en')]], 200);
        }
    }

    public function destroy($id) {
        $deleted=$this->chatRoomMessageService->delete($id);        
        if($deleted !=0){
            return response()->json(['message' => Lang::get('translation.chat_room_message.delete',[],'en')], 200);
        }
    }

    public function getPagedList(Request $request) {
        $filter = (object) ($request->all());
        $data = $this->chatRoomMessageService->filteredChats($filter);
        $data->chat_members_number = $this->chatRoomUserService->getChatRoomUsersNumber($filter->SearchObject['chat_room_id']);
        return response()->json($data,200);
    }

    public function getChatMessagesList(Request $request) {
        $filter = (object) ($request->all());
        return response()->json($this->chatRoomMessageService->getChatMessagesList($filter),200);
    }

   public function sendAttachmentMessage(Request $request){
        $data = $request->all();
        $senderUser = $this->userService->getById($data['sender_user_id']);
   
        $error = $this->validateChatMessage($data,$senderUser);
        if($error){
            return response()->json($error, 404);
        }
        $messageData = $this->chatRoomMessageHelper->prepareChatAttachmentDataOnCreate($data);
        $validator = Validator::make($messageData, ChatRoomMessage::rules('save-attachment'));
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }
        $chatMessage = $this->chatRoomMessageService->createChatRoomAttachment($messageData);
        if($chatMessage){
            $this->chatRoomMessageService->fireChatEvent($chatMessage,$data,$senderUser->id);
            return response()->json(['message'=> Lang::get('translation.chat_room_message.send-attachment',[],'en')], 200);
        }
   }
   
   private function validateChatMessage($data,$senderUser){
        if(!$senderUser){
            return ['error' => Lang::get('validation.custom.user.not-found',[],'en')];
        }
        $chatRoom = $this->chatRoomService->getById($data['chat_room_id']);
        
        if($chatRoom->is_closed){
            return ['error' => Lang::get('validation.custom.chat-room.closed',[],'en'),
            'error_ar' => Lang::get('validation.custom.chat-room.closed',[],'ar')]; 
        }
        $checkUserExist = $this->chatRoomService->checkUserExistInChatRoom($chatRoom,$data['sender_user_id']);
        if(!$checkUserExist){
            return ['error' => Lang::get('validation.custom.user.not-exist',[],'en')];
        }
        return null;
   }

}
