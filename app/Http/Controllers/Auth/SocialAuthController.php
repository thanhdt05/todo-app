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
    protected $allowedProviders = ['microsoft', 'google'];

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

        return $driver->redirect();
    }

    public function callback(string $provider): RedirectResponse
    {
        try {
            $this->validateProvider($provider);
            $socialUser = Socialite::driver($provider)->user();

            $user = $this->socialAuthService->findUser($provider, $socialUser);

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
        if (! in_array($provider, $this->allowedProviders)) {
            throw new \Exception('Phương thức xác thực không được hỗ trợ');
        }
    }
}
