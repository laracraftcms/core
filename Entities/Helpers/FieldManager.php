<?php

namespace Laracraft\Core\Entities\Helpers;

use DB;
use Laracraft\Core\Entities\Entry;
use Illuminate\Support\Collection;
use Laracraft\Core\Entities\Field;
use Laracraft\Core\Entities\FieldGroup;
use Laracraft\Core\Entities\FieldLayout;
use Laracraft\Core\Entities\FieldLayoutTab;
use Laracraft\Core\Entities\Traits\HasFields;
use App;
use Laracraft\Core\Repositories\Contracts\FieldGroupRepositoryContract;
use Laracraft\Core\Repositories\Contracts\FieldRepositoryContract;

class FieldManger
{

    protected $collection;

    protected $grouped;

    protected $grouped_ids;

	/** @var  FieldLayout[] */
    protected $layouts;

	/** @var  FieldLayoutTab[] */
	protected $tabs;

	/** @var FieldGroupRepositoryContract */
	protected $field_group_repository;

	/** @var FieldRepositoryContract */
	protected $field_repository;

	protected $include_title_field = true;

	protected $include_slug_field = true;

	public function __construct(FieldGroupRepositoryContract $field_group_repository, FieldRepositoryContract $field_repository) {
		$this->field_group_repository = $field_group_repository;
		$this->field_repository = $field_repository;
	}

	public function setIncludeTitle($bool){
		$this->include_title_field = (boolean) $bool;
		return $this;
	}
	public function setIncludeSlug($bool){
		$this->include_slug_field = (boolean) $bool;
		return $this;
	}

	public function populateOnly(Collection $collection, $fields = []){
		$this->collection = $collection;

		$groups = [];
		$columns = [];


		if(is_null($fields)){
			$fields = [];
		}

		/** @var Field[] $fields */
		foreach ($fields as $field) {
			if(!array_key_exists($field->field_group_id,$groups)) {
				$groups[$field->field_group_id] = $field->fieldGroup;
			}
			if(!array_key_exists($field->field_group_id,$columns)) {
				$columns[$field->field_group_id] = [];
			}
			$columns[$field->field_group_id][] = $field->handle;
		}

		if($this->include_title_field || $this->include_slug_field) {
			if(!array_key_exists(FieldGroup::DEFAULT_GROUP,$groups)) {
				$default_group = $this->field_group_repository->find(FieldGroup::DEFAULT_GROUP);
				$groups[FieldGroup::DEFAULT_GROUP] = $default_group;
				$columns[FieldGroup::DEFAULT_GROUP] = [];
			}
		}

		if($this->include_title_field){
			if(!array_key_exists(Field::TITLE_FIELD,$columns[FieldGroup::DEFAULT_GROUP])) {
				$title_field = $this->field_repository->find(Field::TITLE_FIELD);
				$title_field->source_table = FieldGroup::DEFAULT_GROUP_TABLE;
				$columns[FieldGroup::DEFAULT_GROUP][] = Field::TITLE_FIELD_HANDLE;
				$fields[] = $title_field;
			}
		}

		if($this->include_slug_field){
			if(!array_key_exists(Field::SLUG_FIELD,$columns[FieldGroup::DEFAULT_GROUP])) {
				$slug_field = $this->field_repository->find(Field::SLUG_FIELD);
				$slug_field->source_table = FieldGroup::DEFAULT_GROUP_TABLE;
				$columns[FieldGroup::DEFAULT_GROUP][] = Field::SLUG_FIELD_HANDLE;
				$fields[] = $slug_field;
			}
		}


		$queries = [];
		foreach($groups as $id => $g){
			$queries[$g->table_name] = $columns[$id];
		}

		$this->grouped = ['all'=> $collection];

		$this->grouped_ids = ['all' => $collection->pluck('id')->toArray()];
		$this->tabs = [];
		$this->layouts['all'] = new \stdClass();
		$this->layouts['all']->fields = new Collection($fields);

		$this->processFieldQueries('all',$queries);

		return $this->collection;
	}

