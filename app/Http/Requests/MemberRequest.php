<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

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
      // ログイン時
      if ($this->isMethod('post') && $this->routeIs('login.post')) {
        return [
          'email' => ['required', 'email'],
          'password' => ['required'],
        ];
      }

      // パスワード再設定時メール送信時（未ログイン時）
      if ($this->isMethod('post') && $this->routeIs('password.email')) {
        return [
          'email' => ['required', 'email', 'exists:members,email'],
        ];
      }

      // メールアドレス変更時メール送信時（ログイン時）
      if ($this->isMethod('post') && $this->routeIs('members.email.update')) {
        return [
          'email' => ['required', 'string', 'email', 'max:200', Rule::unique('members', 'email')->whereNull('deleted_at'),],
        ];
      }

      // パスワード再設定時（未ログイン時）とパスワード変更時（ログイン時）
      if ($this->isMethod('post') && ($this->routeIs('password.update') || $this->routeIs('members.password.update'))) {
        return [
          'password' => ['required', 'string', 'confirmed', 'min:8', 'max:20', 'regex:/^[a-zA-Z0-9]+$/'],
          'password_confirmation' => ['required', 'string', 'min:8', 'max:20', 'regex:/^[a-zA-Z0-9]+$/'],
        ];
      }

      // 認証コード入力時（ログイン時）
      if ($this->isMethod('post') && $this->routeIs('members.email.verify')) {
        return [
          'auth_code' => ['required', 'string', 'max:6', Rule::exists('members', 'auth_code')->where('id', Auth::id()),],
        ];
      }

      // 新規登録時: $memberId = null
      // 編集時: $memberId = 実際のID
      $memberId = $this->input('id');

      if (is_null($memberId)) {

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
                'max:200',
                Rule::unique('members')->ignore($memberId)->whereNull('deleted_at'),
            ],
        ];
      }else{
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
        ];
      }
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
