<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'suppliers';

    protected $fillable = [
        'code',
        'name',
        'phone',
        'email',
        'address',
        'province_id',
        'ward_id',
        'contact_person',
        'contact_phone',
        'bank_name',
        'bank_account',
        'bank_account_name',
        'opening_balance',
        'note',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'opening_balance' => 'decimal:2',
        'province_id' => 'integer',
        'ward_id' => 'integer',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id', 'code');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id', 'code');
    }

    protected static function booted(): void
    {
        static::creating(function (Supplier $supplier) {
            if (empty($supplier->code)) {
                $supplier->code = static::generateSupplierCode();
            }
        });
    }

    public static function generateSupplierCode(): string
    {
        $lastSupplier = static::withTrashed()
            ->where('code', 'like', 'NCC-%')
            ->orderByRaw("CAST(SUBSTRING(code, 5) AS UNSIGNED) DESC")
            ->first();

        if ($lastSupplier) {
            $lastNumber = (int) substr($lastSupplier->code, 4);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $code = 'NCC-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);

        while (static::withTrashed()->where('code', $code)->exists()) {
            $nextNumber++;
            $code = 'NCC-' . str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
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
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('contact_person', 'like', "%{$search}%");
        });
    }
}
