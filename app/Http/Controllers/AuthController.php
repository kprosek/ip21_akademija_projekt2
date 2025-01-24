<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthController
{
    public function login()
    {
        return view('login');
    }

    public function loginProcess(Request $request)
    {
        $rateLimitKey = 'login:' . $request->ip();

        if (RateLimiter::tooManyAttempts($rateLimitKey, 3)) {
            return back()->withInput()->withErrors([
                'error' => 'To many login attempts. Try again in 60s.',
            ]);
        }

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (Auth::attempt($credentials)) {
            RateLimiter::clear($rateLimitKey);

            $user = Auth::user();

            $request->session()->regenerate();
            $request->session()->put('id', $user->id);
            $request->session()->put('email', $user->email);

            return redirect()->route('index');
        }

        RateLimiter::increment($rateLimitKey);

        return back()->withInput()->withErrors([
            'error' => 'The provided credentials do not match our records.',
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('index');
    }
}
