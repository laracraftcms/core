<?php
namespace Laracraft\Core\Repositories;

use Laracraft\Core\Entities\Contracts\RoutableContract;
use Laracraft\Core\Entities\Entry;
use Laracraft\Core\Repository\Traits\CacheableRepository;
use Laracraft\Core\Repository\Traits\LoadsFields;
use Illuminate\Database\Eloquent\Model;
use Laracraft\Core\Repositories\Contracts\EntryRepositoryContract;

class EntryRepository extends EloquentRepository implements EntryRepositoryContract {

	use CacheableRepository;
	use LoadsFields;

	/**
	 * Specify Model class name or ioc binding
	 *
	 * @return string
	 */
	public function model() {
		return Entry::class;
	}

	public function parseResult($result) {
		return $this->loadFields($result);
	}
}