<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
            'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg',
            'role' => 'required|integer',
        ];

        if ($this->getMethod() == 'POST') {
            $rules += [
                'password' => 'required',
                'email' => 'required|unique:users,email',
                'phone' => 'required|unique:users,phone',
            ];
        }else if ($this->getMethod() == 'PUT') {
            $rules += [
                'email' => [
                    'required',
                    Rule::unique('users', 'email')->ignore($this->route('id')),
                ],
                'phone' => [
                    'required',
                    Rule::unique('users', 'phone')->ignore($this->route('id')),
                ],
            ];
        }

        return $rules;
    }

    public function attributes()
    {
        return [
            'avatar' => 'Profile Image',
        ];
    }
}
