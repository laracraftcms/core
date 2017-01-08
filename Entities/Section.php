<?php

namespace Laracraft\Core\Entities;

use Alsofronie\Uuid\UuidModelTrait;
use Laracraft\Core\Entities\Contracts\EnableableContract;
use Laracraft\Core\Entities\Traits\Enableable;
use Laracraft\Core\Entities\Traits\Lockable;
use Laracraft\Core\Entities\Traits\Tracked;
use Illuminate\Database\Eloquent\Model;
use Laracraft\Core\Entities\Contracts\LockableContract;
use Twig;
use Twig_Loader_Array;

class Section extends Model implements LockableContract, EnableableContract
{
	use Tracked;
	use Lockable;
	use Enableable;
	use UuidModelTrait;

	const SINGLE_TYPE = 'single';
	const CHANNEL_TYPE = 'channel';
	const STRUCTURE_TYPE = 'structure';

	const HOME_PLACEHOLDER = '__HOME__';

	public $table = 'sections';

	protected static $types = [];

	protected $casts = [
		'has_urls'          =>  'boolean',
		'has_versions'      =>  'boolean',
		'default_enabled'   =>  'boolean',
		'enabled'           =>  'boolean',
		'max_levels'		=>  'integer'
	];

	protected $fillable = [
		'name',
		'handle',
		'type',
		'has_urls',
		'view',
		'has_versions',
		'default_enabled',
		'url_format',
		'nested_url_format',
		'max_levels',
		'enabled',
	];


	public function getRouteKeyName() {
		return 'handle';
	}

	public function setMaxLevelsAttribute($value){
		$value = intval($value);
		$value = $value < 1 ? null : $value;
		$this->attributes['max_levels'] = $value;
	}

	public static function getTypes(){

		if(empty(self::$types)){
			foreach(config('laracraft-core.section_types') as $type){
				self::$types[$type] = trans('core::entity.section.types.'.$type);
			}
		}

		return self::$types;
	}

	public function entryTypes(){

		return $this->belongsToMany(EntryType::class,'section_entry_types','section_id','entry_type_id');
	}

	public function entries(){
		switch ($this->type){

			case self::SINGLE_TYPE :
				return $this->hasOne(Entry::class);
			case self::CHANNEL_TYPE :
				return $this->hasMany(Entry::class);
			case self::STRUCTURE_TYPE :
				return $this->hasManyThrough(Entry::class,Structure::class,'structured_id','entity_id','id')
					->where('structures.entity_type',Entry::class);
		}
	}

	public function delete(){

		$this->entryTypes()->sync([]);
		$this->editLock()->delete();

		return parent::delete();
	}

	public function resolveView($params){

		if(!str_contains($this->view,'{')){
			return $this->view;
		}

		switch($this->type){
			case Section::STRUCTURE_TYPE :
				return '';
				break;

			default :
				return app('laracraft.viewformatter')->format($this->id, $this->view, $params);
		}
	}

}
