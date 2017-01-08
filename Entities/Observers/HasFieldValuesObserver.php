<?php

namespace Laracraft\Core\Entities\Observers;

use Laracraft\Core\Entities\Contracts\HasFieldValuesContract as HasFieldValues;
use Laracraft\Core\Entities\Field;
use DB;

class HasFieldValuesObserver{

    public function saved(HasFieldValues $model)
    {

		if($model->isPopulated()){
			$dirtyFields = $model->getDirtyFields();

			/** @var Field[] $fieldMap */
			$fieldMap = $model->getFieldMap();

			if(count($dirtyFields) > 0){

				$updates = [];
				foreach($dirtyFields as $key => $value){
					if(!array_key_exists($fieldMap[$key]->source_table,$updates)){
						$updates[$fieldMap[$key]->source_table] = [];
					}
					$updates[$fieldMap[$key]->source_table][$key] = $value;
				}

				foreach($updates as $table => $fields){
					DB::table($table . '_content')
						->updateOrInsert([
											 'entity_id' => $model->getKey(),
											 'entity_type' => get_class($model),
											 'locale' => $model->getLocale()
										 ],$fields);
				}
			}
		}
    }
}