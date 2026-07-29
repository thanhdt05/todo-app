<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\AbstractProvider;

class SocialAuthController extends Controller
{
    private const ALLOWED_PROVIDERS = ['microsoft', 'google'];

    public function __construct(
        private SocialAuthService $socialAuthService
    ) {}

    public function redirect(string $provider): RedirectResponse
    {
        $this->validateProvider($provider);
        /** @var AbstractProvider $driver */
        $driver = Socialite::driver($provider);

        if ($provider === 'microsoft') {
            $driver->scopes(['openid', 'profile', 'email', 'User.Read']);
        }

        return $driver->with(['prompt' => 'select_account'])->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        try {
            $this->validateProvider($provider);

            if (request()->has('error') || request()->has('error_description')) {
                return redirect()->route('login')->with('error', 'Bạn đã hủy đăng nhập bằng '.ucfirst($provider));
            }

            $socialUser = Socialite::driver($provider)->user();

            $user = $this->socialAuthService->findOrLinkUser($provider, $socialUser);

            Auth::login($user);

            $token = $user->createToken($provider.'-login')->plainTextToken;

            return redirect()->away("http://localhost:5173/auth/callback?token={$token}");
        } catch (\Exception $e) {
            Log::error($provider.' Lỗi xác thực: '.$e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route('login')->with('error', 'Không thể xác thực bằng '.ucfirst($provider).': '.$e->getMessage());
        }
    }

    private function validateProvider(string $provider): void
    {
        abort_unless(
            in_array($provider, self::ALLOWED_PROVIDERS, true),
            404,
            'Phương thức xác thực không được hỗ trợ.'
        );
    }
}
