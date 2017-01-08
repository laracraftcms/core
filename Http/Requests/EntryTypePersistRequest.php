<?php

namespace Laracraft\Core\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class EntryTypePersistRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $ability = $this->method()=='POST' ? 'store':'update';
        return Gate::allows($ability,$this->route()->parameters()['entry_type']);

    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'field_layout_id' => 'required|string',
            'name' => 'required|string|max:255',
            'handle' => 'required|alpha_dash|max:255|unique:entry_types,handle',
            'has_title_field' => 'required|boolean',
            'title_config' => 'required_if:has_title_field,0|string|max:255'
        ];

        if(in_array($this->method(),['PUT','PATCH'])){
            $type = $this->route()->parameters()['entry_type'];
            $rules['handle'] = 'required|string|max:255|unique:entry_types,handle,' . $type->id;
        }

        return $rules;
    }
}
