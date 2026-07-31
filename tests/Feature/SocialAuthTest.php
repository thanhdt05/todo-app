<?php

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

dataset('providers', [
    'google' => ['google'],
    'microsoft' => ['microsoft'],
]);

test('redirects to provider oauth url', function (string $provider) {
    $response = get("/auth/{$provider}");

    $response->assertRedirect();
})->with('providers');

test('callback returns exchange_code and exchanging code returns token and user resource', function (string $provider) {
    $user = User::factory()->create([
        'email' => 'testuser@example.com',
    ]);

    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('provider-id-12345');
    $abstractUser->shouldReceive('getEmail')->andReturn('testuser@example.com');
    $abstractUser->shouldReceive('getName')->andReturn('Test User');
    $abstractUser->token = 'fake-access-token';
    $abstractUser->refreshToken = 'fake-refresh-token';
    $abstractUser->expiresIn = 3600;

    $driver = Mockery::mock(Provider::class);
    $driver->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with($provider)->andReturn($driver);

    $response = get("/auth/{$provider}/callback");

    $response->assertRedirect();
    $targetUrl = $response->getTargetUrl();
    $this->assertStringContainsString('http://localhost:5173/auth/callback?exchange_code=', $targetUrl);

    assertDatabaseHas('social_accounts', [
        'user_id' => $user->id,
        'provider' => $provider,
        'provider_id' => 'provider-id-12345',
        'provider_email' => 'testuser@example.com',
    ]);

    // Parse exchange code from URL query
    $query = parse_url($targetUrl, PHP_URL_QUERY);
    parse_str($query, $params);
    $exchangeCode = $params['exchange_code'] ?? null;

    expect($exchangeCode)->not->toBeNull()->and(strlen($exchangeCode))->toBe(40);

    // Exchange code for token
    $exchangeResponse = postJson('/api/auth/social/exchange', [
        'code' => $exchangeCode,
    ]);

    $exchangeResponse->assertOk()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'token',
                'user' => ['id', 'name', 'email'],
            ],
        ]);

    $exchangeResponse->assertJsonMissingPath('data.user.access_token');
    $exchangeResponse->assertJsonMissingPath('data.user.refresh_token');

    // Reusing the same exchange code must return 400
    $reuseResponse = postJson('/api/auth/social/exchange', [
        'code' => $exchangeCode,
    ]);

    $reuseResponse->assertStatus(400);
})->with('providers');

test('rejects exchange for invalid or expired code', function () {
    $response = postJson('/api/auth/social/exchange', [
        'code' => str_repeat('a', 40),
    ]);

    $response->assertStatus(400)
        ->assertJsonPath('message', 'Mã xác thực không hợp lệ hoặc đã hết hạn');
});

test('rejects social auth when user email does not exist in database', function (string $provider) {
    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('provider-id-99999');
    $abstractUser->shouldReceive('getEmail')->andReturn('notfound@example.com');
    $abstractUser->token = 'fake-token';

    $driver = Mockery::mock(Provider::class);
    $driver->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with($provider)->andReturn($driver);

    $response = get("/auth/{$provider}/callback");

    $response->assertRedirect(rtrim(config('app.frontend_url'), '/').'/login');
})->with('providers');

test('prevents linking social account if provider id is already linked to another user', function (string $provider) {
    $userA = User::factory()->create(['email' => 'userA@example.com']);
    $userB = User::factory()->create(['email' => 'userB@example.com']);

    SocialAccount::create([
        'user_id' => $userA->id,
        'provider' => $provider,
        'provider_id' => 'same-provider-id',
        'provider_email' => 'userA@example.com',
    ]);

    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('same-provider-id');
    $abstractUser->shouldReceive('getEmail')->andReturn('userB@example.com');
    $abstractUser->token = 'fake-token';

    $driver = Mockery::mock(Provider::class);
    $driver->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with($provider)->andReturn($driver);

    $response = get("/auth/{$provider}/callback");

    $response->assertRedirect(rtrim(config('app.frontend_url'), '/').'/login');
})->with('providers');

test('handles canceled or denied oauth grant gracefully', function (string $provider) {
    $response = get("/auth/{$provider}/callback?error=access_denied&error_description=The+user+canceled+the+request");

    $response->assertRedirectToRoute('login');
    $response->assertSessionHas('error', 'Bạn đã hủy đăng nhập bằng '.ucfirst($provider));
})->with('providers');
