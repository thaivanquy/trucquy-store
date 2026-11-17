<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'slug',
        'image',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'category_id' => 'integer',
    ];

    protected $appends = [
        'barcode',
        'selling_price',
        'cost_price',
        'stock_quantity',
        'min_stock',
        'unit',
        'supplier',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function productUnits(): HasMany
    {
        return $this->hasMany(ProductUnit::class, 'product_id');
    }

    public function baseUnit(): HasOne
    {
        return $this->hasOne(ProductUnit::class, 'product_id')->where('is_base_unit', true);
    }

    public function productSuppliers(): HasMany
    {
        return $this->hasMany(ProductSupplier::class, 'product_id');
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'product_suppliers', 'product_id', 'supplier_id')
                    ->withPivot(['supplier_product_code', 'purchase_price', 'is_default'])
                    ->withTimestamps();
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class, 'product_id');
    }

    public function getBarcodeAttribute(): ?string
    {
        return $this->baseUnit?->barcode ?? $this->productUnits->first()?->barcode;
    }

    public function getSellingPriceAttribute(): float
    {
        return (float) ($this->baseUnit?->selling_price ?? $this->productUnits->first()?->selling_price ?? 0);
    }

    public function getCostPriceAttribute(): float
    {
        return (float) ($this->baseUnit?->cost_price ?? $this->inventory?->cost_price ?? 0);
    }

    public function getStockQuantityAttribute(): float
    {
        return (float) ($this->inventory?->quantity ?? 0);
    }

    public function getMinStockAttribute(): float
    {
        return (float) ($this->inventory?->min_stock ?? 5);
    }

    public function getUnitAttribute(): ?array
    {
        $base = $this->baseUnit?->unit ?? $this->productUnits->first()?->unit;
        if (!$base) return null;
        return [
            'id' => $base->id,
            'name' => $base->name,
            'code' => $base->code,
        ];
    }

    public function getSupplierAttribute(): ?array
    {
        $defaultPS = $this->productSuppliers->firstWhere('is_default', true) ?? $this->productSuppliers->first();
        if (!$defaultPS || !$defaultPS->supplier) return null;
        return [
            'id' => $defaultPS->supplier->id,
            'name' => $defaultPS->supplier->name,
            'code' => $defaultPS->supplier->code,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Product $product) {
            if (empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name);
            }
            if (empty($product->code)) {
                $product->code = static::generateProductCode();
            }
        });

        static::updating(function (Product $product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = static::generateUniqueSlug($product->name, $product->id);
            }
        });
    }

    public static function generateProductCode(): string
    {
        $prefix = 'SP-';
        $maxRetries = 10;
        $attempt = 0;

        do {
            $latest = static::withTrashed()
                ->where('code', 'like', "{$prefix}%")
                ->orderBy('id', 'desc')
                ->first();

            $nextNumber = 1;
            if ($latest && preg_match('/^SP-(\d+)$/', $latest->code, $matches)) {
                $nextNumber = ((int) $matches[1]) + 1 + $attempt;
            }

            $code = $prefix . str_pad((string) $nextNumber, 4, '0', STR_PAD_LEFT);
            $exists = static::withTrashed()->where('code', $code)->exists();
            $attempt++;
        } while ($exists && $attempt < $maxRetries);

        return $code;
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        if (empty($baseSlug)) {
            $baseSlug = 'san-pham-' . time();
        }

        $slug = $baseSlug;
        $counter = 1;

        while (
            static::withTrashed()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}
