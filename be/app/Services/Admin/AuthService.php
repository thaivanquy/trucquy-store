<?php

namespace App\Services\Admin;

use App\Models\User;
use App\Traits\Singleton;
use Illuminate\Validation\ValidationException;

class AuthService
{
    use Singleton;

    public function attemptLogin(string $login, string $password, bool $remember = false): array
    {
        $ttl = $remember ? (60 * 24 * 30) : (int) config('jwt.ttl', 60);
        auth('api')->setTTL($ttl);

        $loginField = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        if (!$token = auth('api')->attempt([$loginField => $login, 'password' => $password])) {
            throw ValidationException::withMessages([
                'email' => ['Thông tin tài khoản hoặc mật khẩu không chính xác.'],
            ]);
        }

        $user = auth('api')->user();

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];
    }

    public function logout(): bool
    {
        try {
            auth('api')->logout();
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function refresh(): array
    {
        $token = auth('api')->refresh();
        $user = auth('api')->user();

        return [
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];
    }

    public function me(): ?User
    {
        return auth('api')->user();
    }
}
