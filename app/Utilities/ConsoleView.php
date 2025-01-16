<?php

namespace App\Utilities;

class ConsoleView
{
    public function printList(array $list): void
    {
        foreach ($list as $key => $token) {
            echo ($key . ' ' . $token . PHP_EOL);
        }
    }

    public function printPricePair(array $currencyPair): void
    {
        $price = sprintf('%s: %.2f %s', $currencyPair['data']['base'], $currencyPair['data']['amount'], $currencyPair['data']['currency']);
        echo ($price);
    }

    public function printFavouriteTokens(string $text): void
    {
        echo (PHP_EOL . "Your favourite tokens: " . PHP_EOL . $text . PHP_EOL);
    }
}
