<?php

use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Contracts\Provider;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;

uses(RefreshDatabase::class);

dataset('providers', [
    'google' => ['google'],
    'microsoft' => ['microsoft'],
]);

test('redirects to provider oauth url', function (string $provider) {
    $response = get("/auth/{$provider}");

    $response->assertRedirect();
})->with('providers');

test('authenticates user via social provider when email exists in database', function (string $provider) {
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
    $this->assertStringContainsString('http://localhost:5173/auth/callback?token=', $response->getTargetUrl());

    assertDatabaseHas('social_accounts', [
        'user_id' => $user->id,
        'provider' => $provider,
        'provider_id' => 'provider-id-12345',
        'provider_email' => 'testuser@example.com',
    ]);
})->with('providers');

test('rejects social auth when user email does not exist in database', function (string $provider) {
    $abstractUser = Mockery::mock(SocialiteUser::class);
    $abstractUser->shouldReceive('getId')->andReturn('provider-id-99999');
    $abstractUser->shouldReceive('getEmail')->andReturn('notfound@example.com');
    $abstractUser->token = 'fake-token';

    $driver = Mockery::mock(Provider::class);
    $driver->shouldReceive('user')->andReturn($abstractUser);

    Socialite::shouldReceive('driver')->with($provider)->andReturn($driver);

    $response = get("/auth/{$provider}/callback");

    $response->assertRedirectToRoute('login');
    $response->assertSessionHas('error', 'Không thể xác thực bằng '.ucfirst($provider).': Tài khoản không tồn tại.');
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

    $response->assertRedirectToRoute('login');
    $response->assertSessionHas('error');
})->with('providers');

test('handles canceled or denied oauth grant gracefully', function (string $provider) {
    $response = get("/auth/{$provider}/callback?error=access_denied&error_description=The+user+canceled+the+request");

    $response->assertRedirectToRoute('login');
    $response->assertSessionHas('error', 'Bạn đã hủy đăng nhập bằng '.ucfirst($provider));
})->with('providers');
