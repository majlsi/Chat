<?php

namespace Services;

use Illuminate\Database\DatabaseManager;
use Repositories\RoleRepository;
use Services\RoleRightService;
use \Illuminate\Database\Eloquent\Model;
use Repositories\Criterias\RoleCriteria;

class RoleService extends BaseService
{

    private $roleRightService;

    public function __construct(DatabaseManager $database, RoleRepository $repository, RoleRightService $roleRightService)
    {
        $this->setDatabase($database);
        $this->setRepository($repository);
        $this->roleRightService = $roleRightService;
    }

    public function prepareCreate(array $data)
    {
        $role = $this->repository->create($data);
        if (isset($data["rights"])) {
            $role->rights()->createMany($data["rights"]);
        }
        return $role;
    }

    public function prepareUpdate(Model $model, array $data)
    {
        $rights=[];
        if (isset($data["rights"])) {
            $rights=$data["rights"];
            unset($data["rights"]);
        }
        $this->repository->update($data, $model->id);  
        if (count($rights) > 0) {
            $model->rights()->delete();
            $model->rights()->createMany($rights);
        }
    }

    public function prepareDelete(int $id)
    {
        return $this->repository->delete($id);
    }

    public function filterRoles($filter)
    {
        if (isset($filter->SearchObject)) {
            $params = (object) $filter->SearchObject;
        } else {
            $params = new stdClass();
        }
        $criteria = new RoleCriteria($params);

        if (!property_exists($filter, "SortBy")) {
            $filter->SortBy = "id";
        }
        if (!property_exists($filter, "SortDirection")) {
            $filter->SortDirection = "ASC";
        }
      
        $withExpressions = array();

        return $this->repository->getPagedResults($filter->PageNumber, $filter->PageSize,$withExpressions,$criteria,$filter->SortBy,$filter->SortDirection);
    }

    public function canAccess($roleId, $rightId)
    {
        return $this->repository->canAccess($roleId, $rightId);
    }
}
