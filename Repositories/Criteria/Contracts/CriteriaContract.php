<?php
namespace Laracraft\Core\Repositories\Criteria\Contracts;

use Laracraft\Core\Repositories\Contracts\RepositoryContract;
use Illuminate\Database\Eloquent\Model;

interface CriteriaContract
{
    /**
     * Apply criteria in query repository
     *
     * @param $model
     * @param RepositoryContract $repository
     *
     * @return mixed
     */
    public function apply($model, RepositoryContract $repository);
}
