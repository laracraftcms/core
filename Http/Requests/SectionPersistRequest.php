<?php

namespace Laracraft\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Request;
use Laracraft\Core\Entities\Section;
use Gate;

class SectionPersistRequest extends FormRequest
{

    public function __construct(){

		parent::__construct();

        $type = Request::get('type');

        foreach(Request::all() as $input => $value){
            $matches=[];
            if(preg_match('/^' . $type . '_(.*)/',$input,$matches)){
				Request::merge([$matches[1]=>$value]);
            }
        }

    }
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $ability = $this->method()=='POST' ? 'store':'update';
		$params = $this->method()=='POST' ? Section::class : $this->route()->parameters()['section'];
        return Gate::allows($ability,$params);
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
            'handle' => 'required|alpha_dash|max:255|unique:sections,handle,NULL',
            'type' => 'required|in:' . implode(',',array_keys(Section::getTypes())),
            'has_urls' => 'required|boolean',
            'view' => 'required|string|max:255',
            'has_versions' => 'required|boolean',
            'default_enabled' => 'required|boolean',
            'url_format' => 'required|string|max:255',
            'nested_url_format' => 'required_if:type,' . Section::STRUCTURE_TYPE .'|string|max:255',
        ];

        if(in_array($this->method(),['PUT','PATCH'])){
            $section = $this->route()->parameters()['section'];
            $rules['handle'] = 'required|alpha_dash|max:255|unique:sections,handle,' . $section->id . ',id';
        }

        return $rules;

    }
}
