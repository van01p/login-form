<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $this->wantsJson($request)
                ? response()->json(['message' => 'Belum login'], 401)
                : redirect()->route('login');
        }

        if ($user->status !== 'active') {
            if ($this->wantsJson($request)) {
                return response()->json(['message' => 'Akun tidak aktif'], 403);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors(['email' => 'Akun tidak aktif']);
        }

        return $next($request);
    }

    private function wantsJson(Request $request): bool
    {
        return $request->is('api/*') || $request->expectsJson();
    }
}