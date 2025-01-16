<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavouriteToken extends Model
{
    protected $table = 'favourites';

    protected $fillable = ['token_name'];

    public $timestamps = false;

    public function insertFavouriteTokens(array $tokens)
    {
        foreach ($tokens as $token) {
            FavouriteToken::updateOrInsert([
                'token_name' => $token,
                'token_name' => $token
            ]);
        }
    }

    public function getFavouriteTokens()
    {
        $favourites = FavouriteToken::orderBy('token_name', 'asc')->get();

        if ($favourites === []) {
            return [];
        }

        $userFavouriteTokens = [];
        foreach ($favourites as $row) {
            $userFavouriteTokens[] = $row->token_name;
        }
        return $userFavouriteTokens;
    }

    public function deleteFavouriteTokens(array $tokens)
    {
        foreach ($tokens as $token) {
            FavouriteToken::where('token_name', '=', $token)->delete();
        }
    }

    public function verifyTokenFavourite(array $tokens, array $favourites)
    {
        foreach ($tokens as $token) {
            if (!in_array($token, $favourites)) {
                return false;
            };
        }
    }
}
