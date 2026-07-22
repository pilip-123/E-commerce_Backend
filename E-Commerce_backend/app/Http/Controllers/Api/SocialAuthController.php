<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class SocialAuthController extends Controller
{
    private const ALLOWED_PROVIDERS = ['google', 'facebook', 'github'];

    public function redirect(string $provider): JsonResponse
    {
        abort_if(!in_array($provider, self::ALLOWED_PROVIDERS), 404);

        $url = Socialite::driver($provider)
            ->stateless()
            ->redirectUrl(config("services.{$provider}.redirect"))
            ->redirect()
            ->getTargetUrl();

        $apiState = 'api_flow_' . Str::random(16);
        if (str_contains($url, 'state=')) {
            $url = preg_replace('/state=[^&]+/', 'state=' . urlencode($apiState), $url);
        } else {
            $separator = str_contains($url, '?') ? '&' : '?';
            $url .= $separator . 'state=' . urlencode($apiState);
        }

        return response()->json(['url' => $url]);
    }
}
