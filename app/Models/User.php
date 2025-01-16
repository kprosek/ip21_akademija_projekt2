<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class User extends Model
{
    protected $table = 'users';

    protected $fillable = ['email', 'password'];

    public $timestamps = false;

    public function createUser(string $username, string $password)
    {
        $passwordHashed = Hash::make($password);

        User::create([
            'email' => $username,
            'password' => $passwordHashed,
        ]);
    }
}
