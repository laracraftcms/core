<?php

namespace Laracraft\Core\Http\Requests;

use Gate;
use Illuminate\Foundation\Http\FormRequest;

class SectionUpdateEntryTypesRequest extends FormRequest
{

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('update',$this->route()->parameters()['section']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'entry_types' => 'array'
        ];
        return $rules;

    }
}
