<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use SoftDeletes;
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
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
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'image_1',
        'image_2',
        'image_3',
        'image_4',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'product_category_id' => 'integer',
        'product_subcategory_id' => 'integer',
    ];
}
