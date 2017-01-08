<?php

namespace Laracraft\Core\Entities\Helpers;

use Laracraft\Core\Entities\FieldGroup;

class SluggableFieldService extends \Cviebrock\EloquentSluggable\Services\SlugService{

	/**
	 * Get all existing slugs that are similar to the given slug.
	 *
	 * @param string $slug
	 * @param string $attribute
	 * @param array $config
	 * @return \Illuminate\Support\Collection
	 */
	protected function getExistingSlugs($slug, $attribute, array $config)
	{
		$separator = $config['separator'];

		$results = \DB::table(FieldGroup::DEFAULT_GROUP_TABLE . '_content')
			->where(function($q) use ($attribute, $slug, $separator) {
			$q->where($attribute, '=', $slug)
				->orWhere($attribute, 'LIKE', $slug . $separator . '%');
		})->select($attribute, 'entity_id as id')->get()->toBase();

		// key the results and return
		return $results->pluck($attribute, $this->model->getKeyName());
	}

	/**
	 * Query scope for finding "similar" slugs, used to determine uniqueness.
	 *
	 * @param \Illuminate\Database\Eloquent\Builder $query
	 * @param \Illuminate\Database\Eloquent\Model $model
	 * @param string $attribute
	 * @param array $config
	 * @param string $slug
	 * @return \Illuminate\Database\Eloquent\Builder
	 */
	public function scopeFindSimilarSlugs(Builder $query, Model $model, $attribute, $config, $slug)
	{
		$separator = $config['separator'];

		return $query->where(function( $q) use ($attribute, $slug, $separator) {
			$q->where($attribute, '=', $slug)
				->orWhere($attribute, 'LIKE', $slug . $separator . '%');
		});
	}

}