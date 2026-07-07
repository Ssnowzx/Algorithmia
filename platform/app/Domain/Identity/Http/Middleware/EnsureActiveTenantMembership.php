<?php

declare(strict_types=1);

namespace App\Domain\Identity\Http\Middleware;

use App\Domain\Identity\Models\User;
use App\Domain\Tenancy\Models\TenantMembership;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

final class EnsureActiveTenantMembership
{
    public function handle(Request $request, Closure $next): Response|JsonResponse
    {
        $user = $request->user();

        if (! $user instanceof User) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        /** @var string $userId */
        $userId = (string) $user->getAuthIdentifier();

        $membershipExists = TenantMembership::query()
            ->where('user_id', $userId)
            ->where('status', 'active')
            ->exists();

        if (! $membershipExists) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json(['message' => 'Forbidden.'], 403);
        }

        return $next($request);
    }
}
