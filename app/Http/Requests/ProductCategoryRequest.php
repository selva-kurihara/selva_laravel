<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;

class ProductCategoryRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;  // 必要に応じて認可処理を追加してください
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      // 商品大カテゴリ：必須、文字列、20文字以内
      'name' => ['required', 'string', 'max:20'],

      // 商品小カテゴリ（配列全体の制御）
      'subcategories'   => ['required', 
                            'array', 
                            'max:10',
                            function ($attribute, $value, $fail) {
                                if (collect($value)->filter(fn($v) => filled($v))->isEmpty()) {
                                    $fail('商品小カテゴリを少なくとも1つ入力してください。');
                                }
                            },
                          ],

      // 商品小カテゴリ（各要素ごとの制御）
      'subcategories.*' => ['nullable', 'string', 'max:20'],
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
      'name' => '商品大カテゴリ',
      'subcategories.*' => '商品小カテゴリ',
    ];
  }
}
