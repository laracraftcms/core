<?php

namespace Laracraft\Core\Repository\Traits;

use Illuminate\Support\Collection;

trait LoadsFields
{
	protected $loadFields = true;

	protected $fields = null;

	protected $include_title_field = true;

	protected $include_slug_field = false;

	public function skipLoadFields($skip) {
		$this->loadFields = !(boolean)$skip;
		return $this;
	}

	public function setFieldsToLoad($fields){
		$this->fields = $fields;
		return $this;
	}

	public function setIncludeTitle($bool){
		$this->include_title_field = (boolean) $bool;
		return $this;
	}
	public function setIncludeSlug($bool){
		$this->include_slug_field = (boolean) $bool;
		return $this;
	}

	public function loadFields($result) {

		if($this->loadFields) {
			if($result instanceof Collection) {
				$collection = $result;
			} else {
				$collection = new Collection($result);
			}
			$method = (is_array($this->fields) || $this->fields instanceof \ArrayAccess) ? 'populateOnly' : 'populate';
			app('laracraft.fieldmanager')
				->setIncludeTitle($this->include_title_field)
				->setIncludeSlug($this->include_slug_field)
				->{$method}($collection, $this->fields);
		}

		return $result;
	}
}