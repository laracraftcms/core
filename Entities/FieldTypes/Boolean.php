<?php

namespace Laracraft\Core\Entities\FieldTypes;

use Laracraft\Core\Entities\Field;

class Boolean extends Field{

    protected static $field_type = 'boolean';
	protected static $cast_as = 'boolean';

    public function getDatabaseType($original = false){
        return 'boolean';
    }

    public static function defineSettings(){
        return [
            'default'   => 'boolean'
        ];
    }

	public function prepPostValue($value){
		return (boolean) $value;
	}
}