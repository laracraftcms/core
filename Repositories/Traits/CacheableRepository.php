<?php
namespace Laracraft\Core\Repository\Traits;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Database\Eloquent\Model;
use Laracraft\Core\Repositories\Criteria\Contracts\CriteriaContract;
use Illuminate\Support\Collection;
use Exception;
use Laracraft\Core\Repositories\EloquentRepository;
use SuperClosure\Serializer;
use Auth;
use Closure;
use Log;

trait CacheableRepository
{

    /**
     * @var CacheRepository
     */
    protected $cacheRepository = null;

	/**
	 * @var Collection
	 */
	protected $tags;


	public static function bootCacheableRepository(EloquentRepository $repository){
		$repository->tags = new Collection($repository->getBaseCacheTags());
	}

	public function getBaseCacheTags(){
		return [ $this->model() ];
	}

	public function getCacheTags(){
		return $this->tags->toArray();
	}

	public function flushCache($tags = []){
		if (empty($tags)){
			$tags  = $this->getCacheTags();
		}
		$this->cacheRepository->tags($tags)->flush();
	}
    /**
     * Set Cache Repository
     *
     * @param CacheRepository $repository
     *
     * @return $this
     */
    public function setCacheRepository(CacheRepository $repository)
    {
        $this->cacheRepository = $repository;

        return $this;
    }

    /**
     * Return instance of Cache Repository
     *
     * @return CacheRepository
     */
    public function getCacheRepository()
    {
        if (is_null($this->cacheRepository)) {
            $this->cacheRepository = app(config('laracraft-core.cache.repository', 'cache'));
        }

        return $this->cacheRepository;
    }

    /**
     * Skip Cache
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCache($status = true)
    {
        $this->cacheSkip = $status;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSkippedCache()
    {
        $skipped = isset($this->cacheSkip) ? $this->cacheSkip : false;
        $request = app('Illuminate\Http\Request');
		if(Auth::check()) {
			$skipCacheParam = config('laracraft-core.cache.params.skipCache', 'skipCache');

			if($request->has($skipCacheParam) && $request->get($skipCacheParam)) {
				$skipped = true;
			}
		}

        return $skipped;
    }

    /**
     * @param $method
     *
     * @return bool
     */
    protected function allowedCache($method)
    {
        $cacheEnabled = config('laracraft-core.cache.enabled', true);

        if (!$cacheEnabled) {
            return false;
        }

        $cacheOnly = isset($this->cacheOnly) ? $this->cacheOnly : config('laracraft-core.cache.allowed.only', null);
        $cacheExcept = isset($this->cacheExcept) ? $this->cacheExcept : config('laracraft-core.cache.allowed.except', null);

        if (is_array($cacheOnly)) {
            return in_array($method, $cacheOnly);
        }

        if (is_array($cacheExcept)) {
            return !in_array($method, $cacheExcept);
        }

        if (is_null($cacheOnly) && is_null($cacheExcept)) {
            return true;
        }

        return false;
    }

    /**
     * Get Cache key for the method
     *
     * @param $method
     * @param $args
     *
     * @return string
     */
    public function getCacheKey($method, $args = null)
    {

        $request = app('Illuminate\Http\Request');
        $args = serialize($args);
        $criteria = $this->serializeCriteria();
        $key = sprintf('%s@%s-%s', get_called_class(), $method, md5($args . $criteria));

        return $key;

    }

    /**
     * Serialize the criteria making sure the Closures are taken care of.
     *
     * @return string
     */
    protected function serializeCriteria()
    {
		$serialized = '';

		foreach($this->getCriteria() as $criterion){
			try{
				if($criterion instanceof Closure){
					/** @var Serializer $serializer */
					$serializer = app('serializer');
					$serialized .= $serializer->serialize($criterion);
				}else{
					$serialized .= serialize($criterion);
				}
			}catch (Exception $e) {
				Log::warning($e);
			}
		}

		return strlen($serialized) ? md5($serialized) : $serialized;

    }

    /**
     * Get cache minutes
     *
     * @return int
     */
    public function getCacheMinutes($function = null)
    {
		if(isset($this->cacheMinutes) && is_array($this->cacheMinutes) and array_key_exists($function, $this->cacheMinutes)){
			return $this->cacheMinutes[$function];
		}

        $cacheMinutes = isset($this->defaultCacheMinutes) ? $this->defaultCacheMinutes : config('laracraft-core.cache.default_cache_minutes', 120);

        return $cacheMinutes;
    }

	/**
	 * @param $tags
	 *
	 * @return $this
	 */
	public function tags($tags){
		$this->tags = $this->tags->merge($tags);

		return $this;
	}

