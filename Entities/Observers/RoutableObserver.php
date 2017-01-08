<?php

namespace Laracraft\Core\Entities\Observers;

use Laracraft\Core\Entities\Contracts\HasFieldValuesContract;
use Laracraft\Core\Entities\Contracts\RoutableContract as Routable;
use Laracraft\Core\Repositories\Contracts\UrlRepositoryContract;
use Cocur\Slugify\Slugify;

class RoutableObserver{

	protected $url_repository;

	public function __construct(UrlRepositoryContract $url_repository) {
		$this->url_repository = $url_repository;
	}

	public function saving(Routable $model)
    {
		if(empty($model->{$model->getUrlSlug()}) || $model->reSlug){
			$engine = new Slugify();
			if (method_exists($model, 'customizeSlugEngine')) {
				$engine = $model->customizeSlugEngine($engine, $model->getUrlSlug());
			}

			$slug = $engine->slugify($model->{$model->getUrlSlugSource()}, '-');
			$slug = mb_substr($slug, 0, 255);
			if (in_array($slug, config('laravel-core.reserved_slugs',[]))) {
				$slug .= '-1';
			}
			$slug = $this->makeSlugUnique($slug, $config, $attribute);
		}

		if($model->isDirty($model->getUrlSlug()) || ($model instanceof HasFieldValuesContract && $model->isFieldDirty($model->getUrlSlug()))) {
			$model->updateUrl = true;
		}
    }

	public function saved(Routable $model){

		if($model->updateUrl) {
			$model->setUrlAttribute($model->generateUrl());
		}

		$this->url_repository->getCacheRepository()->tags(['url:' . md5($model->url)])->flush();
	}

	public function deleting(Routable $model){
		$this->url_repository->delete($model->getRelationValue('url'));
	}

}