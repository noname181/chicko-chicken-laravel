<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantRequest extends FormRequest
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
            'phone' => 'required',
            'email' => 'required|email:rfc',
            'rating' => 'required|numeric|between:0,5',
            'delivery_time' => 'required|numeric',
            'for_two' => 'required|numeric',
            'address' => 'required',
            // 'landmark' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'commission_rate' => 'required|numeric',
            'license_code' => 'required',
            'restaurant_charges' => 'required|numeric',
            'delivery_radius' => 'integer',
            // 'is_veg' => 'accepted',
            // 'featured' => 'accepted',
            // 'active' => 'accepted',

            'user_id' => 'required',
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
            'user_id.required' => 'Choose the Restaurant Owner'
        ];
    }

    public function attributes()
    {
        return [
            'for_two' => 'Approx. Price for two people',
            'lat' => 'Latitude',
            'long' => 'Longitude',
        ];
    }
}
