<?php
// app/Models/ProductSubcategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSubcategory extends Model
{
protected $fillable = ['product_category_id', 'name'];
}