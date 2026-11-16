<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'banks';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id',
        'name',
        'code',
        'bin',
        'short_name',
        'logo',
        'transfer_supported',
        'lookup_supported',
        'support',
        'is_transfer',
        'swift_code',
    ];

    protected $casts = [
        'id' => 'integer',
        'transfer_supported' => 'boolean',
        'lookup_supported' => 'boolean',
        'is_transfer' => 'boolean',
        'support' => 'integer',
    ];
}
