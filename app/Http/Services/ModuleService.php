<?php

namespace Services;

use Repositories\ModuleRepository;
use Illuminate\Database\DatabaseManager;
use \Illuminate\Database\Eloquent\Model;


class ModuleService extends BaseService
{

    public function __construct(DatabaseManager $database, ModuleRepository $repository)
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

    public function getRoleRights($roleId)
    {
        return $this->repository->getRoleRights($roleId);
    }

}