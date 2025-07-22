<?php

namespace App\Http\Controllers;

use Log;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::updateOrCreate(
                ['email' => $googleUser->getEmail()],
                [
                    'name'       => $googleUser->getName(),
                    'google_id'  => $googleUser->getId(),
                    'gender'     => 0,
                    'birthdate'  => null,
                    'password'   => bcrypt(Str::random(12)),
                ]
            );

            Auth::login($user);

            return redirect('/'); // نجاح

        } catch (\Exception $e) {

            return redirect('/login')->with('error', 'فشل تسجيل الدخول باستخدام جوجل.');
        }
    }
}
