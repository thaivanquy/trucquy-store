<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'units';

    protected $fillable = [
        'code',
        'name',
        'description',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Unit $unit) {
            if (empty($unit->code)) {
                $unit->code = static::generateUnitCode();
            }
        });
    }

    public static function generateUnitCode(): string
    {
        $lastUnit = static::withTrashed()
            ->where('code', 'like', 'DVT-%')
            ->orderByRaw("CAST(SUBSTRING(code, 5) AS UNSIGNED) DESC")
            ->first();

        if ($lastUnit) {
            $lastNumber = (int) substr($lastUnit->code, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $code = 'DVT-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

        while (static::withTrashed()->where('code', $code)->exists()) {
            $nextNumber++;
            $code = 'DVT-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
        }

        return $code;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, string $search)
    {
        $search = trim($search);

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('code', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }
}
