<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\TokenPrice;
use App\Models\User;

class ShowPriceController
{
    private $id;
    private $email;
    private $tokenPrice;

    public function __construct(Request $request)
    {
        $this->tokenPrice = new TokenPrice;
        $this->id = $request->session()->get('id');
        $this->email = $request->session()->get('email');
    }

    public function index(Request $request)
    {
        $tokenFrom = $request->input('dropdown_token_from');
        $tokenTo = $request->input('dropdown_token_to');

        $isFromFavourite = false;
        $isToFavourite = false;
        $list = $this->tokenPrice->getList();
        if (Auth::check()) {
            $userFavouriteTokens = User::find($this->id)->favourites()->orderBy('token_name', 'asc')->get()->pluck('token_name')->toArray();
        } else {
            $userFavouriteTokens = [];
        }
        $dropdownList = array_merge($userFavouriteTokens, array_filter($list, function ($value) use ($userFavouriteTokens) {
            return !in_array($value, $userFavouriteTokens);
        }));

        return view('index')
            ->with('email', $this->email)
            ->with('userFavouriteTokens', $userFavouriteTokens)
            ->with('dropdownList', $dropdownList)
            ->with('tokenFrom', $tokenFrom)
            ->with('tokenTo', $tokenTo)
            ->with('isFromFavourite', $isFromFavourite)
            ->with('isToFavourite', $isToFavourite);
    }

    public function showPrice(Request $request)
    {
        $list = $this->tokenPrice->getList();
        if (Auth::check()) {
            $userFavouriteTokens = User::find($this->id)->favourites()->orderBy('token_name', 'asc')->get()->pluck('token_name')->toArray();
        } else {
            $userFavouriteTokens = [];
        }
        $dropdownList = array_merge($userFavouriteTokens, array_filter($list, function ($value) use ($userFavouriteTokens) {
            return !in_array($value, $userFavouriteTokens);
        }));

        if ($request->isMethod('get')) {
            $tokenFrom = trim($request->input('dropdown_token_from'), ' *');
            $tokenTo = trim($request->input('dropdown_token_to'), ' *');

            $isFromFavourite = in_array($tokenFrom, $userFavouriteTokens);
            $isToFavourite = in_array($tokenTo, $userFavouriteTokens);

            $currencyPair = $this->tokenPrice->getCurrencyPair($tokenFrom, $tokenTo);

            $price = round($currencyPair["currency pair"]["data"]["amount"], 3);

            return view('/show-price')
                ->with('email', $this->email)
                ->with('id', $this->id)
                ->with('userFavouriteTokens', $userFavouriteTokens)
                ->with('dropdownList', $dropdownList)
                ->with('tokenFrom', $tokenFrom)
                ->with('tokenTo', $tokenTo)
                ->with('price', $price)
                ->with('isFromFavourite', $isFromFavourite)
                ->with('isToFavourite', $isToFavourite);
        }

        if ($request->isMethod('post')) {
            $tokenFrom = trim($request->input('dropdown_token_from'), ' *');
            $tokenTo = trim($request->input('dropdown_token_to'), ' *');
            $btnFavourite = $request->input('btn-favourite');

            $token = $btnFavourite === 'btn_from' ? $tokenFrom : $tokenTo;

            if (in_array($token, $userFavouriteTokens)) {
                User::find($this->id)->favourites()->where('token_name', $token)->delete();
                $updatedUserFavouriteTokens = User::find($this->id)->favourites()->orderBy('token_name', 'asc')->get()->pluck('token_name')->toArray();

                $isFromFavourite = in_array($tokenFrom, $updatedUserFavouriteTokens);
                $isToFavourite = in_array($tokenTo, $updatedUserFavouriteTokens);

                return view('/show-price')
                    ->with('email', $this->email)
                    ->with('userFavouriteTokens', $updatedUserFavouriteTokens)
                    ->with('dropdownList', $dropdownList)
                    ->with('tokenFrom', $tokenFrom)
                    ->with('tokenTo', $tokenTo)
                    ->with('price', null)
                    ->with('isFromFavourite', $isFromFavourite)
                    ->with('isToFavourite', $isToFavourite);
            }

            if (!in_array($token, $userFavouriteTokens)) {
                User::find($this->id)->favourites()->updateOrInsert(
                    ['token_name' => $token],
                    ['user_id' => $this->id, 'token_name' => $token]
                );
                $updatedUserFavouriteTokens = User::find($this->id)->favourites()->orderBy('token_name', 'asc')->get()->pluck('token_name')->toArray();
                $isFromFavourite = in_array($tokenFrom, $updatedUserFavouriteTokens);
                $isToFavourite = in_array($tokenTo, $updatedUserFavouriteTokens);

                return view('/show-price')
                    ->with('email', $this->email)
                    ->with('userFavouriteTokens', $updatedUserFavouriteTokens)
                    ->with('dropdownList', $dropdownList)
                    ->with('tokenFrom', $tokenFrom)
                    ->with('tokenTo', $tokenTo)
                    ->with('price', null)
                    ->with('isFromFavourite', $isFromFavourite)
                    ->with('isToFavourite', $isToFavourite);
            }
        }
    }
}
