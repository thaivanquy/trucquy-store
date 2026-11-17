<?php

namespace App\Services\Admin;

use App\Models\Inventory;
use App\Models\Product;
use App\Models\ProductSupplier;
use App\Models\ProductUnit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public function list(array $filters = []): LengthAwarePaginator|Collection
    {
        $query = Product::query()->with([
            'category:id,name,code',
            'productUnits.unit:id,name,code',
            'baseUnit.unit:id,name,code',
            'productSuppliers.supplier:id,name,code',
            'inventory',
        ]);

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('productUnits', function ($qu) use ($search) {
                      $qu->where('barcode', 'like', "%{$search}%");
                  });
            });
        }

        if (isset($filters['category_id']) && $filters['category_id'] !== '' && $filters['category_id'] !== null) {
            $query->where('category_id', $filters['category_id']);
        }

        if (isset($filters['unit_id']) && $filters['unit_id'] !== '' && $filters['unit_id'] !== null) {
            $query->whereHas('productUnits', function ($qu) use ($filters) {
                $qu->where('unit_id', $filters['unit_id']);
            });
        }

        if (isset($filters['supplier_id']) && $filters['supplier_id'] !== '' && $filters['supplier_id'] !== null) {
            $query->whereHas('productSuppliers', function ($qs) use ($filters) {
                $qs->where('supplier_id', $filters['supplier_id']);
            });
        }

        if (isset($filters['is_active']) && $filters['is_active'] !== '' && $filters['is_active'] !== null) {
            $query->where('is_active', filter_var($filters['is_active'], FILTER_VALIDATE_BOOLEAN));
        }

        if (!empty($filters['low_stock']) && filter_var($filters['low_stock'], FILTER_VALIDATE_BOOLEAN)) {
            $query->whereHas('inventory', function ($qi) {
                $qi->whereColumn('quantity', '<=', 'min_stock');
            });
        }

        if (!empty($filters['out_of_stock']) && filter_var($filters['out_of_stock'], FILTER_VALIDATE_BOOLEAN)) {
            $query->whereHas('inventory', function ($qi) {
                $qi->where('quantity', '<=', 0);
            });
        }

        $sortBy = $filters['sort_by'] ?? 'id';
        $sortDirection = strtolower($filters['sort_direction'] ?? 'desc') === 'asc' ? 'asc' : 'desc';

        if (in_array($sortBy, ['id', 'name', 'created_at', 'code'])) {
            $query->orderBy($sortBy, $sortDirection);
        } elseif ($sortBy === 'selling_price') {
            $query->leftJoin('product_units', function ($join) {
                $join->on('products.id', '=', 'product_units.product_id')
                     ->where('product_units.is_base_unit', true);
            })->select('products.*')->orderBy('product_units.selling_price', $sortDirection);
        } elseif ($sortBy === 'stock_quantity') {
            $query->leftJoin('inventories', 'products.id', '=', 'inventories.product_id')
                  ->select('products.*')->orderBy('inventories.quantity', $sortDirection);
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

    public function getStats(): array
    {
        return [
            'total' => Product::count(),
            'active_count' => Product::where('is_active', true)->count(),
            'inactive_count' => Product::where('is_active', false)->count(),
            'low_stock_count' => Product::whereHas('inventory', function ($q) {
                $q->whereColumn('quantity', '<=', 'min_stock');
            })->count(),
            'out_of_stock_count' => Product::whereHas('inventory', function ($q) {
                $q->where('quantity', '<=', 0);
            })->count(),
        ];
    }

    public function find(int $id): Product
    {
        return Product::with([
            'category:id,name,code',
            'productUnits.unit:id,name,code',
            'baseUnit.unit:id,name,code',
            'productSuppliers.supplier:id,name,code',
            'inventory',
        ])->findOrFail($id);
    }

    public function create(array $data): Product
    {
        return DB::transaction(function () use ($data) {
            $data['name'] = trim($data['name']);

            if (empty($data['code'])) {
                $data['code'] = Product::generateProductCode();
            }

            if (empty($data['slug'])) {
                $data['slug'] = Product::generateUniqueSlug($data['name']);
            }

            $units = $data['units'] ?? [];
            $suppliers = $data['suppliers'] ?? [];
            $initialStock = isset($data['stock_quantity']) ? (float) $data['stock_quantity'] : 0;
            $minStock = isset($data['min_stock']) ? (float) $data['min_stock'] : 5;
            $costPrice = isset($data['cost_price']) ? (float) $data['cost_price'] : 0;

            if (empty($units) && !empty($data['unit_id'])) {
                $units[] = [
                    'unit_id' => $data['unit_id'],
                    'barcode' => $data['barcode'] ?? null,
                    'conversion_rate' => 1,
                    'is_base_unit' => true,
                    'cost_price' => $costPrice,
                    'selling_price' => $data['selling_price'] ?? 0,
                ];
            }

            if (empty($suppliers) && !empty($data['supplier_id'])) {
                $suppliers[] = [
                    'supplier_id' => $data['supplier_id'],
                    'purchase_price' => $costPrice,
                    'is_default' => true,
                ];
            }

            $product = Product::create([
                'category_id' => $data['category_id'] ?? null,
                'code' => $data['code'],
                'name' => $data['name'],
                'slug' => $data['slug'],
                'image' => $data['image'] ?? null,
                'description' => $data['description'] ?? null,
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : true,
            ]);

            $hasBase = false;
            foreach ($units as $u) {
                if (empty($u['unit_id'])) continue;
                $isBase = !empty($u['is_base_unit']);
                if ($isBase) $hasBase = true;

                ProductUnit::create([
                    'product_id' => $product->id,
                    'unit_id' => $u['unit_id'],
                    'barcode' => !empty($u['barcode']) ? trim($u['barcode']) : null,
                    'conversion_rate' => !empty($u['conversion_rate']) ? (float) $u['conversion_rate'] : 1,
                    'is_base_unit' => $isBase,
                    'cost_price' => isset($u['cost_price']) ? (float) $u['cost_price'] : $costPrice,
                    'selling_price' => isset($u['selling_price']) ? (float) $u['selling_price'] : 0,
                    'is_direct_sale' => isset($u['is_direct_sale']) ? (bool) $u['is_direct_sale'] : true,
                ]);
            }

            if (!$hasBase && count($units) > 0) {
                $first = ProductUnit::where('product_id', $product->id)->first();
                if ($first) {
                    $first->update(['is_base_unit' => true]);
                }
            }

            foreach ($suppliers as $s) {
                if (empty($s['supplier_id'])) continue;
                ProductSupplier::create([
                    'product_id' => $product->id,
                    'supplier_id' => $s['supplier_id'],
                    'supplier_product_code' => $s['supplier_product_code'] ?? null,
                    'purchase_price' => isset($s['purchase_price']) ? (float) $s['purchase_price'] : $costPrice,
                    'is_default' => !empty($s['is_default']),
                ]);
            }

            Inventory::create([
                'product_id' => $product->id,
                'warehouse_id' => null,
                'quantity' => $initialStock,
                'min_stock' => $minStock,
                'cost_price' => $costPrice,
            ]);

            return $this->find($product->id);
        });
    }

    public function update(Product $product, array $data): Product
    {
        return DB::transaction(function () use ($product, $data) {
            if (!empty($data['name'])) {
                $data['name'] = trim($data['name']);
            }

            $product->update([
                'category_id' => array_key_exists('category_id', $data) ? $data['category_id'] : $product->category_id,
                'name' => $data['name'] ?? $product->name,
                'code' => $data['code'] ?? $product->code,
                'image' => array_key_exists('image', $data) ? $data['image'] : $product->image,
                'description' => array_key_exists('description', $data) ? $data['description'] : $product->description,
                'is_active' => isset($data['is_active']) ? (bool) $data['is_active'] : $product->is_active,
            ]);

            $costPrice = isset($data['cost_price']) ? (float) $data['cost_price'] : null;

            if (isset($data['units']) && is_array($data['units'])) {
                ProductUnit::where('product_id', $product->id)->delete();
                $hasBase = false;
                foreach ($data['units'] as $u) {
                    if (empty($u['unit_id'])) continue;
                    $isBase = !empty($u['is_base_unit']);
                    if ($isBase) $hasBase = true;

                    ProductUnit::create([
                        'product_id' => $product->id,
                        'unit_id' => $u['unit_id'],
                        'barcode' => !empty($u['barcode']) ? trim($u['barcode']) : null,
                        'conversion_rate' => !empty($u['conversion_rate']) ? (float) $u['conversion_rate'] : 1,
                        'is_base_unit' => $isBase,
                        'cost_price' => isset($u['cost_price']) ? (float) $u['cost_price'] : 0,
                        'selling_price' => isset($u['selling_price']) ? (float) $u['selling_price'] : 0,
                        'is_direct_sale' => isset($u['is_direct_sale']) ? (bool) $u['is_direct_sale'] : true,
                    ]);
                }
                if (!$hasBase) {
                    $first = ProductUnit::where('product_id', $product->id)->first();
                    if ($first) $first->update(['is_base_unit' => true]);
                }
            } elseif (isset($data['unit_id']) || isset($data['selling_price'])) {

                $baseUnit = $product->baseUnit ?? ProductUnit::where('product_id', $product->id)->first();
                if ($baseUnit) {
                    $baseUnit->update([
                        'unit_id' => $data['unit_id'] ?? $baseUnit->unit_id,
                        'barcode' => array_key_exists('barcode', $data) ? $data['barcode'] : $baseUnit->barcode,
                        'selling_price' => isset($data['selling_price']) ? (float) $data['selling_price'] : $baseUnit->selling_price,
                        'cost_price' => $costPrice !== null ? $costPrice : $baseUnit->cost_price,
                    ]);
                } else if (!empty($data['unit_id'])) {
                    ProductUnit::create([
                        'product_id' => $product->id,
                        'unit_id' => $data['unit_id'],
                        'barcode' => $data['barcode'] ?? null,
                        'conversion_rate' => 1,
                        'is_base_unit' => true,
                        'cost_price' => $costPrice ?? 0,
                        'selling_price' => $data['selling_price'] ?? 0,
                    ]);
                }
            }

            if (isset($data['suppliers']) && is_array($data['suppliers'])) {
                ProductSupplier::where('product_id', $product->id)->delete();
                foreach ($data['suppliers'] as $s) {
                    if (empty($s['supplier_id'])) continue;
                    ProductSupplier::create([
                        'product_id' => $product->id,
                        'supplier_id' => $s['supplier_id'],
                        'supplier_product_code' => $s['supplier_product_code'] ?? null,
                        'purchase_price' => isset($s['purchase_price']) ? (float) $s['purchase_price'] : 0,
                        'is_default' => !empty($s['is_default']),
                    ]);
                }
            } elseif (array_key_exists('supplier_id', $data)) {
                ProductSupplier::where('product_id', $product->id)->delete();
                if (!empty($data['supplier_id'])) {
                    ProductSupplier::create([
                        'product_id' => $product->id,
                        'supplier_id' => $data['supplier_id'],
                        'purchase_price' => $costPrice ?? 0,
                        'is_default' => true,
                    ]);
                }
            }

            $inv = $product->inventory ?? Inventory::firstOrNew(['product_id' => $product->id]);
            if (isset($data['stock_quantity'])) {
                $inv->quantity = (float) $data['stock_quantity'];
            }
            if (isset($data['min_stock'])) {
                $inv->min_stock = (float) $data['min_stock'];
            }
            if ($costPrice !== null) {
                $inv->cost_price = $costPrice;
            }
            $inv->save();

            return $this->find($product->id);
        });
    }

    public function delete(Product $product): bool
    {
        return (bool) $product->delete();
    }

    public function toggleStatus(Product $product): Product
    {
        $product->is_active = !$product->is_active;
        $product->save();

        return $this->find($product->id);
    }
}
