<?php

namespace App\Models\Passport;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Passport\Token as BaseTokenModel;

class Token extends BaseTokenModel
{
    use HasFactory;

    /**
     * @var false[]
     */
    protected $attributes = [
        'revoked' => false,
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'expires_at' => 'datetime',
        'scopes' => 'array',
        'revoked' => 'bool',
    ];
}
