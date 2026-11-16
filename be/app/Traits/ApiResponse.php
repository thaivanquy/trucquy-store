<?php

namespace App\Traits;

use App\Support\ApiResponse as ApiResponder;
use Illuminate\Http\JsonResponse;

trait ApiResponse
{
    public function successResponse(mixed $data = null, string $message = 'Thành công.', int $code = 200): JsonResponse
    {
        return ApiResponder::success($data, $message, $code);
    }

    public function errorResponse(string $message = 'Đã có lỗi xảy ra.', int $code = 400, mixed $errors = null): JsonResponse
    {
        return ApiResponder::error($message, $code, $errors);
    }

    public static function makeSuccess(mixed $data = null, string $message = 'Thành công.', int $code = 200): JsonResponse
    {
        return ApiResponder::success($data, $message, $code);
    }

    public static function makeError(string $message = 'Đã có lỗi xảy ra.', int $code = 400, mixed $errors = null): JsonResponse
    {
        return ApiResponder::error($message, $code, $errors);
    }
}