	public function populate(Collection $collection){
		$this->collection = $collection;

        $this->grouped = $this->collection->groupBy('fieldLayout.id');
        $this->grouped_ids = [];
        foreach($this->grouped as $layout_id => $g){
            $this->grouped_ids[$layout_id] = $this->grouped[$layout_id]->pluck('id')->toArray();
        }

        $this->layouts =  $this->collection->pluck('fieldLayout', 'fieldLayout.id');

		$this->tabs = [];
        foreach($this->layouts as $layout_id => $layout){
			$this->tabs[$layout_id] = $layout->tabs->keyBy('id');
            $this->processFieldQueries($layout_id, $layout->getFieldQueries($this->field_group_repository,$this->field_repository, $this->include_title_field, $this->include_slug_field));
        }

        return $this->collection;
    }

    protected function processFieldQueries($layout_id, $queries){

        foreach($queries as $table => $columns){
            $query  = DB::table($table . '_content');
            call_user_func([$query,'select'],array_merge(['entity_id'],$columns));
            $query->where('locale', App::getLocale())->whereIn('entity_id',$this->grouped_ids[$layout_id]);
            $results = $query->get();

			$results = $results->keyBy('entity_id');
            $this->matchFieldQueryResults($layout_id, $this->prepResults($layout_id, $table, $results));
        }
    }

    protected function prepResults($layout_id, $table, $results){

        $fieldsMap = $this->layouts[$layout_id]->fields->keyBy('handle');





		$tabs = array_key_exists($layout_id, $this->tabs) ? $this->tabs[$layout_id] : [];

		foreach($tabs as $tab){
			$tab->setRelation('fields', new Collection());
		}

		foreach($fieldsMap as $field){
			$field->source_table = $table;
			if(!empty($tabs)) {
				$field->setRelation('tab', $tabs[$field->pivot->field_layout_tab_id]);
				$tabs[$field->pivot->field_layout_tab_id]->fields->push($field);
			}
		}

		foreach($tabs as $tab){
			$tab->setRelation('fields', $tab->fields->sortBy('pivot.sequence'));
		}


		if($this->include_title_field){
			if(!$fieldsMap->has(Field::TITLE_FIELD_HANDLE)) {
				$title_field = $this->field_repository->find(Field::TITLE_FIELD);
				$title_field->source_table = FieldGroup::DEFAULT_GROUP_TABLE;
				$fieldsMap[Field::TITLE_FIELD_HANDLE] = $title_field;
			}
		}

		if($this->include_slug_field){
			if(!$fieldsMap->has(Field::SLUG_FIELD_HANDLE)) {
				$slug_field = $this->field_repository->find(Field::SLUG_FIELD);
				$slug_field->source_table = FieldGroup::DEFAULT_GROUP_TABLE;
				$fieldsMap[Field::SLUG_FIELD_HANDLE] = $slug_field;
			}
		}

		/** @var Collection $fieldsMap */
		$handles = $fieldsMap->keys()->toArray();

        $prepared = [];
        foreach($results as $entity_id => $row){
            $values = [];
            foreach( $handles as $handle){
                if(property_exists($row,$handle)) {
					/** @var Field[] $fieldsMap */
                    $values[$handle] = $fieldsMap[$handle]->castFromDb($row->{$handle});
                }
            }
            $prepared[$entity_id] = $values;
        }

        return [$fieldsMap, $prepared];
    }

    protected function matchFieldQueryResults($layout_id, $preparedResults){
        $entities = $this->grouped[$layout_id];

        list($fieldMap, $results) = $preparedResults;

        /** @var HasFields $entity */
        foreach($entities as $entity){
            $entity->importFieldValues($fieldMap, ($entity->exists && array_key_exists($entity->id, $results))? $results[$entity->id] : [], App::getLocale());
        }
    }
}