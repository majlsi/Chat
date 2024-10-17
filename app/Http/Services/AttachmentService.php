<?php

namespace Services;

use Repositories\AttachmentRepository;
use Illuminate\Database\DatabaseManager;
use \Illuminate\Database\Eloquent\Model;


class AttachmentService extends BaseService
{

    public function __construct(DatabaseManager $database, AttachmentRepository $repository)
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

    public function filteredChatRoomAttachments($filter,$chatRoomId){
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
        return $this->repository->filteredChatRoomAttachments($filter->PageNumber, $filter->PageSize, $params, $filter->SortBy, $filter->SortDirection,$chatRoomId);
    }
}