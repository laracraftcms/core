<?php
namespace Laracraft\Core\Entities\Contracts;

interface RoutableContract
{

	public function url();

	public function needsUrl();

	public function hasUrl();

	public function getUrlAttribute();

	public function generateUrl();

	public function setUrlAttribute($url);

	public function getResolvedView();

	public function resolveView();

	public function getUrlSlug();

	public function getUrlSlugSource();

	public function getExistingSlugs($slug);

}