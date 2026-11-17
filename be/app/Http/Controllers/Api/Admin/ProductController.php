<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\StoreProductRequest;
use App\Http\Requests\Admin\Product\UpdateProductRequest;
use App\Services\Admin\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $products = $this->productService->list($request->all());

        return $this->successResponse($products, 'Lấy danh sách sản phẩm thành công.');
    }

    public function stats(): JsonResponse
    {
        $stats = $this->productService->getStats();

        return $this->successResponse($stats, 'Lấy thống kê sản phẩm thành công.');
    }

    public function show(int $product): JsonResponse
    {
        $found = $this->productService->find($product);

        return $this->successResponse($found, 'Lấy thông tin sản phẩm thành công.');
    }

    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return $this->successResponse($product, 'Tạo sản phẩm mới thành công.', 201);
    }

    public function update(UpdateProductRequest $request, int $product): JsonResponse
    {
        $found = $this->productService->find($product);
        $updated = $this->productService->update($found, $request->validated());

        return $this->successResponse($updated, 'Cập nhật sản phẩm thành công.');
    }

    public function destroy(int $product): JsonResponse
    {
        $found = $this->productService->find($product);
        $this->productService->delete($found);

        return $this->successResponse(null, 'Đã xóa sản phẩm thành công.');
    }

    public function toggleStatus(int $product): JsonResponse
    {
        $found = $this->productService->find($product);
        $updated = $this->productService->toggleStatus($found);

        $statusText = $updated->is_active ? 'kích hoạt kinh doanh' : 'tạm dừng kinh doanh';

        return $this->successResponse($updated, "Đã {$statusText} sản phẩm thành công.");
    }

    public function reorder(Request $request): JsonResponse
    {
        $items = $request->input('items', []);
        $this->productService->reorder($items);

        return $this->successResponse(null, 'Cập nhật thứ tự sản phẩm thành công.');
    }
}