	/**
	 * Retrieve data array for populate field select
	 *
	 * @param string      $column
	 * @param string|null $key
	 *
	 * @return \Illuminate\Support\Collection|array
	 */
	public function pluck($column, $key = null)
	{
		if (!$this->allowedCache(__FUNCTION__) || $this->isSkippedCache()) {
			return parent::pluck($column, $key);
		}

		$key = $this->getCacheKey(__FUNCTION__, func_get_args());
		$minutes = $this->getCacheMinutes(__FUNCTION__);
		$value = $this->getCacheRepository()->tags($this->getCacheTags())->remember($key, $minutes, function () use ($column, $key) {
			return parent::pluck($column, $key);
		});

		return $value;
	}

	/**
     * Retrieve all data of repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        if (!$this->allowedCache(__FUNCTION__) || $this->isSkippedCache()) {
            return parent::all($columns);
        }

        $key = $this->getCacheKey(__FUNCTION__, func_get_args());
        $minutes = $this->getCacheMinutes(__FUNCTION__);
        $value = $this->getCacheRepository()->tags($this->getCacheTags())->remember($key, $minutes, function () use ($columns) {
            return parent::all($columns);
        });

        return $value;
    }

	/**
	 * Retrieve first data of repository
	 *
	 * @param array $columns
	 *
	 * @return mixed
	 */
	public function first($columns = ['*'])
	{
		if (!$this->allowedCache(__FUNCTION__) || $this->isSkippedCache()) {
			return parent::first($columns);
		}

		$key = $this->getCacheKey(__FUNCTION__, func_get_args());
		$minutes = $this->getCacheMinutes(__FUNCTION__);
		$value = $this->getCacheRepository()->tags($this->getCacheTags())->remember($key, $minutes, function () use ($columns) {
			return parent::first($columns);
		});

		return $value;
	}

	/**
	 * Retrieve all data of repository, paginated
	 *
	 * @param null $limit
	 * @param array $columns
	 * @param string $method
	 *
	 * @return mixed
	 */
    public function paginate($limit = null, $columns = ['*'], $method = 'paginate')
    {
        if (!$this->allowedCache(__FUNCTION__) || $this->isSkippedCache()) {
            return parent::paginate($limit, $columns);
        }

        $key = $this->getCacheKey(__FUNCTION__, func_get_args());

        $minutes = $this->getCacheMinutes(__FUNCTION__);
        $value = $this->getCacheRepository()->tags($this->getCacheTags())->remember($key, $minutes, function () use ($limit, $columns, $method) {
            return parent::paginate($limit, $columns, $method);
        });

        return $value;
    }

    /**
     * Find data by id
     *
     * @param       $id
     * @param array $columns
     *
     * @return mixed
     */
    public function find($id, $columns = ['*'])
    {
        if (!$this->allowedCache(__FUNCTION__) || $this->isSkippedCache()) {
            return parent::find($id, $columns);
        }

        $key = $this->getCacheKey(__FUNCTION__, func_get_args());
        $minutes = $this->getCacheMinutes(__FUNCTION__);
        $value = $this->getCacheRepository()->tags($this->getCacheTags())->remember($key, $minutes, function () use ($id, $columns) {
            return parent::find($id, $columns);
        });

        return $value;
    }

	/**
	 * Find a single item by field and value or fail
	 *
	 * @param       $field
	 * @param       $value
	 * @param array $columns
	 *
	 * @return mixed
	 */
	public function findOrFailByField($field, $value, $columns = ['*']){

		if (!$this->allowedCache(__FUNCTION__) || $this->isSkippedCache()) {
			return parent::findOrFailByField($field, $value, $columns);
		}
		$key = $this->getCacheKey(__FUNCTION__, func_get_args());

		$minutes = $this->getCacheMinutes(__FUNCTION__);
		$value = $this->getCacheRepository()->tags($this->getCacheTags())->remember($key, $minutes, function () use ($field, $value, $columns) {
			return parent::findOrFailByField($field, $value, $columns);
		});

		return $value;

	}

