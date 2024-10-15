<?php

namespace Repositories;

/**
 * Description of ChatRoomMessageRepository
 *
 * @author Heba
 */
class ChatRoomMessageRepository extends BaseRepository {

    /**
     * Determine the model of the repository
     *
     */
    public function model() {
        return 'Models\ChatRoomMessage';
    }

    public function filterChats($pageNumber, $pageSize, $searchObj, $sortBy, $sortDirection , $lastElementId){
        $query = $this->getAllChatsQuery($searchObj);
        return $this->getResults($pageNumber,  $pageSize,  $query, $sortBy, $sortDirection,$lastElementId);
    }

     public function getResults($pageNumber, $pageSize,$query = null, $sortBy = "id", $sortDirection = "ASC",$lastElementId)
    {
        //dd($lastElementId);
        //Sort
        $query = $query->orderBy($sortBy, $sortDirection);
        //Pagination
        $count = count($query->get());

        if($lastElementId){
        
            if(strtoupper($sortDirection) == 'ASC'){
                $query =  $query->where('id','>',$lastElementId)->take($pageSize);
            }elseif(strtoupper($sortDirection) == 'DESC'){
                $query =  $query->where('id','<',$lastElementId)->take($pageSize);

            }
        }else{
            $skip  = ($pageNumber - 1) * $pageSize;
            $query =  $query->skip($skip)->take($pageSize);
        }

        $args = array("TotalRecords" => $count, "Results" => $query->get());
        return (object)$args;
    } 

    public function getAllChatsQuery($searchObj){
        
        if (isset($searchObj->sender_user_id)) {
            $this->model = $this->model->where('sender_user_id', '=', $searchObj->sender_user_id );
        }
        if (isset($searchObj->chat_room_id)) {
            $this->model = $this->model->where('chat_room_id', '=', $searchObj->chat_room_id );
        }
        if (isset($searchObj->chat_status_id)) {
            $this->model = $this->model->where('chat_status_id', '=', $searchObj->chat_status_id );
        }
        if (isset($searchObj->message_text)) {
            $this->model = $this->model->whereRaw("(message_text like ?  )",array('%' . $searchObj->message_text . '%'));
        }

       
        return $this->model;
    }


}
