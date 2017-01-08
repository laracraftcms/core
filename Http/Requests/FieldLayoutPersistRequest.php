<?php

namespace Laracraft\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FieldLayoutPersistRequest extends FormRequest
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
        return [
            'name' => 'required|string|max:255',
            'names' => 'sometimes|required|array',
            'tabs' => 'sometimes|required|array',
            'layout' => 'sometimes|required|array',
            'required' => 'sometimes|required|array'
        ];
    }
}
