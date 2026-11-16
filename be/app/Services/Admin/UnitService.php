<?php

namespace App\Services\Admin;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class UnitService
{
    public function list(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = Unit::query();

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '' && $filters['is_active'] !== null) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        $sortBy = $filters['sort_by'] ?? 'sort_order';
        $sortDirection = strtolower($filters['sort_direction'] ?? 'asc') === 'desc' ? 'desc' : 'asc';

        if (in_array($sortBy, ['sort_order', 'name', 'id', 'created_at', 'code'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
        }

        if (!empty($filters['all']) && filter_var($filters['all'], FILTER_VALIDATE_BOOLEAN)) {
            return $query->get();
        }

        $perPage = (int) ($filters['per_page'] ?? 10);
        $perPage = max(1, min($perPage, 100));
        $page = isset($filters['page']) ? (int) $filters['page'] : null;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function getStats(): array
    {
        return [
            'total' => Unit::count(),
            'active_count' => Unit::where('is_active', true)->count(),
            'inactive_count' => Unit::where('is_active', false)->count(),
        ];
    }

    public function find(int $id): Unit
    {
        return Unit::findOrFail($id);
    }

    public function create(array $data): Unit
    {
        $data['name'] = trim($data['name']);

        if (empty($data['code'])) {
            $data['code'] = Unit::generateUnitCode();
        }

        return Unit::create($data);
    }

    public function update(Unit $unit, array $data): Unit
    {
        if (!empty($data['name'])) {
            $data['name'] = trim($data['name']);
        }

        $unit->update($data);

        return $unit->fresh();
    }

    public function delete(Unit $unit): bool
    {
        if (Schema::hasTable('products') && Schema::hasColumn('products', 'unit_id')) {
            if (DB::table('products')->where('unit_id', $unit->id)->whereNull('deleted_at')->exists()) {
                throw ValidationException::withMessages([
                    'unit' => 'Không thể xóa đơn vị tính này vì đang có sản phẩm đang sử dụng.',
                ]);
            }
        }

        return (bool) $unit->delete();
    }

    public function toggleStatus(Unit $unit): Unit
    {
        $unit->is_active = !$unit->is_active;
        $unit->save();

        return $unit;
    }

    public function reorder(array $items): bool
    {
        foreach ($items as $item) {
            if (isset($item['id'], $item['sort_order'])) {
                Unit::where('id', $item['id'])->update(['sort_order' => (int) $item['sort_order']]);
            }
        }

        return true;
    }
}
