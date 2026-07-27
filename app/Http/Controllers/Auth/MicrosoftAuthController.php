<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftAuthController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('microsoft')
            ->scopes(['openid', 'profile', 'email', 'User.Read'])
            ->redirect();
    }

    public function callback(): RedirectResponse
    {
        try {
            
        
        $microsoftUser = Socialite::driver('microsoft')->user();

        $user = User::where('microsoft_id', $microsoftUser->id)
            ->orWhere('email', $microsoftUser->email)
            ->first();

        if ($user) {
            $user->update([
                'microsoft_id' => $microsoftUser->id,
                'microsoft_token' => $microsoftUser->token,
                'microsoft_refresh_token' => $microsoftUser->refreshToken,
            ]);
        } else {
            return to_route('login')->with('error', 'User not found');
        }

        Auth::login($user);

        return redirect()->intended('dashboard');
        } catch (\Exception $e) {
            return redirect('login')->with('error', 'Unable to login with Microsoft. Please try again.');
        }
    }
}
