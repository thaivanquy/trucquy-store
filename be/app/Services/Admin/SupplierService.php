<?php

namespace App\Services\Admin;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SupplierService
{
    public function list(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = Supplier::query()->with(['province:code,name', 'ward:code,name']);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('contact_person', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '' && $filters['is_active'] !== null) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDirection = strtolower($filters['sort_direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        if (in_array($sortBy, ['name', 'id', 'created_at', 'code'])) {
            $query->orderBy($sortBy, $sortDirection);
        } else {
            $query->orderBy('id', 'desc');
        }

        if (!empty($filters['all']) && filter_var($filters['all'], FILTER_VALIDATE_BOOLEAN)) {
            return $query->get();
        }

        $perPage = (int) ($filters['per_page'] ?? 20);
        $perPage = max(1, min($perPage, 100));
        $page = isset($filters['page']) ? (int) $filters['page'] : null;

        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    public function find(int $id): Supplier
    {
        return Supplier::with(['province:code,name', 'ward:code,name'])->findOrFail($id);
    }

    public function create(array $data): Supplier
    {
        $data['name'] = trim($data['name']);

        if (empty($data['code'])) {
            $data['code'] = Supplier::generateSupplierCode();
        }

        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        if (!empty($data['name'])) {
            $data['name'] = trim($data['name']);
        }

        $supplier->update($data);

        return $supplier->fresh();
    }

    public function delete(Supplier $supplier): bool
    {
        return (bool) $supplier->delete();
    }

    public function toggleStatus(Supplier $supplier): Supplier
    {
        $supplier->is_active = !$supplier->is_active;
        $supplier->save();

        return $supplier;
    }
}
