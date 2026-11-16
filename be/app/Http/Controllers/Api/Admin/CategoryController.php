<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Category\StoreCategoryRequest;
use App\Http\Requests\Admin\Category\UpdateCategoryRequest;
use App\Services\Admin\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->list($request->all());

        return $this->successResponse($categories, 'Lấy danh sách danh mục thành công.');
    }

    public function stats(): JsonResponse
    {
        $stats = $this->categoryService->getStats();

        return $this->successResponse($stats, 'Lấy thống kê danh mục thành công.');
    }

    public function show(int $category): JsonResponse
    {
        $found = $this->categoryService->find($category);

        return $this->successResponse($found, 'Lấy thông tin danh mục thành công.');
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return $this->successResponse($category, 'Tạo danh mục mới thành công.', 201);
    }

    public function update(UpdateCategoryRequest $request, int $category): JsonResponse
    {
        $found = $this->categoryService->find($category);
        $updated = $this->categoryService->update($found, $request->validated());

        return $this->successResponse($updated, 'Cập nhật danh mục thành công.');
    }

    public function destroy(int $category): JsonResponse
    {
        $found = $this->categoryService->find($category);
        $this->categoryService->delete($found);

        return $this->successResponse(null, 'Đã xóa danh mục thành công.');
    }

    public function toggleStatus(int $category): JsonResponse
    {
        $found = $this->categoryService->find($category);
        $updated = $this->categoryService->toggleStatus($found);

        $statusText = $updated->is_active ? 'kích hoạt' : 'tạm dừng';

        return $this->successResponse($updated, "Đã {$statusText} danh mục thành công.");
    }

    public function reorder(Request $request): JsonResponse
    {
        $items = $request->input('items', []);
        $this->categoryService->reorder($items);

        return $this->successResponse(null, 'Cập nhật thứ tự danh mục thành công.');
    }
}
