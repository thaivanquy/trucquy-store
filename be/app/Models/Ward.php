<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ward extends Model
{
    use HasFactory;

    protected $table = 'wards';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'code',
        'province_code',
        'name',
        'division_type',
        'codename',
        'short_codename',
    ];

    protected $casts = [
        'code' => 'integer',
        'province_code' => 'integer',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }
}
