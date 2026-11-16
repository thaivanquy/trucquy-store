<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'categories';

    protected $fillable = [
        'parent_id',
        'code',
        'name',
        'slug',
        'description',
        'image',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name);
            }
            if (empty($category->code)) {
                $category->code = static::generateCategoryCode($category->name, $category->parent_id);
            }
        });

        static::updating(function (Category $category) {
            if ($category->isDirty('name') && empty($category->slug)) {
                $category->slug = static::generateUniqueSlug($category->name, $category->id);
            }
        });
    }

    public static function generateCategoryCode(string $name, ?int $parentId = null): string
    {
        if ($parentId) {
            $parent = static::find($parentId);
            if ($parent) {
                $parentCode = $parent->code;
                $count = static::where('parent_id', $parentId)->count();
                $counter = $count + 1;
                $code = "{$parentCode}-" . str_pad((string)$counter, 2, '0', STR_PAD_LEFT);
                while (static::where('code', $code)->exists()) {
                    $counter++;
                    $code = "{$parentCode}-" . str_pad((string)$counter, 2, '0', STR_PAD_LEFT);
                }
                return strtoupper($code);
            }
        }

        $clean = preg_replace('/[^a-zA-Z0-9\s]/', '', Str::ascii($name));
        $words = array_values(array_filter(preg_split('/\s+/', trim($clean))));

        if (empty($words)) {
            $abbr = 'CAT';
        } elseif (count($words) === 1) {
            $abbr = strtoupper(substr($words[0], 0, 4));
        } else {
            $abbr = '';
            foreach ($words as $w) {
                $abbr .= strtoupper(substr($w, 0, 1));
            }
            $abbr = substr($abbr, 0, 5);
        }

        $baseCode = "DM-{$abbr}";
        $code = $baseCode;
        $counter = 1;

        while (static::where('code', $code)->exists()) {
            $code = "{$baseCode}-" . str_pad((string)$counter, 2, '0', STR_PAD_LEFT);
            $counter++;
        }

        return strtoupper($code);
    }

    public static function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$originalSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order', 'asc');
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
