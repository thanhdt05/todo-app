<?php

namespace App\Services;

use App\Models\SocialAccount;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Cache;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Socialite;

class SocialAuthService
{
    private const EXCHANGE_CODE_TTL_SECONDS = 60;

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

    public function handleCallback(string $provider): string
    {
        $socialUser = Socialite::driver($provider)->user();

        $user = $this->findOrLinkUser(
            $provider,
            $socialUser
        );

        return $this->createExchangeCode($user);
    }

    public function exchangeCode(string $code): ?array
    {
        $cacheKey = "social_exchange_{$code}";

        $userId = Cache::pull($cacheKey);
        if (! $userId) {
            return null;
        }

        $user = User::find($userId);

        if (! $user) {
            return null;
        }

        return [
            'token' => $user->createToken('social-login')->plainTextToken,
            'user' => $user,
        ];
    }

    private function createExchangeCode(User $user): string
    {
        $code = str()->random(40);

        Cache::put(
            "social_exchange_{$code}",
            $user->id,
            now()->addSeconds(self::EXCHANGE_CODE_TTL_SECONDS)
        );

        return $code;
    }
}
