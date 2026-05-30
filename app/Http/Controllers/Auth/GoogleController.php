<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback(): RedirectResponse
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::query()
            ->where('provider', 'google')
            ->where('provider_id', $googleUser->getId())
            ->first();

        if (!$user) {
            $user = User::query()
                ->where('email', $googleUser->getEmail())
                ->first();
        }

        if ($user) {
            $user->update([
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
                'last_login_at' => now(),
                'email_verified_at' => $user->email_verified_at ?? now(),
            ]);
        } else {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Pengguna MARIS',
                'email' => $googleUser->getEmail(),
                'password' => Hash::make(Str::random(32)),
                'role' => User::ROLE_ANALYST,
                'provider' => 'google',
                'provider_id' => $googleUser->getId(),
                'avatar_url' => $googleUser->getAvatar(),
                'last_login_at' => now(),
                'email_verified_at' => now(),
            ]);
        }

        Auth::login($user, true);

        return redirect()->route('dashboard');
    }
}
