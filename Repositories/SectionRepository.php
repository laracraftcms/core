<?php
namespace Laracraft\Core\Repositories;

use Laracraft\Core\Entities\Section;
use Laracraft\Core\Repositories\Contracts\SectionRepositoryContract;
use Laracraft\Core\Repository\Traits\CacheableRepository;

class SectionRepository extends EloquentRepository implements SectionRepositoryContract {

	use CacheableRepository;

	/**
	 * Specify Model class name or ioc binding
	 *
	 * @return string
	 */
	public function model() {
		return Section::class;
	}

}