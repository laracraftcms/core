<?php namespace Laracraft\Core\Entities\Traits;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Support\Collection;
use Laracraft\Core\Entities\Contracts\HasFieldValuesContract;
use Laracraft\Core\Entities\Contracts\RoutableContract;
use Laracraft\Core\Entities\Field;
use Laracraft\Core\Entities\Url;
use Laracraft\Core\Entities\Observers\RoutableObserver;
use Laracraft\Core\Repositories\Contracts\UrlRepositoryContract;

trait Routable {

	protected $urlSlug;
	protected $urlSlugSource;
	protected $resolvedView;
	public $reSlug = false;
	protected $updateUrl;

	public static function bootRoutable()
	{
		static::observe(app(RoutableObserver::class), 0);
	}

	public function url(){
		return $this->morphOne(Url::class,'routable');
	}

	public function needsUrl(){
		return true;
	}

	public function hasUrl(){
		return !is_null($this->getRelationValue('url'));
	}

	public function getUrlAttribute(){
		if($this->needsUrl()) {
			return $this->hasUrl() ? $this->getRelationValue('url')->url : null;
		}
		return null;
	}

	public function getUrlSlug(){
		return isset($this->urlSlug)? $this->urlSlug : Field::SLUG_FIELD_HANDLE;
	}
	public function getUrlSlugSource(){
		return isset($this->urlSlugSource)? $this->urlSlugSource : Field::TITLE_FIELD_HANDLE;
	}

	public function generateUrl(){
		return $this->{$this->getUrlSlug()};
	}

	public function setUrlAttribute($url){
		$url_repository = app(UrlRepositoryContract::class);
		$hash = md5($url);
		if($this->hasUrl()){
			/** @var Url $urlModel */
			$urlModel = $this->getRelationValue('url');
			$url_repository->update(compact('url','hash'), $urlModel);
		}else{
			$routable_id = $this->id;
			$routable_type = get_class($this);
			$this->url()->save($url_repository->create(compact('routable_id','routable_type','url','hash')));
		}
	}

	public function getResolvedView(){
		if(is_null($this->resolvedView)){
			return $this->resolveView();
		}
		return $this->resolvedView;
	}

	public function getExistingSlugs($slug){

		$query = $this->newQuery()->where(function($q) use ($slug) {
			$q->where($this->getUrlSlug(), '=', $slug)
				->orWhere($this->getUrlSlug(), 'LIKE', $slug . '-%');
		});

		// use the model scope to find similar slugs
		if (method_exists($this, 'scopeWithUniqueSlugConstraints')) {
			$query->withUniqueSlugConstraints($this, $this->getUrlSlug(), $slug);
		}

		// get the list of all matching slugs
		$results = $query->select([$this->getUrlSlug(), $this->getTable() . '.' . $this->getKeyName()])
			->get()
			->toBase();

		// key the results and return
		return $results->pluck($this->getUrlSlug(), $this->getKeyName());
	}

	/**
	 * Checks if the slug should be unique, and makes it so if needed.
	 *
	 * @param string $slug
	 * @param RoutableContract $model
	 *
	 * @return string
	 */
	protected function makeSlugUnique($slug, RoutableContract $model)
	{
		// find all models where the slug is like the current one
		/** @var Collection $list */
		$list = $model->getExistingSlugs($slug);

		// if ...
		// 	a) the list is empty, or
		// 	b) our slug isn't in the list
		// ... we are okay
		if (
			$list->count() === 0 ||
			$list->contains($slug) === false
		) {
			return $slug;
		}

		// if our slug is in the list, but
		// 	a) it's for our model, or
		//  b) it looks like a suffixed version of our slug
		// ... we are also okay (use the current slug)
		if ($list->has($model->getKey())) {
			$currentSlug = $list->get($model->getKey());

			if (
				$currentSlug === $slug ||
				strpos($currentSlug, $slug) === 0
			) {
				return $currentSlug;
			}
		}

		// If the slug already exists, but belongs to
		// our model, return the current suffix.
		if ($list->search($slug) === $model->getKey()) {
			return $slug;
		}

		$len = strlen($slug . '-');

		$list->transform(function ($value, $key) use ($len) {
			return intval(substr($value, $len));
		});

		$parts =  explode('-', $slug);
		array_pop($parts);
		return implode('-',$parts) . ($list->max() + 1);

	}
}