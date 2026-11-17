<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductUnit extends Model
{
    use HasFactory;

    protected $table = 'product_units';

    protected $fillable = [
        'product_id',
        'unit_id',
        'barcode',
        'conversion_rate',
        'is_base_unit',
        'cost_price',
        'selling_price',
        'is_direct_sale',
    ];

    protected $casts = [
        'conversion_rate' => 'float',
        'is_base_unit' => 'boolean',
        'cost_price' => 'float',
        'selling_price' => 'float',
        'is_direct_sale' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
