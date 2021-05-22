<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CouponRequest extends FormRequest
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
            'description' => 'required|max:255',
            'discount_type' => 'required|in:PERCENTAGE,FIXED',
            'discount' => 'required|numeric',
            'expire_date' => 'required|date_format:d/m/Y',
            'max_usage' => 'required|integer',

            'restaurant_id' => 'required|integer',
        ];
        if ($this->getMethod() == 'POST') {
            $rules += [
                'coupon_code' => 'required|unique:coupons,coupon_code',
            ];
        }else if ($this->getMethod() == 'PUT') {
            $rules += [
                // 'coupon_code' => 'required|unique:coupons,coupon_code',
                'coupon_code' => [
                    'required',
                    Rule::unique('coupons', 'coupon_code')->ignore($this->route('id')),
                ]
            ];
        }
        return $rules;
    }

    public function messages()
    {
        return [
            'restaurant_id.required' => 'Choose the Restaurant',
        ];
    }

    public function attributes()
    {
        return [
            'restaurant_id' => 'Restaurant',
        ];
    }
}
