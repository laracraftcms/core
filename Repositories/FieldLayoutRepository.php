<?php
namespace Laracraft\Core\Repositories;

use Laracraft\Core\Entities\FieldLayout;
use Laracraft\Core\Repositories\Contracts\FieldLayoutRepositoryContract;
use Laracraft\Core\Repository\Traits\CacheableRepository;

class FieldLayoutRepository extends EloquentRepository implements FieldLayoutRepositoryContract {

	use CacheableRepository;

	/**
	 * Specify Model class name or ioc binding
	 *
	 * @return string
	 */
	public function model() {
		return FieldLayout::class;
	}

}