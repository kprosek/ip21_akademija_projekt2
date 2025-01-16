<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Http;

class TokenPrice extends Model
{
    private $listOfCurrencies = null;

    private function getApiData(string $apiEndpoint): array|false
    {
        $api = config('services.coinbase.url');
        $response = Http::get($api . $apiEndpoint);
        if (empty($response)) {
            return false;
        }

        if ($response->failed()) {
            return false;
        }

        return $response->json();
    }

    public function getList(): array|false
    {
        if ($this->listOfCurrencies !== null) {
            return $this->listOfCurrencies;
        }

        $listCurrencies = $this->getApiData('currencies');
        if ($listCurrencies === false) {
            return $listCurrencies;
        }

        $listCrypto = $this->getApiData('currencies/crypto');
        if ($listCrypto === false) {
            return $listCrypto;
        }

        $list = [];
        foreach ($listCurrencies['data'] as $data) {
            $list[] = $data['id'];
        }
        asort($list);

        foreach ($listCrypto['data'] as $data) {
            $list[] = $data['code'];
        }
        asort($list);

        $listOfOrderedTokens =  [];
        $currentIndex = 1;
        foreach ($list as $token) {
            $listOfOrderedTokens[$currentIndex] = $token;
            $currentIndex += 1;
        }

        $this->listOfCurrencies = $listOfOrderedTokens;
        return $listOfOrderedTokens;
    }

    public function verifyCurrency(string $currency, array $masterCurrencyList): array
    {
        if ($masterCurrencyList === false) {
            return [
                'success' => false,
                'error' => 'Unsupported token pair, empty or invalid .json file'
            ];
        }

        if (!in_array($currency, $masterCurrencyList)) {
            return [
                'success' => false,
                'error' => 'Invalid token'
            ];
        }

        return ['success' => true];
    }

    public function getCurrencyPair(string $cryptoGet, string $currencyGet): array
    {
        $api = 'prices/' . $cryptoGet . '-' . $currencyGet . '/spot';
        $currencyPair = $this->getApiData($api);
        if ($currencyPair === false) {
            return [
                'success' => false,
                'error' => 'Unsupported token pair, empty or invalid .json file'
            ];
        }

        return [
            'success' => true,
            'currency pair' => $currencyPair
        ];
    }
}
