<?php

namespace App\Http\Controllers\Front\Auth;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class GoogleLogin
{
    private string $name = 'google';
    public function login()
    {
        return $this->getDriver()->redirect();
    }

    public function callback(): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        $user = $this->getDriver()->user();
        // Обробка користувача, отриманого з Google

        // Приклад: Зареєструвати або авторизувати користувача
        $existingUser = User::where('email', $user->getEmail())->first();

        if ($existingUser) {
            // Авторизація існуючого користувача
            auth()->login($existingUser);
        } else {
            // Реєстрація нового користувача
            $newUser = new User();
            $newUser->name = $user->getName();
            $newUser->email = $user->getEmail();
            $newUser->save();

            auth()->login($newUser);
        }
        return redirect('/checkout');
    }

    public function getDriver(): \Laravel\Socialite\Contracts\Provider
    {
        return Socialite::driver($this->name);
    }
}
