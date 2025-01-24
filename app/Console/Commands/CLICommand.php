<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Contracts\Console\PromptsForMissingInput;
use Illuminate\Support\Facades\Auth;
use App\Models\TokenPrice;
use App\Models\User;

class CLICommand extends Command implements PromptsForMissingInput
{
    public function handle()
    {
        $arg = $this->argument('arg');
        $currency1 = $this->argument('currency1');
        $currency2 = $this->argument('currency2');

        $id = 0;
        Auth::loginUsingId($id);

        $tokenPrice = new TokenPrice;
        $user = new User;

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
                $this->handleListCommand($tokenPrice, $id);
                break;

            case 'delete':
                $this->handleDeleteCommand($id);
                break;

            case 'price':
                $this->handlePriceCommand($currency1, $currency2, $tokenPrice);
                break;

            case 'add user':
                $this->handleAddUserCommand($user);
                break;

            default:
                $this->error('Please try again :)');
        }
    }

    protected function promptForMissingArgumentsUsing(): array
    {
        return [
            'arg' => 'Please enter \'help\' for instructions on how to use this app',
        ];
    }

    protected function verifyArgument(?string $arg1, ?string $arg2)
    {
        $validation = validator(
            ['arg1' => $arg1, 'arg2' => $arg2],
            [
                'arg1' => ['required', 'min:3', 'max:10'],
                'arg2' => ['required', 'min:3', 'max:10'],
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
        int $id
    ) {
        $list = $tokenPrice->getList();
        foreach ($list as $key => $token) {
            $this->line($key . ' ' . $token);
        }

        $getFavourites = User::find($id)->favourites()->orderBy('token_name', 'asc')->get();
        $listFavourites = implode(PHP_EOL, $getFavourites->pluck('token_name')->toArray());

        $this->newLine();
        $this->line('This are your favourite tokens:' . PHP_EOL . $listFavourites);

        if ($listFavourites === '') {
            $this->line('No favourite tokens added');
        }

        $addFavourite = $this->confirm('Do you wish to add tokens to favourites?');
        if ($addFavourite === true) {
            $addFavouriteTokens = $this->ask('Please enter the number in front of the tokens you want to add: ');
            $saveFavouriteTokenKeys = explode(',', str_replace(' ', '', $addFavouriteTokens));

            $saveUserTokens = [];
            foreach ($saveFavouriteTokenKeys as $key) {
                if (array_key_exists($key, $list) === false) {
                    $this->error('You entered a wrong number. Marking token as favourite was not successful');
                    return;
                }
                $saveUserTokens[] = $list[$key];
            }

            try {
                foreach ($saveUserTokens as $token) {
                    User::find($id)->favourites()->updateOrInsert(['user_id' => $id, 'token_name' => $token], []);
                }
                $this->info('Tokens added to Favourites');
            } catch (\Exception $e) {
                $this->error('Something went wrong, please try again.');
                return;
            }
        }
    }

    protected function handleDeleteCommand(int $id)
    {
        $removeFavourite = $this->confirm('Do you wish to remove tokens from favourites?');
        if ($removeFavourite) {
            $removeFavouriteTokens = $this->ask('Please enter the tokens you want to remove: ');
            $removeFavouriteTokens = explode(',', str_replace(' ', '', strtoupper($removeFavouriteTokens)));

            $getFavourites = User::find($id)->favourites()->get();
            $arrayFavourites = $getFavourites->pluck('token_name')->toArray();

            foreach ($removeFavouriteTokens as $token) {
                if (!in_array($token, $arrayFavourites)) {
                    $this->error('Wrong token name, please try again');
                    die;
                };
            }

            try {
                foreach ($removeFavouriteTokens as $token) {
                    User::find($id)->favourites()->where('token_name', $token)->delete();
                }
                $this->info('Tokens removed from Favourites');
            } catch (\Exception $e) {
                $this->error('Something went wrong, please try again.');
                return;
            }
        }
    }

    protected function handlePriceCommand(
        ?string $currency1,
        ?string $currency2,
        TokenPrice $tokenPrice
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
            $this->info(sprintf('%s: %.2f %s', $currentPrice['currency pair']['data']['base'], $currentPrice['currency pair']['data']['amount'], $currentPrice['currency pair']['data']['currency']));
        }
    }

    protected function handleAddUserCommand()
    {
        $email = $this->ask('Please set new username: ');

        $validation = validator(['email' => $email], ['email' => ['required', 'email']], ['required' => 'Please set a Username', 'email' => 'Username must be an email']);

        if ($validation->fails()) {
            foreach ($validation->errors()->all() as $message) {
                $this->error($message);
            }
            return;
        }

        $password = $this->ask('Please set password: ');

        if (empty($password)) {
            $this->error('Password cannot be an empty string');
            return;
        }

        try {
            User::create(['email' => $email, 'password' => $password]);
            $this->info('New user registered successfully');
        } catch (\Exception $e) {
            $this->error('Something went wrong, please try again.');
            return;
        }
    }

    protected $signature = 'cli {arg} {currency1?} {currency2?}';
    protected $description = 'App CLI functionalities';
}
