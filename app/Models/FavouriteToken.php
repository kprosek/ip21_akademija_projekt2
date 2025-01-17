<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavouriteToken extends Model
{
    protected $table = 'favourites';

    protected $fillable = ['token_name'];

    public $timestamps = false;
}
