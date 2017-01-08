<?php

namespace Laracraft\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Laracraft\Core\Entities\Entry;
use Laracraft\Core\Entities\EntryType;
use Laracraft\Core\Entities\Field;
use Laracraft\Core\Entities\Section;
use Gate;

class EntryPersistRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $ability = $this->method()=='POST' ? 'create':'update';
		$params = $this->method()=='POST' ? Entry::class :  $this->route()->parameters()['entry'];
        return Gate::allows($ability,$params);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
		/** @var EntryType $entry_type */
		$entry_type = $this->route()->parameters()['entry_type'];

		$fields = $entry_type->fieldLayout->fields;

		$rules = [];

		//do we need a title field
		if($entry_type->has_title_field){
			$rules['title'] = 'required|string|max:255';
		}

		//add field rules
		/** @var Field $field */
		foreach($fields as $field){
			if($field->required){
				$rules['field.' . $field->handle] = 'required';
			}
		}

        if(in_array($this->method(),['PUT','PATCH'])){

        }

        return $rules;

    }

	public function attributes()
	{
		$attributes = [];

		/** @var EntryType $entry_type */
		$entry_type = $this->route()->parameters()['entry_type'];

		$fields = $entry_type->fieldLayout->fields;

		/** @var Field $field */
		foreach($fields as $field){
			if($field->required){
				$attributes['field.' . $field->handle] = $field->label;
			}
		}

		return $attributes;
	}
}
