<?php
namespace Laracraft\Core\Entities\Contracts;

interface HasFieldValuesContract
{
	public function initFieldValues();

	public function importFieldValues($fieldMap, $valuesAndSourceMap, $locale);

	public function isPopulated();

	public function getField($key);

	public function setField($key, $value);

	public function getFieldMap();

	public function populateFieldValues();

	public function isFieldDirty($attributes = null);

	public function getDirtyFields();

	public function getLocale();

	public function setLocale($locale);

}