    /**
     * Find data by field and value
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findByField($field, $value = null, $columns = ['*'])
    {
        if (!$this->allowedCache(__FUNCTION__) || $this->isSkippedCache()) {
            return parent::findByField($field, $value, $columns);
        }

        $key = $this->getCacheKey(__FUNCTION__, func_get_args());
        $minutes = $this->getCacheMinutes(__FUNCTION__);
        $value = $this->getCacheRepository()->tags($this->getCacheTags())->remember($key, $minutes, function () use ($field, $value, $columns) {
            return parent::findByField($field, $value, $columns);
        });

        return $value;
    }

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*'])
    {
        if (!$this->allowedCache(__FUNCTION__) || $this->isSkippedCache()) {
            return parent::findWhere($where, $columns);
        }

        $key = $this->getCacheKey(__FUNCTION__, func_get_args());
        $minutes = $this->getCacheMinutes(__FUNCTION__);
        $value = $this->getCacheRepository()->tags($this->getCacheTags())->remember($key, $minutes, function () use ($where, $columns) {
            return parent::findWhere($where, $columns);
        });

        return $value;
    }

	/**
	 * Find data by multiple values in one field
	 *
	 * @param       $field
	 * @param array $values
	 * @param array $columns
	 *
	 * @return mixed
	 */
	public function findWhereIn($field, array $values, $columns = ['*'])
	{
		if (!$this->allowedCache(__FUNCTION__) || $this->isSkippedCache()) {
			return parent::findWhereIn($field, $values, $columns);
		}

		$key = $this->getCacheKey(__FUNCTION__, func_get_args());
		$minutes = $this->getCacheMinutes(__FUNCTION__);
		$value = $this->getCacheRepository()->tags($this->getCacheTags())->remember($key, $minutes, function () use ($field, $values, $columns) {
			return parent::findWhereIn($field, $values, $columns);
		});

		return $value;
	}

	/**
	 * Find data by excluding multiple values in one field
	 *
	 * @param       $field
	 * @param array $values
	 * @param array $columns
	 *
	 * @return mixed
	 */
	public function findWhereNotIn($field, array $values, $columns = ['*'])
	{
		if (!$this->allowedCache(__FUNCTION__) || $this->isSkippedCache()) {
			return parent::findWhereNotIn($field, $values, $columns);
		}

		$key = $this->getCacheKey(__FUNCTION__, func_get_args());
		$minutes = $this->getCacheMinutes(__FUNCTION__);
		$value = $this->getCacheRepository()->tags($this->getCacheTags())->remember($key, $minutes, function () use ($field, $values, $columns) {
			return parent::findWhereNotIn($field, $values, $columns);
		});

		return $value;
	}


	/**
	 * Save a new entity in repository
	 *
	 * @param array $attributes
	 *
	 * @return mixed
	 */
	public function create(array $attributes = [])
	{
		$result = parent::create($attributes);
		if($result){
			$this->getCacheRepository()->tags($this->getBaseCacheTags())->flush();
		}
		return $result;
	}


	/**
	 * Update a entity in repository by id
	 *
	 * @param array $attributes
	 * @param       $id
	 *
	 * @return mixed
	 */
	public function update(array $attributes, $id)
	{
		$result = parent::update($attributes, $id);
		if($result){
			$this->getCacheRepository()->tags($this->getBaseCacheTags())->flush();
		}
		return $result;
	}

	/**
	 * Update or Create an entity in repository
	 *
	 * @param array $attributes
	 * @param array $values
	 *
	 * @return mixed
	 */
	public function updateOrCreate(array $attributes, array $values = [])
	{
		$result = parent::updateOrCreate($attributes, $values);
		if($result){
			$this->getCacheRepository()->tags($this->getBaseCacheTags())->flush();
		}
		return $result;
	}

	/**
	 * Delete a entity in repository by id
	 *
	 * @param mixed $keyOrModel
	 *
	 * @return int
	 */
	public function delete($keyOrModel)
	{
		$result = parent::delete($keyOrModel);
		if($result){
			$this->getCacheRepository()->tags($this->getBaseCacheTags())->flush();
		}
		return $result;
	}

	/**
	 * Delete multiple entities by given criteria.
	 *
	 * @param array $where
	 *
	 * @return int
	 */
	public function deleteWhere(array $where)
	{

		$result = parent::deleteWhere($where);
		if($result){
			$this->getCacheRepository()->tags($this->getBaseCacheTags())->flush();
		}
		return $result;
	}

    /**
     * Find data by Criteria
     *
     * @param CriteriaContract $criteria
     *
     * @return mixed
     */
    public function getByCriteria(CriteriaContract $criteria)
    {
        if (!$this->allowedCache(__FUNCTION__) || $this->isSkippedCache()) {
            return parent::getByCriteria($criteria);
        }

        $key = $this->getCacheKey(__FUNCTION__, func_get_args());
        $minutes = $this->getCacheMinutes(__FUNCTION__);
        $value = $this->getCacheRepository()->tags($this->getCacheTags())->remember($key, $minutes, function () use ($criteria) {
            return parent::getByCriteria($criteria);
        });

        return $value;
    }
}
