<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'customer_code',
        'business_name',
        'contact_person',
        'phone',
        'email',
        'address',
        'license_no',
        'credit_limit',
        'opening_balance',
        'is_active',
        'deleted',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
        'is_active' => 'boolean',
        'deleted' => 'boolean',
    ];
}