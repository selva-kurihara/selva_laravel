<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
  use SoftDeletes;

  protected $table = 'reviews';

  /**
   * Mass assignable attributes
   */
  protected $fillable = [
    'member_id',
    'product_id',
    'evaluation',
    'comment',
  ];

  /**
   * Hidden attributes for serialization
   */
  protected $hidden = [
    
  ];

  /**
   * Attribute casts
   */
  protected $casts = [
   
  ];

  // Review.php
  public function member()
  {
    return $this->belongsTo(Member::class, 'member_id');
  
  }
}
