<?php

namespace Laracraft\Core\Entities;

use Alsofronie\Uuid\UuidModelTrait;
use Laracraft\Core\Entities\Contracts\LockableContract;
use Laracraft\Core\Entities\Traits\Lockable;
use Illuminate\Database\Eloquent\Model;
use Laracraft\Core\Repositories\Contracts\FieldGroupRepositoryContract;
use Laracraft\Core\Repositories\Contracts\FieldRepositoryContract;

class FieldLayout extends Model implements LockableContract
{

	use UuidModelTrait;
    use Lockable;

    public $table = 'field_layouts';

    protected $fillable = [
        'name',
		'handle'
    ];

	public function getRouteKeyName() {
		return 'handle';
	}

    public function entryTypes(){
        return $this->hasMany(EntryType::class);
    }

    public function fields(){
        return $this->belongsToMany(Field::class,'field_layout_fields')->withPivot('field_layout_tab_id','name_override','help_override','required');
    }

    public function tabs(){
        return $this->hasMany(FieldLayoutTab::class)->orderBy('sequence');
    }

    public function fieldCount(){

        return $this->fields()
            ->selectRaw('count(*) as aggregate, field_layout_id')
			->groupBy('pivot_field_layout_id');
    }

    public function getFieldCountAttribute()
    {
        // if relation is not loaded already, let's do it first
        if ( ! $this->relationLoaded('fieldCount'))
            $this->load('fieldCount');

        $related = $this->getRelation('fieldCount')->first();

        // then return the count directly
        return ($related) ? (int) $related->aggregate : 0;
    }

    public function fieldLayoutTabCount(){

        return $this->tabs()
            ->selectRaw('count(*) as aggregate, field_layout_id')->groupBy('field_layout_id');
    }

    public function getFieldLayoutTabCountAttribute()
    {
        // if relation is not loaded already, let's do it first
        if ( ! $this->relationLoaded('fieldLayoutTabCount'))
            $this->load('fieldLayoutTabCount');

        $related = $this->getRelation('fieldLayoutTabCount')->first();

        // then return the count directly
        return ($related) ? (int) $related->aggregate : 0;
    }

	public function getFieldQueries(FieldGroupRepositoryContract $field_group_repository, FieldRepositoryContract $field_repository, $includeTitle = true, $includeSlug = true){

		$groups = [];
		$columns = [];

		if($includeTitle || $includeSlug) {
			$default_group = $field_group_repository->find(FieldGroup::DEFAULT_GROUP);
			$groups[FieldGroup::DEFAULT_GROUP] = $default_group;
			$columns[FieldGroup::DEFAULT_GROUP] = [];
		}

		if($includeTitle){
			$title_field = $field_repository->find(Field::TITLE_FIELD);
			$columns[FieldGroup::DEFAULT_GROUP][] = $title_field->handle;
		}

		if($includeSlug){
			$slug_field = $field_repository->find(Field::SLUG_FIELD);
			$columns[FieldGroup::DEFAULT_GROUP][] = $slug_field->handle;
		}

		/** @var Field[] $fields */
		foreach ($this->fields as $field) {
			if(!array_key_exists($field->field_group_id,$groups)) {
				$groups[$field->field_group_id] = $field->fieldGroup;
			}
			if(!array_key_exists($field->field_group_id,$columns)) {
				$columns[$field->field_group_id] = [];
			}
			$columns[$field->field_group_id][] = $field->handle;
		}

		$queries = [];
		foreach($groups as $id => $g){
			$queries[$g->table_name] = $columns[$id];
		}

		return $queries;
	}

}
