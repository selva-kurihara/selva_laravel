<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Member extends Authenticatable
{
    use SoftDeletes;
    protected $table = 'members';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name_sei',
        'name_mei',
        'nickname',
        'gender',
        'password',
        'email',
        'auth_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'auth_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'password' => 'hashed',
        'gender' => 'integer',
    ];

    use HasFactory, SoftDeletes;

    protected $dates = ['deleted_at'];

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
