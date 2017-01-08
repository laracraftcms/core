<?php
namespace Laracraft\Core\Repositories\Contracts;

interface RepositoryContract
{

	/**
	 * Get the route key name for use when resolving items via route binding
	 * @return mixed
	 */
	public function getRouteKeyName();

    /**
     * Retrieve data array for populate field select
     *
     * @param string      $column
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection|array
     */
    public function pluck($column, $key = null);

    /**
     * Retrieve all data of repository
     *
     * @param array $columns
     *
     * @return mixed
     */
    public function all($columns = ['*']);

	/**
	 * Retrieve first result from repository
	 *
	 * @param array $columns
	 *
	 * @return mixed
	 */
	public function first($columns = ['*']);

    /**
     * Retrieve all data of repository, paginated
     *
     * @param null  $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function paginate($limit = null, $columns = ['*']);

    /**
     * Retrieve all data of repository, simple paginated
     *
     * @param null  $limit
     * @param array $columns
     *
     * @return mixed
     */
    public function simplePaginate($limit = null, $columns = ['*']);

    /**
     * Find a single item by id or fail
     *
     * @param       $key
     * @param array $columns
     *
     * @return mixed
     */
    public function find($key, $columns = ['*']);

	/**
	 * Find a single item by field and value or fail
	 *
	 * @param       $field
	 * @param       $value
	 * @param array $columns
	 *
	 * @return mixed
	 */
	public function findOrFailByField($field, $value, $columns = ['*']);

    /**
     * Find data by field and value
     *
     * @param       $field
     * @param       $value
     * @param array $columns
     *
     * @return mixed
     */
    public function findByField($field, $value, $columns = ['*']);

    /**
     * Find data by multiple fields
     *
     * @param array $where
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhere(array $where, $columns = ['*']);

    /**
     * Find data by multiple values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereIn($field, array $values, $columns = ['*']);

    /**
     * Find data by excluding multiple values in one field
     *
     * @param       $field
     * @param array $values
     * @param array $columns
     *
     * @return mixed
     */
    public function findWhereNotIn($field, array $values, $columns = ['*']);


	/**
	 * Create a new entity but do not persist it to the repository
	 *
	 * @param array $attributes
	 *
	 * @return mixed
	 */
	public function instance(array $attributes = []);

    /**
     * Save a new entity in repository
     *
     * @param array $attributes
     *
     * @return mixed
     */
    public function create(array $attributes = []);

    /**
     * Update a entity in repository by id
     *
     * @param array $attributes
     * @param mixed $keyOrModel
     *
     * @return mixed
     */
    public function update(array $attributes, $keyOrModel);

    /**
     * Update or Create an entity in repository
     *
     * @param array $attributes
     * @param array $values
     *
     * @return mixed
     */
    public function updateOrCreate(array $attributes, array $values = []);

    /**
     * Delete a entity in repository by id
     *
     * @param mixed $keyOrModel
     *
     * @return int
     */
    public function delete($keyOrModel);

	/**
	 * Delete multiple entities by given criteria.
	 *
	 * @param array $where
	 *
	 * @return int
	 */
	public function deleteWhere(array $where);

    /**
     * Order collection by a given column
     *
     * @param string $column
     * @param string $direction
     *
     * @return $this
     */
    public function orderBy($column, $direction = 'asc');


	/**
	 * Check if entity has relation
	 *
	 * @param string $relation
	 *
	 * @return $this
	 */
	public function has($relation);

    /**
     * Load relations
     *
     * @param $relations
     *
     * @return $this
     */
    public function with($relations);

    /**
     * Set hidden fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function hidden(array $fields);

    /**
     * Set visible fields
     *
     * @param array $fields
     *
     * @return $this
     */
    public function visible(array $fields);

}
