<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DishRequest extends FormRequest
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
            'description' => 'required',
            // 'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
            'price' => 'required|numeric',
            'discount_price' => 'numeric',

            'restaurant_id' => 'required|integer',
            'dish_category_id' => 'integer',
        ];

        if ($this->getMethod() == 'POST') {
            $rules += [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
            ];
        }else if ($this->getMethod() == 'PUT') {
            $rules += [
                'image' => 'image|mimes:jpeg,png,jpg,gif,svg',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'restaurant_id.required' => 'Choose the Restaurant',
            'dish_category_id.required' => 'Choose the Dish Category'
        ];
    }

    public function attributes()
    {
        return [
            'restaurant_id' => 'Restaurant',
            'dish_category_id' => 'Dish Category',
        ];
    }
}
