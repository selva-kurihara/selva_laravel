<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Administer extends Authenticatable
{
  use HasFactory, SoftDeletes; // SoftDeletes を使う場合追加

  protected $table = 'administers';

  protected $fillable = [
    'name',
    'login_id',
    'password',
  ];

  /**
   * The attributes that should be cast.
   *
   * @var array<string, string>
   */
  protected $casts = [
      'password' => 'hashed',
  ];

  protected $dates = ['deleted_at'];

}
