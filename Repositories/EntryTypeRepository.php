<?php
namespace Laracraft\Core\Repositories;

use Laracraft\Core\Entities\EntryType;
use Laracraft\Core\Repositories\Contracts\EntryTypeRepositoryContract;
use Laracraft\Core\Repository\Traits\CacheableRepository;

class EntryTypeRepository extends EloquentRepository implements EntryTypeRepositoryContract {

	use CacheableRepository;

	/**
	 * Specify Model class name or ioc binding
	 *
	 * @return string
	 */
	public function model() {
		return EntryType::class;
	}

}