<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddonRequest extends FormRequest
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
        $rules =  [
            'name' => 'required|max:255',
            'price' => 'required|numeric',
            'addons_category_id' => 'integer',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'addons_category_id.required' => 'Choose the Dish Addon Category'
        ];
    }

    public function attributes()
    {
        return [
            'addons_category_id' => 'Dish Addon Category',
        ];
    }
}
