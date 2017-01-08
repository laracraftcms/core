<?php

namespace Laracraft\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FieldGroupPersistRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
			'table_name' => 'required|alpha_dash|max:53|unique:field_groups,table_name,NULL'
        ];

		if(in_array($this->method(),['PUT','PATCH'])){
			$field_group = $this->route()->parameters()['field_group'];
			$rules['table_name'] = 'required|alpha_dash|max:53|unique:field_groups,table_name,' . $field_group->id . ',id';
		}

		return $rules;
	}
}
