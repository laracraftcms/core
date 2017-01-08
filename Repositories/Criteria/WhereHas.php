<?php
namespace Laracraft\Core\Repositories\Criteria;

use Laracraft\Core\Repositories\Contracts\RepositoryContract;
use Laracraft\Core\Repositories\Criteria\Contracts\CriteriaContract;
use Closure;

class WhereHas implements CriteriaContract{

	protected $relation;
	protected $closure;

	public function __construct($relation, Closure $closure) {

		$this->relation = $relation;
		$this->closure = $closure;

	}

	/**
	 * Apply criteria in query repository
	 *
	 * @param $model
	 * @param RepositoryContract $repository
	 *
	 * @return mixed
	 */
	public function apply($model, RepositoryContract $repository) {
		return $model->whereHas($this->relation, $this->closure);
	}
}