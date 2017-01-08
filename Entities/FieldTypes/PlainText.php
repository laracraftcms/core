<?php

namespace Laracraft\Core\Entities\FieldTypes;

use Laracraft\Core\Database\DBHelper;
use Laracraft\Core\Entities\Field;

class PlainText extends Field{

    protected static $field_type = 'plaintext';


	public function getMaxLength($original = false){

		if($original){
			return isset($this->getOriginalSettings()['max_length'])?$this->getOriginalSettings()['max_length']:null;
		}

		return isset($this->settings['max_length'])?$this->settings['max_length']:null;
	}

    public function getDatabaseType($original = false){

        $maxLength = $this->getMaxLength($original);

        return DBHelper::getTextualColumnTypeByContentLength($maxLength);

    }

    public function getDatabaseTypeParams($original = false){

        $max_length = $this->getMaxLength($original);

        if(!empty($max_length) && $this->getDatabaseType($original) == 'string' ){
            return [$max_length];
        }

        return null;
    }

    public static function defineSettings(){
        return [
            'placeholder'   => 'sometimes|string|max:255',
            'multiline'     => 'required|boolean',
            'initial_rows'   => 'required|integer|min:1',
            'max_length'     => 'required|integer|min:1|max:' . DBHelper::SIZE_MAX_LONGTEXT,
        ];
    }

	public function processSettings(){
		if(
			$this->isSettingDirty('max_length') &&
		   (
			   ($this->getDatabaseType() == 'string' || $this->getDatabaseType(true) == 'string') ||
			   ($this->getDatabaseType() !== $this->getDatabaseType(true))
		   )
		){
			$this->migration['modify'] = $this->fieldGroup;
		}
	}

}