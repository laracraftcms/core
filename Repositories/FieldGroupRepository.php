<?php
namespace Laracraft\Core\Repositories;

use Laracraft\Core\Entities\FieldGroup;
use Laracraft\Core\Repositories\Contracts\FieldGroupRepositoryContract;
use Laracraft\Core\Repository\Traits\CacheableRepository;

class FieldGroupRepository extends EloquentRepository implements FieldGroupRepositoryContract {

	use CacheableRepository;

	/**
	 * Specify Model class name or ioc binding
	 *
	 * @return string
	 */
	public function model() {
		return FieldGroup::class;
	}

}