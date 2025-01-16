<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use App\Models\TokenPrice;
use App\Models\FavouriteToken;
use App\Models\User;
use App\Utilities\ConsoleView;

class CLICommand extends Command implements PromptsForMissingInput
{
    protected $signature = 'cli {arg} {currency1?} {currency2?}';
    protected $description = 'App CLI functionalities';

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'arg' => 'Please enter \'help\' for instructions how to use this app',
        ];
    }

    protected function verifyArgument(?string $arg1, ?string $arg2)
    {
        $validation = validator(
            ['arg1' => $arg1, 'arg2' => $arg2],
            [
                'arg1' => 'required|min:3|max:10',
                'arg2' => 'required|min:3|max:10',
            ],
            [
                'required' => 'Please specify both tokens you wish to compare',
                'min' => 'Wrong token length',
                'max' => 'Wrong token length'
            ]
        );

        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $message) {
                $this->error($message);
            }
            die;
        }
    }

    protected function verifyCurrencyList(string $currency, array $list, TokenPrice $tokenPrice)
    {
        $checkCurrency = $tokenPrice->verifyCurrency($currency, $list);
        if (($checkCurrency['success']) === false) {
            $this->error($checkCurrency['error']);
            die;
        }
    }

    protected function handleListCommand(
        TokenPrice $tokenPrice,
        FavouriteToken $favouriteToken,
        ConsoleView $view
    ) {
        $list = $tokenPrice->getList();
        $view->printList($list);

        $favourites = implode(PHP_EOL, $favouriteToken->getFavouriteTokens());
        $view->printFavouriteTokens($favourites);
        if ($favourites === '') {
            $this->line('No favourite tokens added');
        }

        $addFavourite = $this->confirm('Do you wish to add tokens to favourites?');
        if ($addFavourite === true) {
            $addFavouriteTokens = $this->ask('Please enter the number in front of the tokens you want to add: ');
            $saveFavouriteTokenKeys = explode(',', str_replace(' ', '', $addFavouriteTokens));

            $saveUserTokens = [];
            foreach ($saveFavouriteTokenKeys as $key) {
                if (array_key_exists($key, $list) === false) {
                    $this->error('Error: You entered a wrong number. Marking token as favourite was not successful');
                    return;
                }
                $saveUserTokens[] = $list[$key];
            }

            try {
                $favouriteToken->insertFavouriteTokens($saveUserTokens);
                $this->info('Tokens added to Favourites');
            } catch (\Exception $e) {
                $this->error('Something went wrong, please try again.');
                return;
            }
        }
    }

    protected function handleDeleteCommand(
        FavouriteToken $favouriteToken,
    ) {
        $removeFavourite = $this->confirm('Do you wish to remove tokens from favourites?');
        if ($removeFavourite) {
            $removeFavouriteTokens = $this->ask('Please enter the tokens you want to remove: ');
            $removeFavouriteTokens = explode(',', str_replace(' ', '', strtoupper($removeFavouriteTokens)));

            $favourites = $favouriteToken->getFavouriteTokens();
            $tokenIsFavourite = $favouriteToken->verifyTokenFavourite($removeFavouriteTokens, $favourites);
            if ($tokenIsFavourite === false) {
                $this->error('Wrong token name, please try again');
                return;
            }

            try {
                $favouriteToken->deleteFavouriteTokens($removeFavouriteTokens);
                $this->info('Tokens removed from Favourites');
            } catch (\Exception $e) {
                $this->error('Something went wrong, please try again.');
                return;
            }
        }
    }

    protected function handlePriceCommand(
        string $currency1,
        string $currency2,
        TokenPrice $tokenPrice,
        ConsoleView $view
    ) {
        $list = $tokenPrice->getList();

        $this->verifyArgument($currency1, $currency2);
        $this->verifyCurrencyList($currency1, $list, $tokenPrice);
        $this->verifyCurrencyList($currency2, $list, $tokenPrice);

        $currentPrice = $tokenPrice->getCurrencyPair($currency1, $currency2);
        if ($currentPrice['success'] === false) {
            $this->error($currentPrice['error']);
        }
        if ($currentPrice['success'] === true) {
            $view->printPricePair($currentPrice['currency pair']);
        }
    }

    protected function handleAddUserCommand(
        User $user
    ) {
        $email = $this->ask('Please set new username: ');

        $validation = validator(['email' => $email], ['email' => 'required|email']);

        if ($validation->fails()) {
            $this->error('Username must be an email');
            return;
        }

        $password = $this->ask('Please set password: ');

        if (empty($password)) {
            $this->error('Password cannot be an empty string');
            return;
        }

        try {
            $user->createUser($email, $password);
            $this->info('New user registered successfully');
        } catch (\Exception $e) {
            $this->error('Something went wrong, please try again.');
            return;
        }
    }

    public function handle()
    {
        $arg = $this->argument('arg');
        $currency1 = $this->argument('currency1');
        $currency2 = $this->argument('currency2');

        $tokenPrice = new TokenPrice;
        $favouriteToken = new FavouriteToken;
        $user = new User;
        $view = new ConsoleView;

        switch ($arg) {
            case 'help':
                $this->line(
                    'Available commands:' . PHP_EOL .
                        '\'list\'           view all available currencies and mark favourites' . PHP_EOL .
                        '\'delete\'         remove favourite tokens from the list' . PHP_EOL .
                        '\'price\' BTC EUR  view price' . PHP_EOL .
                        '\'add user\'       register new user' . PHP_EOL
                );
                break;

            case 'list':
                $this->handleListCommand($tokenPrice, $favouriteToken, $view);
                break;

            case 'delete':
                $this->handleDeleteCommand($favouriteToken);
                break;

            case 'price':
                $this->handlePriceCommand($currency1, $currency2, $tokenPrice, $view);
                break;

            case 'add user':
                $this->handleAddUserCommand($user);
                break;

            default:
                $this->error('Please try again :)');
        }
    }
}
