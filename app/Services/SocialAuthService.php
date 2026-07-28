<?php

namespace App\Services;

use App\Models\User;
use Exception;
use Laravel\Socialite\Contracts\User as SocialiteUser;

class SocialAuthService
{
    public function findUser(string $provider, SocialiteUser $socialUser): User
    {
        $email = $socialUser->getEmail();
        $socialId = $socialUser->getId();

        $providerId = "{$provider}_id";
        $providerToken = "{$provider}_token";
        $providerRefreshToken = "{$provider}_refresh_token";

        if (! $socialId || ! $email) {
            throw new Exception(strtoupper($provider).' không trả về đủ thông tin tài khoản.');
        }

        $user = User::query()
            ->where($providerId, $socialId)
            ->orWhere('email', $email)
            ->first();

        if (! $user) {
            throw new Exception(strtoupper($provider).' Tài khoản không tồn tại.');
        }

        if ($user->$providerId !== null && $socialId !== $user->$providerId) {
            throw new Exception('Tài khoản đã được liên kết với tài khoản khác.');
        }

        $user->$providerId = $socialId;
        $user->$providerToken = $socialUser->token ?? null;
        $user->$providerRefreshToken = $socialUser->refreshToken ?? null;
        $user->save();

        return $user;
    }
}
