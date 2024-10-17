<?php
/*
  Base Repositoy
 */

namespace Repositories;

use Prettus\Repository\Eloquent\BaseRepository as Repository;
use Prettus\Repository\Criteria\RequestCriteria as Criteria;

/**
 * Base to All Repositories that contains common functionalities
 *
 * @author yasser.mohamed
 */
abstract class BaseRepository extends Repository
{

    public function getPagedResults($pageNumber, $pageSize, $withExpressions = array(), Criteria $filter = null, $sortBy = "id", $sortDirection = "ASC", $columns = array('*'))
    {
       //Sort
        $this->model = $this->model->orderBy($sortBy, $sortDirection);
        //Criteria
        if (!is_null($filter)) {
            $this->pushCriteria($filter);
            $this->applyCriteria();
        }

        //Include the related entities
        if (!is_null($withExpressions)) {
            foreach ($withExpressions as $relation) {
                $this->model->with($relation);
            }
        }
        //Pagination
        $count = count($this->model->get($columns));
        $skip = ($pageNumber - 1) * $pageSize;
        $this->model->skip($skip)->take($pageSize);
        $args = array("TotalRecords" => $count, "Results" => $this->model->get($columns));
        return (object) $args;
    }

    public function getPagedQueryResults($pageNumber, $pageSize,$query = null, $sortBy = "id", $sortDirection = "ASC")
    {
        //Sort
        $query = $query->orderBy($sortBy, $sortDirection);
        //Pagination
        $count = count($query->get());
        $skip  = ($pageNumber - 1) * $pageSize;
        $query =  $query->skip($skip)->take($pageSize);

        $args = array("TotalRecords" => $count, "Results" => $query->get());
        return (object)$args;
    }
}