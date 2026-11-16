<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Unit\StoreUnitRequest;
use App\Http\Requests\Admin\Unit\UpdateUnitRequest;
use App\Services\Admin\UnitService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function __construct(
        protected UnitService $unitService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $units = $this->unitService->list($request->all());

        return $this->successResponse($units, 'Lấy danh sách đơn vị tính thành công.');
    }

    public function stats(): JsonResponse
    {
        $stats = $this->unitService->getStats();

        return $this->successResponse($stats, 'Lấy thống kê đơn vị tính thành công.');
    }

    public function show(int $unit): JsonResponse
    {
        $found = $this->unitService->find($unit);

        return $this->successResponse($found, 'Lấy thông tin đơn vị tính thành công.');
    }

    public function store(StoreUnitRequest $request): JsonResponse
    {
        $unit = $this->unitService->create($request->validated());

        return $this->successResponse($unit, 'Tạo đơn vị tính mới thành công.', 201);
    }

    public function update(UpdateUnitRequest $request, int $unit): JsonResponse
    {
        $found = $this->unitService->find($unit);
        $updated = $this->unitService->update($found, $request->validated());

        return $this->successResponse($updated, 'Cập nhật đơn vị tính thành công.');
    }

    public function destroy(int $unit): JsonResponse
    {
        $found = $this->unitService->find($unit);
        $this->unitService->delete($found);

        return $this->successResponse(null, 'Đã xóa đơn vị tính thành công.');
    }

    public function toggleStatus(int $unit): JsonResponse
    {
        $found = $this->unitService->find($unit);
        $updated = $this->unitService->toggleStatus($found);

        $statusText = $updated->is_active ? 'kích hoạt' : 'tạm dừng';

        return $this->successResponse($updated, "Đã {$statusText} đơn vị tính thành công.");
    }

    public function reorder(Request $request): JsonResponse
    {
        $items = $request->input('items', []);
        $this->unitService->reorder($items);

        return $this->successResponse(null, 'Cập nhật thứ tự đơn vị tính thành công.');
    }
}
