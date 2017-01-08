<?php
namespace Laracraft\Core\Repositories;

use Closure;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

use Laracraft\Core\Repositories\Criteria\Contracts\CriteriaContract;
use Laracraft\Core\Repositories\Contracts\RepositoryContract;
use Laracraft\Core\Repositories\Contracts\RepositoryCriteriaContract;

/**
 * Class EloquentRepository
 */
abstract class EloquentRepository implements RepositoryContract, RepositoryCriteriaContract{

    /**
     * @var Model|Builder
     */
    protected $model;

    /**
     * Collection of Criteria
     *
     * @var Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;


	protected static function bootTraits( $instance ) {

        $class = static::class;

        foreach (class_uses_recursive($class) as $trait) {
            if (method_exists($class, $method = 'boot'.class_basename($trait))) {
                forward_static_call_array([$class, $method], [$instance]);
            }
        }
    }


    public function __construct()
    {
        $this->criteria = new Collection();

        $this->makeModel();

		static::bootTraits($this);
    }

	public function getRouteKeyName(){
		return $this->model->getRouteKeyName();
	}

    /**
     * @throws \Exception
     */
    public function resetModel()
    {
        $this->makeModel();
    }

    /**
     * Specify Model class name or ioc binding
     *
     * @return string
     */
    abstract public function model();

