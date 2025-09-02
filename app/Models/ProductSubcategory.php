<?php
// app/Models/ProductSubcategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductSubcategory extends Model
{
  use SoftDeletes;

  protected $fillable = ['product_category_id', 'name'];

  public function category()
  {
    return $this->belongsTo(ProductCategory::class);
  }
}