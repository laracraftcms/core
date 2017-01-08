<?php

namespace Laracraft\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Laracraft\Core\Entities\Field;

class FieldPersistRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
		$types = config('laracraft-core.field_types');

        $rules = [
            'name' => 'required|string|max:255',
			'handle' => 'required|alpha_dash|max:255|unique:fields,handle,NULL',
			'type' => 'required|in:' . implode(',',array_keys($types)),
			'settings'=>'sometimes|array'
        ];

		$settings = [];
		if($this->has('type') && in_array($this->get('type'),array_keys($types))){
			/** @var Field $class */
			$class = $types[$this->get('type')];
			$settings = $class::defineSettings();
		}

		foreach($settings as $handle => $rule){
			$rules['settings.' . $handle ] = $rule;
		}

		if(in_array($this->method(),['PUT','PATCH'])){
			$field = $this->route()->parameters()['field'];
			$rules['handle'] = 'required|alpha_dash|max:255|unique:fields,handle,' . $field->id . ',id';
		}


		return $rules;
	}

	public function attributes()
	{
		$attributes = [];

		$types = config('laracraft-core.field_types');

		$settings = [];
		if($this->has('type') && in_array($this->get('type'),array_keys($types))){
			/** @var Field $class */
			$class = $types[$this->get('type')];
			$settings = $class::defineSettings();
		}

		foreach($settings as $handle => $rule){
			$attributes['settings.' . $handle ] = trans('core::field_type.' . $this->get('type') . '.settings.' . $handle . '.label');
		}

		return $attributes;
	}
}
