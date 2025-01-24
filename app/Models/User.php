<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\TokenFavourite;

class User extends Authenticatable
{
    protected $fillable = ['email', 'password'];
    protected $hidden = ['password'];
    public $timestamps = false;

    public function favourites(): HasMany
    {
        return $this->hasMany(TokenFavourite::class);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
}
