<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class AdministerRequest extends FormRequest
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
      // ログイン時
      if ($this->isMethod('post') && $this->routeIs('admin.login.post')) {
        return [
          'login_id' => ['required', 'string', 'min:7', 'max:10', 'regex:/^[a-zA-Z0-9]+$/'],
          'password' => ['required', 'string', 'min:8', 'max:20', 'regex:/^[a-zA-Z0-9]+$/'],
        ];
      }


    // 会員登録
    if ($this->routeIs('admin.members.store')) {
      return [
        'name_sei' => ['required', 'string', 'max:50'],
        'name_mei' => ['required', 'string', 'max:50'],
        'nickname' => ['nullable', 'string', 'max:50'],
        'gender' => ['required', 'in:1,2'],
        'password' => ['required', 'confirmed', 'min:8'],
        'email' => ['required', 'email', 'max:255', 'unique:members,email'],
      ];
    }

    // 会員編集
    if ($this->routeIs('admin.members.update')) {
      $memberId = $this->route('member');
      return [
        'name_sei' => ['required', 'string', 'max:50'],
        'name_mei' => ['required', 'string', 'max:50'],
        'nickname' => ['nullable', 'string', 'max:50'],
        'gender' => ['required', 'in:1,2'],
        'password' => ['nullable', 'confirmed', 'min:8'],
        'email' => [
          'required',
          'email',
          'max:255',
          Rule::unique('members', 'email')->ignore($memberId),
        ],
      ];
    }

      return [];
  }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'login_id' => 'ログインID',
            'password' => 'パスワード',
            'name_sei' => '姓',
            'name_mei' => '名',
            'nickname' => 'ニックネーム',
            'gender' => '性別',
            'email' => 'メールアドレス',
        ];
    }
}
