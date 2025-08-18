<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
  use SoftDeletes;

  protected $table = 'products';

  /**
   * Mass assignable attributes
   */
  protected $fillable = [
    'member_id',
    'product_category_id',
    'product_subcategory_id',
    'name',
    'image_1',
    'image_2',
    'image_3',
    'image_4',
    'product_content',
  ];

  /**
   * Hidden attributes for serialization
   */
  protected $hidden = [
    'image_1',
    'image_2',
    'image_3',
    'image_4',
  ];

  /**
   * Attribute casts
   */
  protected $casts = [
    'product_category_id' => 'integer',
    'product_subcategory_id' => 'integer',
  ];

  /**
   * カテゴリとのリレーション
   */
  public function category()
  {
    return $this->belongsTo(ProductCategory::class, 'product_category_id');
  }

  /**
   * サブカテゴリとのリレーション
   */
  public function subcategory()
  {
    return $this->belongsTo(ProductSubcategory::class, 'product_subcategory_id');
  }

  /**
   * 代表画像取得（image_1があれば返す）
   */
  public function getImagePathAttribute()
  {
    return $this->image_1 ?? null;
  }

  public function reviews()
  {
    return $this->hasMany(Review::class);
  }
}
