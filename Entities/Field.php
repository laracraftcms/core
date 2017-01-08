<?php

namespace Laracraft\Core\Entities;

use Alsofronie\Uuid\UuidModelTrait;
use Illuminate\Database\Eloquent\Model;
use Artisan;
use Illuminate\Support\Collection;
use Laracraft\Core\Entities\Contracts\LockableContract;
use Laracraft\Core\Entities\Traits\Lockable;

class Field extends Model implements LockableContract
{
	use UuidModelTrait;
	use Lockable;

	const PLAINTEXT_TYPE = 'plaintext';
	const BOOLEAN_TYPE = 'boolean';

	const TITLE_FIELD = '26cf2ea5-b615-4b7c-aaf7-4e17f8a6e5ef';
	const TITLE_FIELD_HANDLE = 'title';
	const SLUG_FIELD = '9bb58701-ef6c-4463-b444-8a2c43540adb';
	const SLUG_FIELD_HANDLE = 'slug';

    public $table = 'fields';

    protected static $field_type = null;

    protected static $cast_as = 'string';

	/**
	 * Temporary store/cache of the table name of the field group this field belongs
	 * @var string|null
	 */
	public $source_table = null;

    protected $casts = [
        'settings' => 'array'
    ];

    protected $fillable = [
        'field_group_id',
        'name',
        'handle',
        'help',
        'type',
        'settings'
    ];

    public $migration = [];

	public static $types = null;

	protected $originalSettings = null;

	public function getRouteKeyName() {
		return 'handle';
	}

    /**
     * Boot model
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (Field $model) {
            $model->migration = ['add' => $model->fieldGroup];
        });

        static::deleting(function (Field $model) {
            $model->migration = ['drop' => $model->fieldGroup];
        });

        static::updating(function (Field $model) {

            if($model->isDirty('field_group_id')) {
                $model->migration = [
                    'drop' => FieldGroup::find($model->getOriginal('field_group_id')),
                    'add' => $model->fieldGroup
                ];
            }

            if($model->isDirty('handle')) {
                $model->migration = [ 'rename' => [$model->fieldGroup,$model->getOriginal('handle')]];
            }

			$model->processSettings();
        });

        static::deleted(function(Field $field){
            app()->make('laracraft.generator.migration.field_group')->dropField($field->migration['drop'],$field);
        });

        static::saved(function(Field $model){
            if(!empty($model->migration)){
                foreach($model->migration as $k => $v){
                    switch($k){
                        case 'add':
                            app()->make('laracraft.generator.migration.field_group')->addField($v, $model->newFromBuilder($model->getAttributes()));
                            break;
                        case 'drop':
                            app()->make('laracraft.generator.migration.field_group')->dropField($v,$model);
                            break;
                        case 'rename':
                            app()->make('laracraft.generator.migration.field_group')->renameField($v[0],$model,$v[1]);
                            break;
						case 'modify':
							app()->make('laracraft.generator.migration.field_group')->modifyField($v,$model);
							break;
                    }
                }
				Artisan::call('migrate', ['--force' => true]);
            }
        });

    }


    public function newFromBuilder($attributes = array(), $connection = null) {

		if(is_array($attributes)){
			$attributes = (object) $attributes;
		}

        if(!isset($attributes->type)){
            return parent::newFromBuilder($attributes,$connection);
        }

        $field_types = config('laracraft-core.field_types');

        if(empty($attributes->type)){
            throw new \Exception('Cannot instantiate Field as type not set');
        }

        if(!array_key_exists($attributes->type,$field_types)){
            throw new \Exception('Cannot instantiate Field as invalid type set');
        }

        $class = $field_types[$attributes->type];
        $model = new $class;
        $model->exists = true;

        $model->setRawAttributes((array) $attributes, true);

        $model->setConnection($connection ?: $this->getConnectionName());
        return $model;
    }

    public function fieldGroup(){
        return $this->belongsTo(FieldGroup::class);
    }

    public function getDatabaseType($original = false){
        return 'text';
    }

    public function getDatabaseTypeParams($original = false){
        return null;
    }

    public function getDatabaseTypeOptions($original = false){
        return null;
    }

    public static function defineSettings(){
       return [];
    }

	public function processSettings(){
		//
	}

    public function getLabelAttribute(){
        return (isset($this->pivot) && !empty($this->pivot->name_override))?$this->pivot->name_override:$this->attributes['name'];
    }

	public function getHelpAttribute(){
		return (isset($this->pivot) && !empty($this->pivot->help_override))?$this->pivot->help_override:(isset($this->attributes['help']) ? $this->attributes['help'] : null);
	}

    public function getRequiredAttribute(){
        return isset($this->pivot)?(bool)$this->pivot->required:false;
    }

	public function getInputView(){
		return 'core::fieldtypes.' . (empty($this->type)?config('laracraft-core.default_field_type'):$this->type) . '.input';
	}

    public static function getInputHtml(Field $field, $value, $exists, $label = null, $help = null)
    {
        if(is_null(static::$field_type)){
            return '<textarea class="form-control" id="field_' .$field->handle . '" name="' .$field->handle . '">' . $value . '</textarea>';
        }

        return view($field->getInputView(), array(
            'label'    => empty($label) ? $field->label : $label,
            'name'     => 'field[' . $field->handle . ']',
			'handle'   => $field->handle,
			'help'	   => empty($help) ? $field->help : $help,
            'value'    => $value,
            'required' => $field->required,
            'settings' => $field->settings,
			'exists'   => (boolean) $exists
        ))->render();
    }

	public function getSettingsView(){
		return 'core::fieldtypes.' . (empty($this->type)?config('laracraft-core.default_field_type'):$this->type) . '.settings';
	}

    public static function getSettingsHtml(Field $field)
    {

        return view($field->getSettingsView(), array(
            'name_prefix'     => 'settings[' .(empty($field->handle)?'new':$field->handle). ']',
            'settings' => $field->settings
        ))->render();
    }

    public function prepPostValue($value){
        return $value;
    }

    public function castFromDb($value){
		switch (static::$cast_as) {
			case 'int':
			case 'integer':
				return (int) $value;
			case 'real':
			case 'float':
			case 'double':
				return (float) $value;
			case 'string':
				return (string) $value;
			case 'bool':
			case 'boolean':
				return (bool) $value;
			case 'object':
				return $this->fromJson($value, true);
			case 'array':
			case 'json':
				return $this->fromJson($value);
			case 'collection':
				return new Collection($this->fromJson($value));
			case 'date':
			case 'datetime':
				return $this->asDateTime($value);
			case 'timestamp':
				return $this->asTimeStamp($value);
			default:
				return $value;
		}
    }

	public static function getTypes(){

		if(empty(self::$types)){
			foreach(config('laracraft-core.field_types') as $type => $class){
				self::$types[$type] = trans('core::entity.field.types.' . $type);
			}
		}

		return self::$types;
	}

	public function getOriginalSettings(){
		$this->originalSettings = is_null($this->originalSettings) ? $this->castAttribute('settings',$this->original['settings']) : $this->originalSettings;
		return $this->originalSettings;
	}

	public function isSettingDirty($key){

		if(!$this->isDirty('settings')){
			return false;
		}

		return(
			(array_key_exists($key,$this->settings) && !array_key_exists($key,$this->getOriginalSettings())) ||
			(!array_key_exists($key,$this->settings) && array_key_exists($key,$this->getOriginalSettings())) ||
			(
				array_key_exists($key,$this->settings) &&
			 	array_key_exists($key,$this->getOriginalSettings()) &&
			 	$this->settings[$key] !== $this->getOriginalSettings()[$key]
			)
		);
	}
}
