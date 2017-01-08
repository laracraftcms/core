<?php namespace Laracraft\Core\Entities\Scopes;

use Carbon\Carbon;
use Illuminate\Database\Query\Builder as BaseBuilder;
use Illuminate\Database\Eloquent\ScopeInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PublishedScope implements ScopeInterface{

	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function apply(Builder $builder, Model $model)
	{
		$now = Carbon::now();
		$builder->where($model->getPublishedAtColumn(),'<',$now)
			->where($model->getExpiredAtColumn(),'>',$now);

		$this->addWithPendingAndExpired($builder);
		$this->addOnlyExpired($builder);
	}

	/**
	 * Remove scope from the query.
	 *
	 * @param \Illuminate\Database\Eloquent\Builder  $builder
	 * @param \Illuminate\Database\Eloquent\Model  $model
	 * @return void
	 */
	public function remove(Builder $builder, Model $model)
	{
		$query = $builder->getQuery();

		$publishedcolumns = [
			$model->getQualifiedPublishedAtColumn(),
			$model->getPublishedAtColumn()
		];

		$expiredcolumns = [
			$model->getQualifiedExpiredAtColumn(),
			$model->getExpiredAtColumn()
		];

		$bindingKey = 0;

		foreach ((array) $query->wheres as $key => $where)
		{
			if ($this->isPublishedConstraint($where, $publishedcolumns))
			{
				$this->removeWhere($query, $key);

				// Here SoftDeletingScope simply removes the where
				// but since we use Basic where (not Null type)
				// we need to get rid of the binding as well
				$this->removeBinding($query, $bindingKey);
			}

			if ($this->isExpiredConstraint($where, $expiredcolumns))
			{
				$this->removeWhere($query, $key);

				// Here SoftDeletingScope simply removes the where
				// but since we use Basic where (not Null type)
				// we need to get rid of the binding as well
				$this->removeBinding($query, $bindingKey);
			}

			// Check if where is either NULL or NOT NULL type,
			// if that's the case, don't increment the key
			// since there is no binding for these types
			if ( ! in_array($where['type'], ['Null', 'NotNull'])){
				$bindingKey++;
			}
		}
	}

	/**
	 * Remove scope constraint from the query.
	 *
	 * @param  BaseBuilder  $query
	 * @param  int  $key
	 * @return void
	 */
	protected function removeWhere(BaseBuilder &$query, $key)
	{

		unset($query->wheres[$key]);
		$query->wheres = array_values($query->wheres);
	}

	/**
	 * Remove scope constraint from the query.
	 *
	 * @param  BaseBuilder  $query
	 * @param  int  $key
	 * @return void
	 */
	protected function removeBinding(BaseBuilder $query, $key)
	{
		$bindings = $query->getRawBindings()['where'];

		unset($bindings[$key]);

		$query->setBindings($bindings);
	}

	/**
	 * Check if given where is the scope constraint.
	 *
	 * @param  array   $where
	 * @param  array  $columns
	 * @return boolean
	 */
	protected function isPublishedConstraint(array $where, $columns)
	{
		return ($where['type'] == 'Basic' && in_array($where['column'],$columns) && $where['operator'] == '<');
	}

	/**
	 * Check if given where is the scope constraint.
	 *
	 * @param  array   $where
	 * @param  array  $columns
	 * @return boolean
	 */
	protected function isExpiredConstraint(array $where, $columns)
	{
		return ($where['type'] == 'Basic' && in_array($where['column'],$columns) && $where['operator'] == '>');
	}

	/**
	 * Extend Builder with custom method.
	 *
	 * @param Builder  $builder
	 */
	protected function addWithPendingAndExpired(Builder $builder)
	{
		$builder->macro('withPendingAndExpired', function(Builder $builder)
		{
			$this->remove($builder, $builder->getModel());

			return $builder;
		});
	}

	/**
	 * Extend Builder with custom method.
	 *
	 * @param Builder  $builder
	 */
	protected function addOnlyExpired(Builder $builder)
	{
		$builder->macro('onlyExpired', function(Builder $builder)
		{

			$model = $builder->getModel();
			$column = $model->getQualifiedExpiredAtColumn();

			$this->remove($builder, $model);

			return $builder->where($column,'<',Carbon::now());
		});
	}

	/**
	 * Extend Builder with custom method.
	 *
	 * @param Builder  $builder
	 */
	protected function addWithExpired(Builder $builder)
	{
		$builder->macro('withExpired', function(Builder $builder)
		{

			$model = $builder->getModel();
			$column = $model->getQualifiedPublishedAtColumn();

			$this->remove($builder, $model);

			return $builder->where($column,'<',Carbon::now());
		});
	}

	/**
	 * Extend Builder with custom method.
	 *
	 * @param Builder  $builder
	 */
	protected function addOnlyPending(Builder $builder)
	{
		$builder->macro('onlyPending', function(Builder $builder)
		{

			$model = $builder->getModel();
			$column = $model->getQualifiedPublishedAtColumn();

			$this->remove($builder, $model);

			return $builder->where($column,'>',Carbon::now());
		});
	}

	/**
	 * Extend Builder with custom method.
	 *
	 * @param Builder  $builder
	 */
	protected function addWithPending(Builder $builder)
	{
		$builder->macro('withPending', function(Builder $builder)
		{

			$model = $builder->getModel();
			$column = $model->getQualifiedExpiredAtColumn();

			$this->remove($builder, $model);

			return $builder->where($column,'>',Carbon::now());
		});
	}


}