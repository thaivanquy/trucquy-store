<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Supplier\StoreSupplierRequest;
use App\Http\Requests\Admin\Supplier\UpdateSupplierRequest;
use App\Services\Admin\SupplierService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function __construct(
        protected SupplierService $supplierService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $suppliers = $this->supplierService->list($request->all());

        return $this->successResponse($suppliers, 'Lấy danh sách nhà cung cấp thành công.');
    }

    public function show(int $supplier): JsonResponse
    {
        $found = $this->supplierService->find($supplier);

        return $this->successResponse($found, 'Lấy thông tin nhà cung cấp thành công.');
    }

    public function store(StoreSupplierRequest $request): JsonResponse
    {
        $supplier = $this->supplierService->create($request->validated());

        return $this->successResponse($supplier, 'Tạo nhà cung cấp mới thành công.', 201);
    }

    public function update(UpdateSupplierRequest $request, int $supplier): JsonResponse
    {
        $found = $this->supplierService->find($supplier);
        $updated = $this->supplierService->update($found, $request->validated());

        return $this->successResponse($updated, 'Cập nhật nhà cung cấp thành công.');
    }

    public function destroy(int $supplier): JsonResponse
    {
        $found = $this->supplierService->find($supplier);
        $this->supplierService->delete($found);

        return $this->successResponse(null, 'Đã xóa nhà cung cấp thành công.');
    }

    public function toggleStatus(int $supplier): JsonResponse
    {
        $found = $this->supplierService->find($supplier);
        $updated = $this->supplierService->toggleStatus($found);

        $statusText = $updated->is_active ? 'kích hoạt' : 'tạm dừng';

        return $this->successResponse($updated, "Đã {$statusText} nhà cung cấp thành công.");
    }
}
