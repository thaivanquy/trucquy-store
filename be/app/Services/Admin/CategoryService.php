<?php

namespace App\Services\Admin;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class CategoryService
{
    public function list(array $filters = []): LengthAwarePaginator|Collection
    {
        if (!empty($filters['tree']) && filter_var($filters['tree'], FILTER_VALIDATE_BOOLEAN)) {
            $query = Category::query()->whereNull('parent_id');

            if (!empty($filters['search'])) {
                $search = trim($filters['search']);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%")
                      ->orWhereHas('children', function ($cq) use ($search) {
                          $cq->where('name', 'like', "%{$search}%")
                             ->orWhere('code', 'like', "%{$search}%")
                             ->orWhere('slug', 'like', "%{$search}%");
                      });
                });
            }

            if (isset($filters['is_active']) && $filters['is_active'] !== '' && $filters['is_active'] !== null) {
                $active = filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN);
                $query->where(function ($q) use ($active) {
                    $q->where('is_active', $active)
                      ->orWhereHas('children', function ($cq) use ($active) {
                          $cq->where('is_active', $active);
                      });
                });
            }

            if (isset($filters['parent_id']) && $filters['parent_id'] !== '' && $filters['parent_id'] !== null) {
                if ($filters['parent_id'] !== 'root') {
                    $query->where('id', $filters['parent_id']);
                }
            }

            $query->with(['children' => function ($q) use ($filters) {
                if (!empty($filters['search'])) {
                    $search = trim($filters['search']);
                    $q->where(function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                           ->orWhere('code', 'like', "%{$search}%")
                           ->orWhere('slug', 'like', "%{$search}%");
                    });
                }
                if (isset($filters['is_active']) && $filters['is_active'] !== '' && $filters['is_active'] !== null) {
                    $q->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
                }
                $q->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
            }]);

            $sortBy = $filters['sort_by'] ?? 'sort_order';
            $sortDirection = strtolower($filters['sort_direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

            if (in_array($sortBy, ['sort_order', 'name', 'id', 'created_at', 'code'])) {
                $query->orderBy($sortBy, $sortDirection);
            } else {
                $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
            }

            if (!empty($filters['all']) && filter_var($filters['all'], FILTER_VALIDATE_BOOLEAN)) {
                return $query->get();
            }

            $perPage = (int) ($filters['per_page'] ?? 10);
            $perPage = max(1, min($perPage, 100));
            $page = isset($filters['page']) ? (int) $filters['page'] : null;

            return $query->paginate($perPage, ['*'], 'page', $page);
        }

        $query = Category::query()->with(['parent', 'children']);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '' && $filters['is_active'] !== null) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        if (isset($filters['parent_id']) && $filters['parent_id'] !== '' && $filters['parent_id'] !== null) {
            if ($filters['parent_id'] === 'root') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $filters['parent_id']);
            }
        }

        $sortBy = $filters['sort_by'] ?? 'sort_order';
        $sortDirection = strtolower($filters['sort_direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        if (in_array($sortBy, ['sort_order', 'name', 'id', 'created_at', 'code'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('sort_order', 'asc')->orderBy('id', 'desc');
        }

        if (!empty($filters['all']) && filter_var($filters['all'], FILTER_VALIDATE_BOOLEAN)) {
            return $query->get();
        }

        $perPage = (int) ($filters['per_page'] ?? 20);
        $perPage = max(1, min($perPage, 100));
        $page = isset($filters['page']) ? (int) $filters['page'] : null;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getStats(): array
    {
        return [
            'total' => Category::count(),
            'active_count' => Category::where('is_active', true)->count(),
            'inactive_count' => Category::where('is_active', false)->count(),
            'root_count' => Category::whereNull('parent_id')->count(),
            'sub_count' => Category::whereNotNull('parent_id')->count(),
        ];
    }

    public function find(int $id): Category
    {
        return Category::with(['parent', 'children'])->findOrFail($id);
    }

    public function create(array $data): Category
    {
        $data['name'] = trim($data['name']);
        if (empty($data['slug'])) {
            $data['slug'] = Category::generateUniqueSlug($data['name']);
        }
        if (empty($data['code'])) {
            $data['code'] = Category::generateCategoryCode($data['name'], $data['parent_id'] ?? null);
        }

        if (!empty($data['parent_id'])) {
            $parent = Category::find($data['parent_id']);
            if (!$parent) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Danh mục cha đã chọn không tồn tại.',
                ]);
            }
            if ($parent->parent_id !== null) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Danh mục cha phải là danh mục gốc, không được tạo danh mục cấp 3.',
                ]);
            }
        }

        return Category::create($data);
    }

    public function update(Category $category, array $data): Category
    {
        if (!empty($data['name'])) {
            $data['name'] = trim($data['name']);
        }

        if (array_key_exists('parent_id', $data)) {
            $newParentId = $data['parent_id'] !== '' && $data['parent_id'] !== null ? (int) $data['parent_id'] : null;

            if ($newParentId !== null && $newParentId === (int) $category->id) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Không thể chọn chính danh mục này làm danh mục cha.',
                ]);
            }

            if ($newParentId !== null && $category->children()->where('id', $newParentId)->exists()) {
                throw ValidationException::withMessages([
                    'parent_id' => 'Không thể chọn danh mục con của danh mục này làm danh mục cha (tránh tạo vòng lặp).',
                ]);
            }

            if ($category->parent_id === null && $newParentId !== null) {
                if ($category->children()->exists()) {
                    throw ValidationException::withMessages([
                        'parent_id' => 'Không thể chuyển danh mục cha thành danh mục con khi danh mục này đang chứa các danh mục con khác.',
                    ]);
                }
            }

            if ($newParentId !== null) {
                $newParent = Category::find($newParentId);
                if (!$newParent) {
                    throw ValidationException::withMessages([
                        'parent_id' => 'Danh mục cha đã chọn không tồn tại.',
                    ]);
                }
                if ($newParent->parent_id !== null) {
                    throw ValidationException::withMessages([
                        'parent_id' => 'Danh mục cha được chọn phải là danh mục gốc, không được tạo danh mục cấp 3.',
                    ]);
                }
            }

            $data['parent_id'] = $newParentId;
        }

        $category->update($data);

        return $category->fresh(['parent', 'children']);
    }

    public function delete(Category $category): bool
    {
        if ($category->children()->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Không thể xóa danh mục này vì vẫn còn danh mục con trực thuộc. Vui lòng xóa hoặc di chuyển các danh mục con trước.',
            ]);
        }

        if (Schema::hasTable('products') && DB::table('products')->where('category_id', $category->id)->whereNull('deleted_at')->exists()) {
            throw ValidationException::withMessages([
                'category' => 'Không thể xóa danh mục này vì đang có sản phẩm thuộc danh mục.',
            ]);
        }

        return (bool) $category->delete();
    }

    public function toggleStatus(Category $category): Category
    {
        $category->is_active = !$category->is_active;
        $category->save();

        return $category;
    }

    public function reorder(array $items): bool
    {
        foreach ($items as $item) {
            if (isset($item['id'], $item['sort_order'])) {
                Category::where('id', $item['id'])->update(['sort_order' => (int) $item['sort_order']]);
            }
        }
        return true;
    }
}
