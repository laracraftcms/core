<?php
namespace Laracraft\Core\Repositories\Contracts;


interface RepositoryLoadsFieldsContract{

	public function skipLoadFields($skip);

	public function setFieldsToLoad($fields);

	public function loadFields($result);

	public function setIncludeTitle($bool);

	public function setIncludeSlug($bool);

}