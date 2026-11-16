<?php

namespace App\Http\Middleware\Admin;

use App\Support\ApiResponse;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Exceptions\JWTException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenBlacklistedException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenExpiredException;
use PHPOpenSourceSaver\JWTAuth\Exceptions\TokenInvalidException;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateAdminToken
{
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $user = auth('api')->authenticate();

            if (!$user) {
                return ApiResponse::error('Không tìm thấy tài khoản quản trị.', 404);
            }
        } catch (TokenBlacklistedException) {
            return ApiResponse::error('Token đã bị vô hiệu hoá do đã đăng xuất.', 401);
        } catch (TokenExpiredException) {
            return ApiResponse::error('Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.', 401);
        } catch (TokenInvalidException) {
            return ApiResponse::error('Token không hợp lệ.', 401);
        } catch (JWTException|AuthenticationException) {
            return ApiResponse::error('Thiếu hoặc sai định dạng token xác thực.', 401);
        } catch (\Throwable) {
            return ApiResponse::error('Chưa được xác thực hoặc phiên đăng nhập không hợp lệ.', 401);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
