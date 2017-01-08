<?php
namespace Laracraft\Core\Entities\Traits;

use Illuminate\Support\Collection;
use Laracraft\Core\Entities\Contracts\HasFieldValuesContract;
use Laracraft\Core\Entities\Observers\HasFieldValuesObserver;
use App;
use Laracraft\Core\Entities\Field;

trait HasFieldValues
{

	protected $locale = null;

	protected $fields_populated = false;

	protected $include_title_field = true;

    /** @var Collection */
    protected $field_map = null;

    /** @var Collection */
    protected $field_values = null;

	/** @var Collection */
	protected $field_originals = null;

	public static function bootHasFieldValues()
	{

		static::observe(new HasFieldValuesObserver());

	}

	/**
	 * Fill the model with an array of attributes.
	 *
	 * @param  array  $attributes
	 * @return $this
	 *
	 * @throws \Illuminate\Database\Eloquent\MassAssignmentException
	 */
	public function fill(array $attributes)
	{
		parent::fill($attributes);

		$this->fillFields($attributes);

		return $this;
	}

	/**
	 * Create a new instance of the given model.
	 *
	 * @param  array  $attributes
	 * @param  bool  $exists
	 * @return static
	 */
	public function newInstance($attributes = [], $exists = false)
	{
		$model = parent::newInstance($attributes, $exists);
		$model->initFieldValues();
		$this->fillFields($attributes,$model);

		return $model;
	}

	protected function fillFields($attributes = [], $model = null){

		if(is_null($model)){
			$model = $this;
		}

		if(isset($attributes['field']) && is_array($attributes['field'])){
			$model->populateFieldValues();
			foreach($attributes['field'] as $handle => $value){
				$model->setField($handle,$value);
			}
		}
	}

	public function initFieldValues(){
        $this->field_values = is_null($this->field_values) ? new Collection() : $this->field_values;
        $this->field_map = is_null($this->field_map) ? new Collection() : $this->field_map;
		$this->field_originals = is_null($this->field_originals) ? new Collection() : $this->field_originals;
    }

    public function importFieldValues($fieldMap, $values, $locale){
        $this->field_values = is_null($this->field_values) ? new Collection($values) : $this->field_values->merge($values);
       	$this->field_map = is_null($this->field_map)? $fieldMap : $this->field_map->merge($fieldMap);
		$this->field_originals = new Collection($this->field_values);
		$this->fields_populated = true;
		$this->setLocale($locale);
    }

	public function isPopulated(){
		return $this->fields_populated;
	}

	public function populateFieldValues(){
		if(!$this->isPopulated()) {
			app('laracraft.fieldmanager')->populate(new Collection([$this]));
		}
	}

    public function getField($key = null){
		return is_null($key) ? $this->field_values : $this->field_values->get($key,null);
    }

	public function getFieldMap(){
		return $this->field_map;
	}

	/**
	 * Get the fields that have been changed since last sync.
	 *
	 * @return array
	 */
	public function getDirtyFields()
	{
		$dirty = [];

		foreach ($this->field_values as $key => $value) {
			if (! $this->field_originals->has($key)) {
				$dirty[$key] = $value;
			} elseif ($value !== $this->field_originals[$key] &&
					  ! $this->originalFieldIsNumericallyEquivalent($key)) {
				$dirty[$key] = $value;
			}
		}

		return $dirty;
	}

	/**
	 * Determine if the new and old values for a given key are numerically equivalent.
	 *
	 * @param  string  $key
	 * @return bool
	 */
	protected function originalFieldIsNumericallyEquivalent($key)
	{
		$current = $this->field_values[$key];

		$original = $this->field_originals[$key];

		return is_numeric($current) && is_numeric($original) && strcmp((string) $current, (string) $original) === 0;
	}

	/**
	 * Determine if the model or given attribute(s) have been modified.
	 *
	 * @param  array|string|null  $attributes
	 * @return bool
	 */
	public function isFieldDirty($attributes = null)
	{
		$dirtyFields = $this->getDirtyFields();
		if (is_null($attributes)) {
			return count($dirtyFields) > 0;
		}

		if (! is_array($attributes)) {
			$attributes = func_get_args();
		}

		foreach ($attributes as $attribute) {
			if (array_key_exists($attribute, $dirtyFields)) {
				return true;
			}
		}

		return false;
	}

    public function setField($key, $value){
        if($this->field_map->has($key)){
			$value = $this->field_map[$key]->prepPostValue($value);
            $this->field_values->put($key,$value);
            return true;
        }

        return false;
    }

	public function getLocale(){
		return $this->locale ? $this->locale : $this->locale = App::getLocale();
	}

	public function setLocale($locale){
		return $this->locale = $locale;
	}

    public function __get($key)
    {
        $modelValue = $this->getAttribute($key);

        return is_null($modelValue) && $this->field_map->has($key) ?$this->getField($key):$modelValue;
    }

    public function __set($key, $value)
    {
        if(!is_null($this->getAttribute($key)) || !$this->field_map->has($key)){
            $this->setAttribute($key,$value);
        }else{
            $this->setField($key,$value);
        }
    }
}