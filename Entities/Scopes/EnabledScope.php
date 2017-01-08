<?php namespace Laracraft\Core\Entities\Scopes;

use Laracraft\Core\Entities\Contracts\EnableableContract as Enableable;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class EnabledScope implements Scope{

	/**
	 * All of the extensions to be added to the builder.
	 *
	 * @var array
	 */
	protected $extensions = ['WithDisabled', 'OnlyDisabled'];


	/**
	 * Apply the scope to a given Eloquent query builder.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @param  \Illuminate\Database\Eloquent\Model|Enableable  $model
	 * @return void
	 */
	public function apply(Builder $builder, Model $model)
	{
		$builder->where($model->getQualifiedEnabledColumn(),1);
	}

	/**
	 * Extend the query builder with the needed functions.
	 *
	 * @param  \Illuminate\Database\Eloquent\Builder  $builder
	 * @return void
	 */
	public function extend(Builder $builder)
	{
		foreach ($this->extensions as $extension) {
			$this->{"add{$extension}"}($builder);
		}

	}

	/**
	 * Extend Builder with custom method.
	 *
	 * @param Builder  $builder
	 */
	protected function addWithDisabled(Builder $builder)
	{
		$builder->macro('withDisabled', function(Builder $builder)
		{
			return $builder->withoutGlobalScope($this);
		});
	}

	/**
	 * Extend Builder with custom method.
	 *
	 * @param Builder  $builder
	 */
	protected function addOnlyDisabled(Builder $builder)
	{
		$builder->macro('onlyDisabled', function(Builder $builder)
		{
			/** @var Enableable $model */
			$model = $builder->getModel();
			$column = $model->getQualifiedEnabledColumn();

			return $builder->withoutGlobalScope($this)->where($column,0);
		});
	}


}