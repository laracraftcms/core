<?php
namespace Laracraft\Core\Repositories;

use Illuminate\Support\Collection;
use Laracraft\Core\Entities\Contracts\RoutableContract;
use Laracraft\Core\Entities\Url;
use Laracraft\Core\Repositories\Contracts\UrlRepositoryContract;
use Laracraft\Core\Repository\Traits\CacheableRepository;
use Laracraft\Core\Repository\Traits\LoadsFields;
use Illuminate\Database\Eloquent\Model;

class UrlRepository extends EloquentRepository implements UrlRepositoryContract {

	use CacheableRepository;
	use LoadsFields;

	/**
	 * Specify Model class name or ioc binding
	 *
	 * @return string
	 */
	public function model() {
		return Url::class;
	}

	public function loadFields($result) {

		if($this->loadFields) {
			if($result instanceof Collection) {
				$collection = $result->pluck('routable');
			} else {
				$collection  = new Collection([$result->routable]);
			}
			$method = (is_array($this->fields) || $this->fields instanceof \ArrayAccess) ? 'populateOnly' : 'populate';
			app('laracraft.fieldmanager')
				->setIncludeTitle($this->include_title_field)
				->setIncludeSlug($this->include_slug_field)
				->{$method}($collection, $this->fields);
		}

		return $result;
	}

	public function parseResult($result) {

		if($result instanceof Model && $result->routable instanceof RoutableContract){
			$result->routable->getResolvedView();
		}

		return $this->loadFields($result);
	}
}