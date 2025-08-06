<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MemberRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name_sei' => [
                'required',
                'string',
                'max:20'
            ],
            'name_mei' => [
                'required',
                'string',
                'max:20'
            ],
            'nickname' => [
                'required',
                'string',
                'max:10'
            ],
            'gender' => [
                'required',
                'in:1,2'
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/^[a-zA-Z0-9]+$/',
                'confirmed'
            ],
            'password_confirmation' => [
                'required',
                'string',
                'min:8',
                'max:20',
                'regex:/^[a-zA-Z0-9]+$/'
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:200'
            ],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name_sei' => '氏名（姓）',
            'name_mei' => '氏名（名）',
            'nickname' => 'ニックネーム',
            'gender' => '性別',
            'password' => 'パスワード',
            'password_confirmation' => 'パスワード確認',
            'email' => 'メールアドレス',
        ];
    }
}
