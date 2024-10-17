<?php

namespace Repositories;

/**
 * Description of AttachmentRepository
 *
 * @author Ghada
 */
class AttachmentRepository extends BaseRepository {

    /**
     * Determine the model of the repository
     *
     */
    public function model() {
        return 'Models\Attachment';
    }

    public function filteredChatRoomAttachments($pageNumber, $pageSize, $searchObj, $sortBy, $sortDirection,$chatRoomId){
        $query = $this->getAllChatRoomAttachmentsQuery($searchObj, $chatRoomId);
        return $this->getPagedQueryResults($pageNumber,  $pageSize,  $query, $sortBy, $sortDirection);
    }

    public function getAllChatRoomAttachmentsQuery($searchObj,$chatRoomId){

        if (isset($searchObj->attachemnt_name)) {
            $this->model = $this->model->whereRaw("(attachemnt_name like ?  )",array('%' . $searchObj->attachemnt_name . '%'));
        }
        return $this->model->selectRaw('attachments.*,chat_messages.message_date')
            ->join('chat_messages','chat_messages.attachment_id','attachments.id')
            ->where('chat_messages.chat_room_id',$chatRoomId);
    }
}
