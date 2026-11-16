<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use App\Services\Admin\AuthService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $authService
    ) {
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->attemptLogin(
            $request->validated('email'),
            $request->validated('password'),
            (bool) $request->boolean('remember')
        );

        return $this->successResponse([
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
            'user' => $result['user'],
        ], 'Đăng nhập thành công.');
    }

    public function logout(): JsonResponse
    {
        $this->authService->logout();

        return $this->successResponse(null, 'Đăng xuất thành công.');
    }

    public function refresh(): JsonResponse
    {
        $result = $this->authService->refresh();

        return $this->successResponse([
            'token' => $result['token'],
            'token_type' => $result['token_type'],
            'expires_in' => $result['expires_in'],
            'user' => $result['user'],
        ], 'Làm mới token thành công.');
    }

    public function me(): JsonResponse
    {
        return $this->successResponse([
            'user' => $this->authService->me(),
        ]);
    }
}
