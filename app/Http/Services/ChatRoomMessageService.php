<?php

namespace Services;

use Repositories\ChatRoomMessageRepository;
use Repositories\ChatRoomRepository;
use Repositories\AttachmentRepository;
use Illuminate\Database\DatabaseManager;
use \Illuminate\Database\Eloquent\Model;
use Helpers\ChatEventHelper;
use Carbon\Carbon;
class ChatRoomMessageService extends BaseService
{
    private $chatEventHelper;
    private $chatRoomRepository;

    public function __construct(DatabaseManager $database, ChatRoomMessageRepository $repository,ChatEventHelper $chatEventHelper,ChatRoomRepository $chatRoomRepository,
            AttachmentRepository $attachmentRepository)
    {
        $this->setDatabase($database);
        $this->setRepository($repository);
        $this->chatEventHelper = $chatEventHelper;
        $this->chatRoomRepository = $chatRoomRepository;
        $this->attachmentRepository = $attachmentRepository;
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

    public function fireChatEvent($chatMessageDetails,$data,$chatUserId){
        $eventClassName = 'App\Events\ChatEvent';
        $senderUser = $data['sender_user'];
        $firingData = [
            'message_text' => $chatMessageDetails->message_text,
            'message_type_id' => $chatMessageDetails->message_type_id,
            'attachment' => $chatMessageDetails->attachment,
            'message_date' => $chatMessageDetails->message_date,
            'sender_user' => $senderUser,
            'id' => $chatMessageDetails->chat_room_id,
            'chat_room_id' => $chatMessageDetails->chat_room_id,
            'sender_user_id' => $chatUserId,
            'first_char_name' => !preg_match('/[^A-Za-z0-9]/', $senderUser['name'])?  strtoupper($senderUser['name'][0]) : mb_substr($senderUser['name'], 0, 1,'utf8'),
            'message_time' => Carbon::Parse($chatMessageDetails->message_date)->format('d M Y g:i A'),
            'image_url' => $senderUser['image_url'] ?? '',
            'username' => $senderUser['name'],
            'chat_room_name'=>$data['chat_room_name'],
            'meeting_id'=>$data['meeting_id'],
            'committee_id'=>$data['committee_id'],
            'is_committee'=>$data['is_committee'],
            'is_general_chat'=> isset($data['is_general_chat'])? $data['is_general_chat'] : false,
            'chat_group_id'=> isset($data['chat_group_id'])? $data['chat_group_id'] : null,
            'chat_name'=>$data['chat_name'],
            'chat_name_ar'=>$data['chat_name_ar'],
        ];

        $chatRoom = $this->chatRoomRepository->find($chatMessageDetails->chat_room_id);
        $channelName =  $chatRoom->chat_room_name;
        $this->chatEventHelper->fireEvent($channelName,$firingData,$eventClassName);
        //dd($firingData);

    }

    public function filteredChats($filter)
    {
        if (isset($filter->SearchObject)) {
            $params = (object) $filter->SearchObject;
        } else {
            $params = new stdClass();
        }

        if (!property_exists($filter, "SortBy")) {
            $filter->SortBy = "message_date";
        }
        if (!property_exists($filter, "SortDirection")) {
            $filter->SortDirection = "DESC";
        }
        if (!property_exists($filter, "lastElementId")) {
            $filter->lastElementId = null;
        }
        
        return $this->repository->filterChats($filter->PageNumber, $filter->PageSize, $params, $filter->SortBy, $filter->SortDirection,$filter->lastElementId);

    }

    public function createChatRoomAttachment(array $data){
        $attachment = $this->attachmentRepository->create($data['attachment']);
        $data['attachment_id'] = $attachment->id;
        unset($data['attachment']);
        return $this->repository->create($data);
    }
  

}