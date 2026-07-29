<?php

namespace App\Services;

use App\Models\SocialAccount;
use App\Models\User;
use Exception;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialAuthService
{
    public function findOrLinkUser(string $provider, SocialiteUser $socialUser): User
    {
        $email = $socialUser->getEmail();
        $socialId = $socialUser->getId();

        if (! $socialId || ! $email) {
            throw new Exception(strtoupper($provider).' không trả về đủ thông tin tài khoản.');
        }

        $user = User::firstWhere('email', $email);

        if (! $user) {
            throw new Exception('Tài khoản không tồn tại.');
        }

        $existingSocialAccount = SocialAccount::firstWhere([
            'provider' => $provider,
            'provider_id' => $socialId,
        ]);

        if ($existingSocialAccount && $existingSocialAccount->user_id !== $user->id) {
            throw new Exception('Tài khoản '.strtoupper($provider).' này đã được liên kết với một người dùng khác.');
        }

        SocialAccount::updateOrCreate(
            [
                'user_id' => $user->id,
                'provider' => $provider,
            ],
            [
                'provider_id' => $socialId,
                'provider_email' => $email,
                'access_token' => $socialUser->token ?? null,
                'refresh_token' => $socialUser->refreshToken ?? null,
                'expires_at' => isset($socialUser->expiresIn) ? now()->addSeconds($socialUser->expiresIn) : null,
            ]
        );

        return $user;
    }
}
