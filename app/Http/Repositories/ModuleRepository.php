<?php

namespace Repositories;


class ModuleRepository extends BaseRepository
{

    /**
     * Determine the model of the repository
     *
     */
    public function model()
    {
        return 'Models\Module';
    }

    public function getRoleRights($roleId)
    {
        return $this->model->select('module_name as title','id')->with(array('submenu' => function ($query) use ($roleId) {
            $query->join('role_rights', 'rights.id', '=', 'role_rights.right_id')
                ->where('rights.in_menu', '=', 1)
                ->where('role_rights.role_id', '=', $roleId)
                ->select('rights.*','rights.right_url as page' ,'rights.right_name as title','rights.right_name_ar as title_ar')
                ->orderby('rights.right_order_number', 'asc');
        }))->get();
    }

}