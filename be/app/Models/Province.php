<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    use HasFactory;

    protected $table = 'provinces';
    protected $primaryKey = 'code';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'code',
        'name',
        'division_type',
        'codename',
        'phone_code',
    ];

    protected $casts = [
        'code' => 'integer',
        'phone_code' => 'integer',
    ];

    public function wards(): HasMany
    {
        return $this->hasMany(Ward::class, 'province_code', 'code');
    }
}
