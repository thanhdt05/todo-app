<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\SocialAuthService;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use SocialiteProviders\Manager\OAuth2\AbstractProvider;

class SocialAuthController extends Controller
{
    use HttpResponse;

    private const ALLOWED_PROVIDERS = ['microsoft', 'google'];

    /**
     * Create a new controller instance.
     */
    public function __construct(
        private SocialAuthService $socialAuthService
    ) {}

    /**
     * Redirect the user to the OAuth provider authentication page.
     */
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

    /**
     * Handle the OAuth provider callback and issue a one-time exchange code.
     */
    public function callback(Request $request, string $provider): RedirectResponse
    {
        $this->validateProvider($provider);

        if ($request->hasAny(['error', 'error_description'])) {
            return redirect()->route('login')->with('error', 'Bạn đã hủy đăng nhập bằng '.ucfirst($provider));
        }

        try {
            $exchangeCode = $this->socialAuthService->handleCallback($provider);

            $frontendUrl = rtrim(config('app.frontend_url'), '/');

            return redirect()->away("{$frontendUrl}/auth/callback?exchange_code=".rawurlencode($exchangeCode));
        } catch (\Exception $e) {
            Log::error("Social authentication failed [{$provider}]: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            return redirect()->route('login')->with(
                'error',
                'Không thể đăng nhập bằng '.ucfirst($provider).'. Vui lòng thử lại sau.'
            );
        }
    }

    /**
     * Exchange a valid one-time code for a Sanctum access token.
     */
    public function exchange(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'size:40'],
        ]);

        $result = $this->socialAuthService->exchangeCode($validated['code']);

        if (! $result) {
            return $this->error('Mã xác thực không hợp lệ hoặc đã hết hạn', 400);
        }

        return $this->success(
            $result,
            'Đăng nhập thành công',
            200
        );
    }

    /**
     * Validate whether the social authentication provider is supported.
     */
    private function validateProvider(string $provider): void
    {
        abort_unless(
            in_array($provider, self::ALLOWED_PROVIDERS, true),
            404,
            'Phương thức xác thực không được hỗ trợ.'
        );
    }
}
