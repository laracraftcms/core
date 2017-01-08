<?php
namespace Laracraft\Core\Repositories;

use Laracraft\Core\Entities\Field;
use Laracraft\Core\Repositories\Contracts\FieldRepositoryContract;
use Laracraft\Core\Repository\Traits\CacheableRepository;

class FieldRepository extends EloquentRepository implements FieldRepositoryContract {

	use CacheableRepository;

	/**
	 * Specify Model class name or ioc binding
	 *
	 * @return string
	 */
	public function model() {
		return Field::class;
	}

}