<?php

declare(strict_types=1);

namespace App\Domain\Identity\Http\Controllers;

use App\Domain\Identity\Models\User;
use App\Domain\Tenancy\Models\TenantMembership;
use Illuminate\Auth\AuthManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

final class TenantAuthenticationController
{
    public function __construct(
        private readonly AuthManager $auth,
    ) {}

    public function create(Request $request): Response|JsonResponse|RedirectResponse
    {
        if ($request->user() instanceof User) {
            return $this->successResponse($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
            ]);
        }

        $action = url('/login');
        $token = csrf_token();

        return response(sprintf(
            '<!doctype html><html lang="pt-BR"><body><form method="post" action="%s"><input type="hidden" name="_token" value="%s"><label>E-mail <input type="email" name="email" autocomplete="email" required></label><label>Senha <input type="password" name="password" autocomplete="current-password" required></label><button type="submit">Entrar</button></form></body></html>',
            e($action),
            e($token),
        ))
            ->header('Content-Type', 'text/html; charset=UTF-8');
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email:rfc'],
            'password' => ['required', 'string'],
        ]);

        $normalizedEmail = mb_strtolower(trim($validated['email']));
        $password = $validated['password'];

        /** @var User|null $user */
        $user = User::query()
            ->whereRaw('lower(email) = ?', [$normalizedEmail])
            ->first();

        if (
            ! $user instanceof User
            || ! Hash::check($password, $user->password)
            || ! $this->membershipIsActive($user)
        ) {
            return $this->failedLoginResponse($request);
        }

        $this->auth->guard()->login($user);
        $request->session()->regenerate();

        return $this->successResponse($request);
    }

    public function destroy(Request $request): JsonResponse|RedirectResponse
    {
        $this->auth->guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $this->logoutSuccessResponse($request);
    }

    private function membershipIsActive(User $user): bool
    {
        return TenantMembership::query()
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->exists();
    }

    private function failedLoginResponse(Request $request): JsonResponse|RedirectResponse
    {
        $message = 'As credenciais informadas são inválidas.';

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $message,
            ], 422);
        }

        throw ValidationException::withMessages([
            'email' => [$message],
        ]);
    }

    private function successResponse(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
            ]);
        }

        return redirect()->to('/__tenant/auth-context');
    }

    private function logoutSuccessResponse(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'ok',
            ]);
        }

        return redirect()->to('/login');
    }
}
