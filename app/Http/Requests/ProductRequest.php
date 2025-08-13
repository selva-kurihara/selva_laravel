<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  public function authorize(): bool
  {
    return true;  // 必要に応じて認可処理を追加してください
  }

  /**
   * バリデーション前の処理
   */
  protected function prepareForValidation(): void
  {
    // 既存 hidden のパスをベースに
    $paths = $this->input('imagePaths', []);

    // 画像ファイルを保存
    for ($i = 0; $i < 4; $i++) {
      $file = $this->file("images.$i");
      if ($file && $file->isValid()) {  
        $paths[$i] = $file->store('temp_images', 'public');
      }
    }

    // リクエストにマージ
    $this->merge(['imagePaths' => $paths]);
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
   */
  public function rules(): array
  {
    return [
      'name' => ['required', 'string', 'max:100'],

      'product_category_id' => [
        'required',
        'integer',
        Rule::exists('product_categories', 'id'),
      ],

      'product_subcategory_id' => [
        'required',
        'integer',
        Rule::exists('product_subcategories', 'id'),
      ],

      'images'   => ['array', 'max:4'],
      'images.*' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png,gif', 'max:10240'],
      'imagePaths'   => ['array'],
      'imagePaths.*' => ['nullable', 'string', 'max:255', 'regex:/\.(jpg|jpeg|png|gif)$/i'],

      'product_content' => ['required', 'string', 'max:500'],
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
      'name' => '商品名',
      'product_category_id' => '商品カテゴリ大',
      'product_subcategory_id' => '商品カテゴリ小',
      'imagePaths.0' => '商品写真1',
      'imagePaths.1' => '商品写真2',
      'imagePaths.2' => '商品写真3',
      'imagePaths.3' => '商品写真4',
      'product_content' => '商品説明',
    ];
  }
}
