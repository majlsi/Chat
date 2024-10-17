<?php

namespace Services;

use Helpers\SecurityHelper;
use Illuminate\Database\DatabaseManager;
use Repositories\UserRepository;
use \Illuminate\Database\Eloquent\Model;
use Repositories\Criterias\UserCriteria;

class UserService extends BaseService
{

    public function __construct(DatabaseManager $database, UserRepository $repository)
    {
        $this->setDatabase($database);
        $this->setRepository($repository);
    }

    public function prepareCreate(array $data)
    {
        if (isset($data['password'])) {
            $data["password"] = SecurityHelper::getHashedPassword($data["password"]);
        }
        
        $user = $this->repository->create($data);
        return $user;
    }

    public function prepareUpdate(Model $model, array $data)
    {
        $this->repository->update($data, $model->id);
    }

    public function prepareDelete(int $id)
    {
        $this->repository->delete($id);
    }

    public function login($username, $appId)
    {
        $user = $this->repository->getUserByUsernameAndAppId($username,$appId);
        if ($user) {
                return $user;
            
        } else {
            return null;
        }
    }

    public function filteredUsers($filter)
    {
        if (isset($filter->SearchObject)) {
            $params = (object) $filter->SearchObject;
        } else {
            $params = new stdClass();
        }
        $criteria = new UserCriteria($params);

        if (!property_exists($filter, "SortBy")) {
            $filter->SortBy = "id";
        }
        if (!property_exists($filter, "SortDirection")) {
            $filter->SortDirection = "ASC";
        }
      
        $withExpressions = array("role");

        return $this->repository->getPagedResults($filter->PageNumber, $filter->PageSize,$withExpressions,$criteria,$filter->SortBy,$filter->SortDirection);
    }

}
