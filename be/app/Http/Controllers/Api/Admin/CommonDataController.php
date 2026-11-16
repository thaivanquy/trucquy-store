<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Province;
use App\Models\Ward;
use Illuminate\Http\JsonResponse;

class CommonDataController extends Controller
{
    public function getProvinces(): JsonResponse
    {
        $provinces = Province::select('code', 'name', 'division_type')
            ->orderBy('name')
            ->get();

        return $this->successResponse($provinces, 'Lấy danh sách tỉnh/thành phố thành công.');
    }

    public function getWards(int $provinceCode): JsonResponse
    {
        $wards = Ward::where('province_code', $provinceCode)
            ->select('code', 'province_code', 'name', 'division_type')
            ->orderBy('name')
            ->get();

        return $this->successResponse($wards, 'Lấy danh sách phường/xã thành công.');
    }

    public function getBanks(): JsonResponse
    {
        $banks = Bank::select('id', 'name', 'code', 'bin', 'short_name', 'logo')
            ->orderBy('short_name')
            ->get();

        return $this->successResponse($banks, 'Lấy danh sách ngân hàng thành công.');
    }
}
