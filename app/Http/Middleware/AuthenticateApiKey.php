<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken() ?: $request->header('X-API-Key');

        if (! is_string($token) || $token === '') {
            return response()->json([
                'message' => __('Unauthenticated.'),
            ], 401);
        }

        /** @var ?User $user */
        $user = User::query()
            ->where('api_key_hash', hash('sha256', $token))
            ->first();

        if ($user === null) {
            return response()->json([
                'message' => __('Unauthenticated.'),
            ], 401);
        }

        Auth::setUser($user);
        $request->setUserResolver(fn (): User => $user);

        return $next($request);
    }
}
