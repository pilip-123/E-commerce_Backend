<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private const ALLOWED_PROVIDERS = ['google', 'facebook', 'github'];

    public function redirect(string $provider): RedirectResponse
    {
        abort_if(!in_array($provider, self::ALLOWED_PROVIDERS), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        abort_if(!in_array($provider, self::ALLOWED_PROVIDERS), 404);

        Log::info('Social callback reached', ['provider' => $provider, 'params' => $request->all()]);

        $state = $request->input('state', '');
        $isApiFlow = str_starts_with($state, 'api_flow_');
        $errorUrl = $isApiFlow
            ? config('app.frontend_url', 'http://localhost:5173') . '?social_error='
            : null;

        try {
            $socialUser = Socialite::driver($provider)
                ->stateless()
                ->redirectUrl(config("services.{$provider}.redirect"))
                ->user();
        } catch (\Throwable $e) {
            Log::error('Social auth callback failed: ' . $e->getMessage(), [
                'provider' => $provider,
                'trace' => $e->getTraceAsString(),
            ]);
            if ($isApiFlow) {
                return redirect()->away($errorUrl . rawurlencode('Authentication with ' . $provider . ' failed. Please try again.'));
            }
            return redirect()->route('login')->withErrors(['email' => 'Unable to authenticate with ' . $provider . '.']);
        }

        $socialId = $socialUser->getId();
        $email = $socialUser->getEmail();
        $name = $socialUser->getName() ?? $socialUser->getNickname() ?? 'User';
        $avatar = $socialUser->getAvatar();

        if (!$email) {
            $email = 'social_' . $provider . '_' . $socialId . '@example.com';
        }

        $user = User::where('social_provider', $provider)
            ->where('social_id', $socialId)
            ->first();

        if (!$user) {
            $user = User::where('email', $email)->first();

            if ($user) {
                $user->update([
                    'social_id' => $socialId,
                    'social_provider' => $provider,
                    'social_avatar' => $avatar,
                ]);
            } else {
                try {
                    $user = User::create([
                        'name' => $name,
                        'email' => $email,
                        'password' => Hash::make(Str::random(32)),
                        'role' => 'customer',
                        'social_id' => $socialId,
                        'social_provider' => $provider,
                        'social_avatar' => $avatar,
                    ]);
                } catch (\Throwable $e) {
                    Log::error('Social user creation failed: ' . $e->getMessage(), [
                        'provider' => $provider,
                        'email' => $email,
                        'social_id' => $socialId,
                    ]);
                    if ($isApiFlow) {
                        return redirect()->away($errorUrl . rawurlencode('Failed to create account. Please try again.'));
                    }
                    return redirect()->route('register')->withErrors(['email' => 'Registration failed. Please try again.']);
                }
            }
        }

        if ($isApiFlow) {
            $token = Str::random(64);
            $cacheKey = 'api_token:' . hash('sha256', $token);
            \Illuminate\Support\Facades\Cache::put($cacheKey, $user->id, now()->addDays(7));

            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');
            $userPayload = $this->userPayload($user);
            $redirectUrl = $frontendUrl . '?social_token=' . $token . '&social_user=' . rawurlencode(json_encode($userPayload));

            return redirect()->away($redirectUrl);
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard');
    }

    private function userPayload(User $user): array
    {
        $imageUrl = $user->image_url
            ? rtrim(request()->getSchemeAndHttpHost(), '/') . Storage::url($user->image_url)
            : ($user->social_avatar ?: null);

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'phone' => $user->phone,
            'address' => $user->address,
            'image_url' => $imageUrl,
        ];
    }
}
