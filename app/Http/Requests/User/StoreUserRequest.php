<?php

namespace App\Http\Requests\User;

use App\Models\User\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Action access is enforced by the route middleware.
        return true;
    }

    public function rules(): array
    {
        return [
            'FirstName' => ['required', 'string', 'max:40'],
            'LastName' => ['required', 'string', 'max:40'],

            'UserName' => [
                'required',
                'string',
                'max:40',
                Rule::unique(User::class, 'UserName'),
            ],

            'Password' => [
                'required',
                'string',
                'max:72',
                Password::min(8),
            ],

            'Email' => ['nullable', 'email', 'max:50'],
            'mobile' => ['nullable', 'string', 'max:50'],
            'nationalCode' => ['nullable', 'digits:10'],
            'isActive' => ['required', 'boolean'],
            'RequireChangePassword' => ['required', 'boolean'],
        ];
    }

    public function attributes(): array
    {
        return [
            'FirstName' => 'first name',
            'LastName' => 'last name',
            'UserName' => 'username',
            'Password' => 'password',
            'Email' => 'email',
            'mobile' => 'mobile',
            'nationalCode' => 'national code',
            'isActive' => 'account status',
            'RequireChangePassword' => 'password change on login',
        ];
    }
}