    /**
     * @return Model|Builder
     * @throws \Exception
     */
    public function makeModel()
    {
        $model = app($this->model());

        if (!$model instanceof Model) {
            throw new \Exception("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $this->model = $model;
    }
	/**
	 * Create a new entity but do not persist it to the repository
	 *
	 * @param array $attributes
	 *
	 * @return mixed
	 */
	public function instance(array $attributes = []){

		$model = $this->model->newInstance($attributes);
		$this->resetModel();

		return $model;

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
        $this->applyCriteria();

        $result = $this->model->pluck($column, $key);

        $this->resetModel();

        return $result;
    }

    /**
     * Retrieve all data from repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        $this->applyCriteria();

        if ($this->model instanceof Builder) {
            $results = $this->model->get($columns);
        } else {
            $results = $this->model->all($columns);
        }

        $this->resetModel();

        return $this->parseResult($results);
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
        $this->applyCriteria();

        $results = $this->model->first($columns);

        $this->resetModel();

        return $this->parseResult($results);
    }

    /**
     * Retrieve all data of repository, paginated
     *
     * @param null   $limit
     * @param array  $columns
     * @param string $method
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*'], $method = "paginate")
    {
        $this->applyCriteria();

        $limit = is_null($limit) ? config('repository.pagination.limit', 15) : $limit;
        $results = $this->model->{$method}($limit, $columns);
        $results->appends(app('request')->query());

        $this->resetModel();

        return $this->parseResult($results);
    }

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param null  $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function simplePaginate($limit = null, $columns = ['*'])
    {
        return $this->paginate($limit, $columns, "simplePaginate");
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
        $this->applyCriteria();

        $model = $this->model->findOrFail($id, $columns);

        $this->resetModel();

        return $this->parseResult($model);
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

		$this->applyCriteria();

		$this->applyConditions([$field => $value]);

		$model = $this->model->firstOrFail($columns);

		$this->resetModel();

		return $this->parseResult($model);

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
        $this->applyCriteria();

        $model = $this->model->where($field, '=', $value)->get($columns);

        $this->resetModel();


        return $this->parseResult($model);
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
        $this->applyCriteria();

        $this->applyConditions($where);

        $model = $this->model->get($columns);
        $this->resetModel();

        return $this->parseResult($model);
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
        $this->applyCriteria();
        $model = $this->model->whereIn($field, $values)->get($columns);
        $this->resetModel();

        return $this->parseResult($model);
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
        $this->applyCriteria();
        $model = $this->model->whereNotIn($field, $values)->get($columns);
        $this->resetModel();

        return $this->parseResult($model);
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
        $model = $this->model->newInstance($attributes);
        $model->save();
        $this->resetModel();

        return $this->parseResult($model);
    }

    /**
     * Update a entity in repository by id
	 *
	 * @param array $attributes
     * @param       $keyOrModel
	 *
	 * @return mixed
     */
    public function update(array $attributes, $keyOrModel)
    {

		$model = $keyOrModel instanceof Model ? $keyOrModel : $this->model->findOrFail($keyOrModel);

        $model->fill($attributes);
        $model->save();

        $this->resetModel();

        return $this->parseResult($model);
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

        $model = $this->model->updateOrCreate($attributes, $values);

        $this->resetModel();

        event(new RepositoryEntityUpdated($this, $model));

        return $this->parseResult($model);
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

		$model = $keyOrModel instanceof Model ? $keyOrModel : $this->find($keyOrModel);

		if($model) {
			$this->resetModel();

			$deleted = $model->delete();

			return $deleted;
		}

		return false;
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
		$this->applyCriteria();

        $this->applyConditions($where);

        $deleted = $this->model->delete();

		$this->resetModel();

        return $deleted;
    }

    /**
     * Check if entity has relation
     *
     * @param string $relation
     *
     * @return $this
     */
    public function has($relation)
    {
        $this->model = $this->model->has($relation);
        return $this;
    }

    /**
     * Load relations
     *
     * @param array|string $relations
     *
     * @return $this
     */
    public function with($relations)
    {
        $this->model = $this->model->with($relations);
        return $this;
    }

    /**
     * Set hidden fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function hidden(array $fields)
    {
        $this->model->setHidden($fields);

        return $this;
    }

    public function orderBy($column, $direction = 'asc')
    {
        $this->model = $this->model->orderBy($column, $direction);

        return $this;
    }

    /**
     * Set visible fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function visible(array $fields)
    {
        $this->model->setVisible($fields);

        return $this;
    }

	/**
	 * Push Criteria for filter the query
	 *
	 * @param $criteria
	 *
	 * @return $this
	 * @throws \Exception
	 */
    public function pushCriteria($criteria)
    {
        if (is_string($criteria)) {
            $criteria = new $criteria;
        }
        if (!$criteria instanceof CriteriaContract) {
            throw new \Exception("Class " . get_class($criteria) . " must be an instance of " . CriteriaContract::class);
        }
        $this->criteria->push($criteria);

        return $this;
    }

    /**
     * Pop Criteria
     *
     * @param $criteria
     *
     * @return $this
     */
    public function popCriteria($criteria)
    {
        $this->criteria = $this->criteria->reject(function ($item) use ($criteria) {
            if (is_object($item) && is_string($criteria)) {
                return get_class($item) === $criteria;
            }

            if (is_string($item) && is_object($criteria)) {
                return $item === get_class($criteria);
            }

            return get_class($item) === get_class($criteria);
        });

        return $this;
    }

    /**
     * Get Collection of Criteria
     *
     * @return Collection
     */
    public function getCriteria()
    {
        return $this->criteria;
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
        $this->model = $criteria->apply($this->model, $this);
        $results = $this->model->get();
        $this->resetModel();

        return $this->parseResult($results);
    }

    /**
     * Skip Criteria
     *
     * @param bool $status
     *
     * @return $this
     */
    public function skipCriteria($skip = true)
    {
        $this->skipCriteria = $skip;

        return $this;
    }

    /**
     * Reset all Criterias
     *
     * @return $this
     */
    public function resetCriteria()
    {
        $this->criteria = new Collection();

        return $this;
    }

    /**
     * Apply criteria in current Query
     *
     * @return $this
     */
    protected function applyCriteria()
    {

        if ($this->skipCriteria === true) {
            return $this;
        }

        $criteria = $this->getCriteria();

        if ($criteria) {
            foreach ($criteria as $c) {
                if ($c instanceof CriteriaContract) {
                    $this->model = $c->apply($this->model, $this);
                }
            }
        }

        return $this;
    }

    /**
     * Applies the given where conditions to the model.
     *
     * @param array $where
     * @return void
     */
    protected function applyConditions(array $where)
    {
        foreach ($where as $field => $value) {
            if (is_array($value)) {
                list($field, $condition, $val) = $value;
                $this->model = $this->model->where($field, $condition, $val);
            } else {
                $this->model = $this->model->where($field, '=', $value);
            }
        }
    }

    /**
     * Wrap result data
     *
     * @param mixed $result
     *
     * @return mixed
     */
    public function parseResult($result)
    {
        return $result;
    }
}
