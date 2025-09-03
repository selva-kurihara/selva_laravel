<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\UploadedFile;

class ReviewRequest extends FormRequest
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
    $rules = [
      'evaluation' => ['required', 'integer', 'min:1', 'max:5'],
      'comment'    => ['required', 'string', 'max:500'],
    ];

    // 管理画面からのアクセスかどうか判定
    // 例: ルート名が "admin.reviews.*" の場合
    if ($this->routeIs('admin.reviews.*')) {
      $rules['product_id'] = ['required', 'integer', 'exists:products,id'];
      $rules['member_id']  = ['required', 'integer', 'exists:members,id'];
    }

    return $rules;
  }

  /**
   * Get custom attributes for validator errors.
   *
   * @return array<string, string>
   */
  public function attributes(): array
  {
    return [
      'evaluation' => '評価',
      'comment' => '商品コメント',
      'product_id' => '商品',
      'member_id' => '会員',
    ];
  }
}
